<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки конкретного разрешения (permission).
 * 
 * Использование в маршрутах:
 * Route::middleware(['permission:products.create'])->group(...)
 * Route::middleware(['permission:finance.view,finance.edit'])->group(...)
 */
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
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

        // Если разрешения не переданы — пропускаем
        if (empty($permissions)) {
            return $next($request);
        }

        // Проверяем, есть ли у пользователя хотя бы одно из указанных разрешений
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        // Если ни одно разрешение не подошло
        abort(403, 'Доступ запрещён. У вас нет прав для выполнения этого действия.');
    }
}
