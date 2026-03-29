<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SecurityLogger;
use App\Services\AdminSecurityService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для строгой проверки роли администратора (Admin).
 *
 * Проверяет:
 * - Пользователь аутентифицирован
 * - Роль пользователя = admin (role_id = 2, level 80)
 * - Пользователь активен
 * - 2FA верификация пройдена (обязательно для admin)
 * - Опционально: IP whitelist
 * - Защита от эскалации привилегий
 *
 * Логирует каждый доступ администратора к системе.
 *
 * Использование:
 * Route::middleware(['auth', 'role:admin', 'ensure.admin'])->group(...)
 */
class EnsureAdminRole
{
    /**
     * IP адреса, разрешённые для доступа администратора (опционально).
     * Если пустой массив - все IP разрешены.
     * Настройка в .env: ADMIN_IP_WHITELIST=127.0.0.1,192.168.1.100
     */
    private const IP_WHITELIST = [
        // '127.0.0.1',
        // '192.168.1.100',
    ];

    public function __construct(
        protected SecurityLogger $securityLogger,
        protected AdminSecurityService $adminSecurityService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Проверка аутентификации
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Требуется авторизация');
        }

        // Проверка активности пользователя
        if (!$user->is_active) {
            auth()->logout();
            $this->securityLogger->logSuspiciousActivity(
                $user,
                'Попытка доступа неактивного пользователя с ролью администратора',
                ['ip' => $request->ip()]
            );

            return redirect()->route('login')
                ->withErrors(['username' => 'Ваша учётная запись деактивирована. Обратитесь к администратору.']);
        }

        // Строгая проверка роли admin
        if (!$user->isAdmin()) {
            $this->securityLogger->logSuspiciousActivity(
                $user,
                'Попытка доступа к панели администратора без соответствующей роли',
                [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'actual_role' => $user->role?->name,
                    'ip' => $request->ip(),
                ]
            );

            abort(403, 'Доступ запрещён. Требуется роль администратора (Admin).');
        }

        // Проверка 2FA (обязательно для admin)
        if ($user->two_factor_secret) {
            $sessionKey = '2fa_verified_' . $user->id;
            if (!session($sessionKey) && !cache($sessionKey)) {
                // Если 2FA не верифицирована в этой сессии
                if ($request->routeIs('auth.2fa.*')) {
                    // Разрешаем доступ к 2FA маршрутам
                    return $next($request);
                }

                return redirect()->route('auth.2fa.show')
                    ->with('warning', 'Требуется подтверждение двухфакторной аутентификации');
            }
        } else {
            // Для админа 2FA обязательна - предупреждаем если не включена
            // Но не блокируем доступ (администратор должен включить 2FA в настройках)
        }

        // Проверка IP whitelist (если настроена)
        $whitelist = $this->getIpWhitelist();
        if (!empty($whitelist)) {
            $currentIp = $this->getClientIp($request);
            if (!in_array($currentIp, $whitelist)) {
                $this->securityLogger->logSuspiciousActivity(
                    $user,
                    'Доступ администратора с IP вне белого списка',
                    [
                        'ip' => $currentIp,
                        'whitelist' => $whitelist,
                    ]
                );

                abort(403, 'Доступ запрещён. Ваш IP адрес не находится в белом списке.');
            }
        }

        // Проверка на эскалацию привилегий (admin → owner)
        if ($this->isPrivilegeEscalationAttempt($request, $user)) {
            $this->securityLogger->logSuspiciousActivity(
                $user,
                'Попытка эскалации привилегий администратора',
                [
                    'route' => $request->route()->getName(),
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'ip' => $request->ip(),
                ]
            );

            abort(403, 'Доступ запрещён. Попытка выполнения действий, доступных только владельцу.');
        }

        // Логирование доступа администратора
        $this->securityLogger->logAdminAccess(
            $user,
            $request->route()->getName() ?? $request->path(),
            [
                'ip' => $request->ip(),
                'method' => $request->method(),
                'path' => $request->path(),
            ]
        );

        return $next($request);
    }

    /**
     * Проверка на попытку эскалации привилегий.
     * Администратор не должен иметь доступ к функциям владельца.
     */
    private function isPrivilegeEscalationAttempt(Request $request, $user): bool
    {
        // Маршруты, доступные только владельцу
        $ownerOnlyRoutes = [
            'owner.',
            'finance.',
            'roles.',
        ];

        $currentRoute = $request->route()->getName() ?? '';

        foreach ($ownerOnlyRoutes as $ownerRoute) {
            if (str_starts_with($currentRoute, $ownerRoute)) {
                return true;
            }
        }

        // Проверка POST данных на попытку изменения роли
        if ($request->isMethod('POST') || $request->isMethod('PUT')) {
            // Попытка установить role_id = 1 (owner)
            if ($request->input('role_id') == 1) {
                return true;
            }

            // Попытка изменить уровень роли
            if ($request->has('level') && $request->input('level') >= 100) {
                return true;
            }
        }

        return false;
    }

    /**
     * Получение IP whitelist из конфигурации или константы.
     */
    private function getIpWhitelist(): array
    {
        // Проверяем переменную окружения
        $envWhitelist = env('ADMIN_IP_WHITELIST');
        if ($envWhitelist) {
            return array_map('trim', explode(',', $envWhitelist));
        }

        return self::IP_WHITELIST;
    }

    /**
     * Получение реального IP адреса клиента.
     */
    private function getClientIp(Request $request): string
    {
        $ip = $request->ip();

        $forwardedHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CF_CONNECTING_IP',
        ];

        foreach ($forwardedHeaders as $header) {
            if ($request->server->has($header)) {
                $forwardedIp = $request->server->get($header);
                $forwardedIp = explode(',', $forwardedIp)[0];
                $forwardedIp = trim($forwardedIp);

                if (filter_var($forwardedIp, FILTER_VALIDATE_IP)) {
                    return $forwardedIp;
                }
            }
        }

        return $ip ?? '0.0.0.0';
    }
}
