<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AdminSecurityService;
use Illuminate\Support\Facades\Auth;

class AdminSecurityMiddleware
{
    public function __construct(protected AdminSecurityService $securityService) {}

    /**
     * Защитная стена для админ-панели и панели владельца.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // 1. Пропускаем клиентов-покупателей (им не нужна такая жесткая защита)
        if ($user && $user->role && $user->role->name === 'client') {
            return $next($request);
        }

        // 2. ЖЕСТКАЯ ПРОВЕРКА IP и VPN для персонала
        $ipCheck = $this->securityService->validateRequest($request->ip());
        if (!$ipCheck['valid']) {
            Auth::logout();
            $request->session()->invalidate();
            
            $msg = $ipCheck['reason'] === 'foreign_ip' 
                ? "Доступ заблокирован: ваша страна ({$ipCheck['country']}) не имеет доступа. Разрешен только Кыргызстан."
                : "Доступ заблокирован: обнаружен VPN или Прокси. Отключите его для входа.";

            return redirect()->route('login')->withErrors(['username' => $msg]);
        }

        // 3. ПРОВЕРКА ВТОРОГО ЭТАПА (Ключ года "2026")
        // Если пользователь авторизован, но в сессии нет метки о проверке ключа
        if (Auth::check() && !$request->session()->has('auth_verified')) {
            // Если мы УЖЕ на странице проверки или пытаемся проверить ключ - пропускаем
            if ($request->is('login/verify*') || $request->routeIs('login.verify*') || $request->routeIs('logout')) {
                return $next($request);
            }

            // Ставим шаг 1 в true, так как пароль уже введен
            $request->session()->put('auth_step_1', true);
            return redirect()->route('login.verify');
        }

        return $next($request);
    }
}
