<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки роли пользователя.
 * 
 * Использование в маршрутах:
 * Route::middleware(['role:owner'])->group(...)
 * Route::middleware(['role:admin,seller'])->group(...)
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Если пользователь не аутентифицирован
        if (!$user) {
            return redirect()->route('login');
        }

        // Если пользователь не активен
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['username' => 'Ваша учётная запись деактивирована. Обратитесь к администратору.']);
        }

        // Если роли не переданы — пропускаем (только аутентификация)
        if (empty($roles)) {
            return $next($request);
        }

        // Проверяем, есть ли у пользователя хотя бы одна из указанных ролей
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Если ни одна роль не подошла
        abort(403, 'Доступ запрещён. Недостаточно прав для выполнения этого действия.');
    }
}
