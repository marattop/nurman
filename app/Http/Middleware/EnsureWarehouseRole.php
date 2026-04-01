<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SecurityLogger;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для строгой проверки роли кладовщика (Warehouse).
 *
 * Проверяет:
 * - Пользователь аутентифицирован
 * - Роль пользователя = warehouse (role_id = 4, level 20)
 * - Пользователь активен
 *
 * Использование:
 * Route::middleware(['auth', 'role:warehouse', 'ensure.warehouse'])->group(...)
 */
class EnsureWarehouseRole
{
    public function __construct(
        protected SecurityLogger $securityLogger
    ) {}

    /**
     * Handle an incoming request.
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
                'Попытка доступа неактивного пользователя с ролью кладовщика',
                ['ip' => $request->ip()]
            );

            return redirect()->route('login')
                ->withErrors(['username' => 'Ваша учётная запись деактивирована. Обратитесь к администратору.']);
        }

        // Строгая проверка роли warehouse
        if (!$user->isWarehouse()) {
            $this->securityLogger->logSuspiciousActivity(
                $user,
                'Попытка доступа к панели кладовщика без соответствующей роли',
                [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'actual_role' => $user->role?->name,
                    'ip' => $request->ip(),
                ]
            );

            abort(403, 'Доступ запрещён. Требуется роль кладовщика (Warehouse).');
        }

        return $next($request);
    }
}
