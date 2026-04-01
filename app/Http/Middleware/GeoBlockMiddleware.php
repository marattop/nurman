<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SecurityLogger;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для блокировки VPN и других стран.
 * 
 * Проверяет:
 * - IP адрес не из VPN/Proxy
 * - Страна доступа (если настроено)
 * - Блокировка подозрительных IP
 */
class GeoBlockMiddleware
{
    /**
     * Разрешённые страны (ISO коды).
     * Пустой массив = все страны разрешены.
     * 
     * Пример: ['KG', 'RU', 'KZ'] - только Кыргызстан, Россия, Казахстан
     */
    private const ALLOWED_COUNTRIES = [
        // 'KG', // Кыргызстан
        // 'RU', // Россия
        // 'KZ', // Казахстан
    ];

    /**
     * Заблокированные страны.
     */
    private const BLOCKED_COUNTRIES = [
        // 'US', // США
        // 'CN', // Китай
        // 'IR', // Иран
        // 'KP', // Северная Корея
    ];

    public function __construct(
        protected SecurityLogger $securityLogger
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $ip = $this->getClientIp($request);

        // Проверка на VPN/Proxy (через API)
        if ($this->isVpnOrProxy($ip)) {
            if ($user) {
                $this->securityLogger->logSuspiciousActivity(
                    $user,
                    'Попытка входа через VPN/Proxy',
                    [
                        'ip' => $ip,
                        'reason' => 'vpn_detected',
                    ]
                );
            }

            abort(403, 'Доступ через VPN/Proxy запрещён. Отключите VPN для входа.');
        }

        // Проверка страны (если настроено)
        if (!empty(self::ALLOWED_COUNTRIES) || !empty(self::BLOCKED_COUNTRIES)) {
            $country = $this->getCountryByIp($ip);

            // Проверка заблокированных стран
            if (!empty(self::BLOCKED_COUNTRIES) && in_array($country, self::BLOCKED_COUNTRIES)) {
                if ($user) {
                    $this->securityLogger->logSuspiciousActivity(
                        $user,
                        'Доступ из заблокированной страны',
                        [
                            'ip' => $ip,
                            'country' => $country,
                            'reason' => 'blocked_country',
                        ]
                    );
                }

                abort(403, 'Доступ из вашей страны запрещён.');
            }

            // Проверка разрешённых стран
            if (!empty(self::ALLOWED_COUNTRIES) && !in_array($country, self::ALLOWED_COUNTRIES)) {
                if ($user) {
                    $this->securityLogger->logSuspiciousActivity(
                        $user,
                        'Доступ из неразрешённой страны',
                        [
                            'ip' => $ip,
                            'country' => $country,
                            'reason' => 'country_not_allowed',
                        ]
                    );
                }

                abort(403, 'Доступ из вашей страны ограничен. Разрешённые страны: ' . implode(', ', self::ALLOWED_COUNTRIES));
            }
        }

        return $next($request);
    }

    /**
     * Проверка на VPN/Proxy через бесплатные API.
     */
    private function isVpnOrProxy(string $ip): bool
    {
        // Проверяем через ipapi.co (бесплатно, 1000 запросов/день)
        try {
            $response = file_get_contents("https://ipapi.co/{$ip}/json/");
            $data = json_decode($response, true);

            if ($data && isset($data['is_tor']) && $data['is_tor']) {
                return true;
            }

            // Проверяем через ip-api.com (бесплатно для некоммерческого использования)
            $response = file_get_contents("http://ip-api.com/json/{$ip}?fields=proxy,hosting");
            $data = json_decode($response, true);

            if ($data && ($data['proxy'] || $data['hosting'])) {
                return true;
            }
        } catch (\Exception $e) {
            // Если API недоступен, пропускаем проверку
            \Log::warning('VPN check API unavailable', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Получение страны по IP.
     */
    private function getCountryByIp(string $ip): ?string
    {
        try {
            $response = file_get_contents("https://ipapi.co/{$ip}/country/");
            return trim($response);
        } catch (\Exception $e) {
            \Log::warning('Country lookup failed', ['error' => $e->getMessage()]);
            return null;
        }
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
