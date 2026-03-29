<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки активности сессии.
 * 
 * Продлевает сессию при активности пользователя.
 * Принудительно завершает сессию при бездействии.
 * 
 * Использование:
 * Route::middleware(['auth', 'session.activity'])->group(...)
 */
class SessionActivity
{
    /**
     * Максимальное время бездействия в секундах (30 минут).
     */
    private const MAX_IDLE_TIME = 1800;

    /**
     * Ключ для хранения времени последней активности.
     */
    private function getSessionActivityKey(int $userId): string
    {
        return 'session:activity:' . $userId;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $activityKey = $this->getSessionActivityKey($user->id);
        $lastActivity = Cache::get($activityKey);
        $currentTime = time();

        // Проверяем время бездействия
        if ($lastActivity !== null) {
            $idleTime = $currentTime - $lastActivity;

            if ($idleTime > self::MAX_IDLE_TIME) {
                // Сессия неактивна слишком долго - завершаем
                Cache::forget($activityKey);
                
                Auth::logout();
                
                return redirect()->route('login')
                    ->with('warning', 'Сессия завершена из-за длительного бездействия.');
            }
        }

        // Продлеваем сессию
        Cache::put($activityKey, $currentTime, self::MAX_IDLE_TIME);

        // Обновляем время последней активности в БД
        $user->update(['last_login_at' => now()]);

        return $next($request);
    }
}
