<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки роли ИЛИ разрешения.
 * 
 * Использование в маршрутах:
 * Route::middleware(['role_or_permission:admin,finance.view'])->group(...)
 */
class RoleOrPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$params
     */
    public function handle(Request $request, Closure $next, string ...$params): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['username' => 'Ваша учётная запись деактивирована.']);
        }

        if (empty($params)) {
            return $next($request);
        }

        // Проверяем каждую сущность — это может быть роль или разрешение
        foreach ($params as $param) {
            // Проверяем как роль
            if ($user->hasRole($param)) {
                return $next($request);
            }
            // Проверяем как разрешение
            if ($user->can($param)) {
                return $next($request);
            }
        }

        abort(403, 'Доступ запрещён. Недостаточно прав.');
    }
}
