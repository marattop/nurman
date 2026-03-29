<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для защиты от Brute-force атак.
 * 
 * Блокирует IP после 5 неудачных попыток входа на 15 минут.
 * Логирует все попытки входа.
 * 
 * Использование:
 * Route::post('login', [AuthController::class, 'login'])->middleware('rate.limit.auth');
 */
class RateLimitAuth
{
    /**
     * Максимальное количество попыток входа.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Время блокировки в секундах (15 минут).
     */
    private const BLOCK_DURATION = 900;

    /**
     * Время жизни счётчика попыток (1 час).
     */
    private const ATTEMPT_TTL = 3600;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Применяем только к POST запросам логина
        if (!$this->isLoginRequest($request)) {
            return $next($request);
        }

        $ipAddress = $this->getClientIp($request);
        $username = $request->input('username', 'unknown');
        $cacheKey = $this->getCacheKey($ipAddress);
        $blockKey = $this->getBlockKey($ipAddress);

        // Проверяем, заблокирован ли IP
        if (Cache::has($blockKey)) {
            $remainingTime = Cache::get($blockKey);
            
            $this->logAttempt($ipAddress, $username, 'blocked', "IP заблокирован. Осталось секунд: {$remainingTime}");

            return response()->json([
                'success' => false,
                'message' => 'Слишком много неудачных попыток входа. Попробуйте позже.',
                'retry_after' => $remainingTime,
            ], 429);
        }

        // Получаем количество попыток
        $attempts = (int) Cache::get($cacheKey, 0);

        // Если превышен лимит попыток - блокируем
        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::put($blockKey, self::BLOCK_DURATION, self::BLOCK_DURATION);
            Cache::forget($cacheKey);

            $this->logAttempt($ipAddress, $username, 'blocked', 'Превышен лимит попыток входа. IP заблокирован на 15 минут.');

            return response()->json([
                'success' => false,
                'message' => 'Слишком много неудачных попыток входа. Ваш IP заблокирован на 15 минут.',
                'retry_after' => self::BLOCK_DURATION,
            ], 429);
        }

        // Сохраняем запрос для последующего анализа в случае неудачи
        $request->attributes->set('auth_attempt_key', $cacheKey);
        $request->attributes->set('auth_ip', $ipAddress);
        $request->attributes->set('auth_username', $username);

        return $next($request);
    }

    /**
     * Инкремент счётчика неудачных попыток.
     * Вызывается из контроллера при неудачной попытке входа.
     */
    public static function incrementAttempts(Request $request): void
    {
        $cacheKey = $request->attributes->get('auth_attempt_key');
        $ipAddress = $request->attributes->get('auth_ip');
        $username = $request->attributes->get('auth_username', 'unknown');

        if ($cacheKey) {
            $attempts = Cache::increment($cacheKey) ?? 1;
            Cache::put($cacheKey, $attempts, self::ATTEMPT_TTL);

            $remainingAttempts = self::MAX_ATTEMPTS - $attempts;
            
            Log::channel('security')->warning('Неудачная попытка входа', [
                'ip' => $ipAddress,
                'username' => $username,
                'attempts' => $attempts,
                'remaining_attempts' => max(0, $remainingAttempts),
            ]);
        }
    }

    /**
     * Сброс счётчика попыток при успешном входе.
     */
    public static function resetAttempts(Request $request): void
    {
        $cacheKey = $request->attributes->get('auth_attempt_key');
        $ipAddress = $request->attributes->get('auth_ip');

        if ($cacheKey) {
            Cache::forget($cacheKey);
        }

        // Также сбрасываем блокировку если есть
        $blockKey = self::getBlockKey($ipAddress);
        Cache::forget($blockKey);
    }

    /**
     * Проверка, является ли запрос запросом входа.
     */
    private function isLoginRequest(Request $request): bool
    {
        return $request->isMethod('POST') 
            && ($request->routeIs('login') || $request->routeIs('login.post'));
    }

    /**
     * Получение IP адреса клиента с учётом прокси.
     */
    private function getClientIp(Request $request): string
    {
        $ip = $request->ip();
        
        // Проверка заголовков прокси
        $forwardedHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CF_CONNECTING_IP', // Cloudflare
        ];

        foreach ($forwardedHeaders as $header) {
            if ($request->server->has($header)) {
                $forwardedIp = $request->server->get($header);
                // Берём первый IP из списка (если их несколько)
                $forwardedIp = explode(',', $forwardedIp)[0];
                $forwardedIp = trim($forwardedIp);
                
                if (filter_var($forwardedIp, FILTER_VALIDATE_IP)) {
                    $ip = $forwardedIp;
                    break;
                }
            }
        }

        return $ip ?? '0.0.0.0';
    }

    /**
     * Генерация ключа для кэша попыток.
     */
    private function getCacheKey(string $ipAddress): string
    {
        return 'auth:attempts:' . $ipAddress;
    }

    /**
     * Генерация ключа для кэша блокировки.
     */
    private function getBlockKey(string $ipAddress): string
    {
        return 'auth:blocked:' . $ipAddress;
    }

    /**
     * Логирование попытки входа.
     */
    private function logAttempt(string $ipAddress, string $username, string $status, string $message): void
    {
        Log::channel('security')->info('Попытка входа', [
            'ip' => $ipAddress,
            'username' => $username,
            'status' => $status,
            'message' => $message,
            'user_agent' => request()->userAgent(),
        ]);
    }
}
