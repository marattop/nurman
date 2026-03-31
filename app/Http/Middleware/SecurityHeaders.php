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
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://unpkg.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; ";
        $csp .= "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com; ";
        $csp .= "img-src 'self' data: https://api.qrserver.com https://images.unsplash.com; ";
        $csp .= "connect-src 'self'; ";
        $csp .= "frame-ancestors 'none';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        // Строгая транспортная безопасность (HSTS) - включать только если есть SSL
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
