<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TwoFactorService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки 2FA.
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

        // Если 2FA не включен - пропускаем
        if (!$user->two_factor_secret) {
            return $next($request);
        }

        // Проверяем верификацию
        if ($this->twoFactorService->needsTwoFactorVerification($user)) {
            // Если запрос AJAX - возвращаем JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Требуется 2FA верификация',
                    'requires_2fa' => true,
                ], 403);
            }

            // Редирект на страницу 2FA
            return redirect()->route('auth.2fa');
        }

        return $next($request);
    }
}
