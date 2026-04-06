<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Блокировка встраивания в iframe (защита от Clickjacking)
        $response->headers->set('X-Frame-Options', 'DENY');

        // Блокировка определения типа контента (защита от MIME-sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Включение фильтра XSS в браузере
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Управление передачей Referrer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Ограничение разрешенных ресурсов (Content Security Policy)
        // Настраиваем базовую политику, разрешающую скрипты с доверенных источников
        $ngrokDomain = $request->getHost();
        
        // Не применяем CSP на главной странице (нужен для Яндекс/Google Карт)
        if ($request->path() !== '/' && $request->path() !== '') {
            $csp = "default-src 'self'; ";
            $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://unpkg.com https://api-maps.yandex.ru; ";
            $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://api-maps.yandex.ru; ";
            $csp .= "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com https://api-maps.yandex.ru; ";
            $csp .= "img-src 'self' data: https: blob: https://api.qrserver.com https://images.unsplash.com https://{$ngrokDomain} http://{$ngrokDomain} https://core-renderer-tiles.maps.yandex.net https://core-tiles.maps.yandex.net https://vec01.maps.yandex.net https://vec02.maps.yandex.net; ";
            $csp .= "connect-src 'self' https://api-maps.yandex.ru https://geocode-maps.yandex.ru https: http://{$ngrokDomain}; ";
            $csp .= "frame-ancestors 'none';";
            
            $response->headers->set('Content-Security-Policy', $csp);
        }

        // Строгая транспортная безопасность (HSTS) - включать только если есть SSL
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
