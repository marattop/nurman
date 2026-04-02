<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TwoFactorService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки 2FA.
 * Применяется ТОЛЬКО к администраторам и складским работникам.
 */
class RequireTwoFactorAuthentication
{
    public function __construct(
        protected TwoFactorService $twoFactorService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 2FA применяется ТОЛЬКО к админам и складским работникам (role_id 2, 3, 4)
        // Клиенты (role_id 5) и владельцы (role_id 1) не проходят 2FA
        $requires2FA = in_array($user->role_id, [2, 3, 4]);
        
        if (!$requires2FA) {
            return $next($request);
        }

        // Если 2FA не включен - пропускаем
        if (!$user->two_factor_secret) {
            return $next($request);
        }

        // Проверяем верификацию через сессию или кэш
        $sessionKey = '2fa_verified_' . $user->id;
        $isVerified = session($sessionKey) || cache($sessionKey);

        if (!$isVerified) {
            // Если запрос AJAX - возвращаем JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Требуется 2FA верификация',
                    'requires_2fa' => true,
                ], 403);
            }

            // Разрешаем доступ к 2FA маршрутам
            if ($request->routeIs('auth.2fa.*')) {
                return $next($request);
            }

            // Редирект на страницу 2FA
            return redirect()->route('auth.2fa.show');
        }

        return $next($request);
    }
}
