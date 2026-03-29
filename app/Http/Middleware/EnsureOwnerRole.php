<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SecurityLogger;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для строгой проверки роли владельца (Owner).
 * 
 * Проверяет:
 * - Пользователь аутентифицирован
 * - Роль пользователя = owner (role_id = 1)
 * - Пользователь активен
 * - 2FA верификация пройдена (если включена)
 * - Опционально: IP whitelist
 * 
 * Логирует каждый доступ владельца к системе.
 * 
 * Использование:
 * Route::middleware(['auth', 'role:owner', 'ensure.owner'])->group(...)
 */
class EnsureOwnerRole
{
    /**
     * IP адреса, разрешённые для доступа владельца (опционально).
     * Если пустой массив - все IP разрешены.
     */
    private const IP_WHITELIST = [
        // '127.0.0.1',
        // '192.168.1.100',
    ];

    public function __construct(
        protected SecurityLogger $securityLogger
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
                'Попытка доступа неактивного пользователя с ролью владельца',
                ['ip' => $request->ip()]
            );
            
            return redirect()->route('login')
                ->withErrors(['username' => 'Ваша учётная запись деактивирована. Обратитесь к администратору.']);
        }

        // Строгая проверка роли owner
        if (!$user->isOwner()) {
            $this->securityLogger->logSuspiciousActivity(
                $user,
                'Попытка доступа к панели владельца без соответствующей роли',
                [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'actual_role' => $user->role?->name,
                    'ip' => $request->ip(),
                ]
            );

            abort(403, 'Доступ запрещён. Требуется роль владельца (Owner).');
        }

        // Проверка 2FA (если включена)
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
        }

        // Проверка IP whitelist (если настроена)
        if (!empty(self::IP_WHITELIST)) {
            $currentIp = $this->getClientIp($request);
            if (!in_array($currentIp, self::IP_WHITELIST)) {
                $this->securityLogger->logSuspiciousActivity(
                    $user,
                    'Доступ владельца с IP вне белого списка',
                    [
                        'ip' => $currentIp,
                        'whitelist' => self::IP_WHITELIST,
                    ]
                );

                abort(403, 'Доступ запрещён. Ваш IP адрес не находится в белом списке.');
            }
        }

        // Логирование доступа владельца
        $this->securityLogger->logOwnerAccess(
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
