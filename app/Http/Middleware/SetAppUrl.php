<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetAppUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Установить правильный URL для ngrok
        if ($request->getHost() !== 'localhost' && $request->getHost() !== '127.0.0.1') {
            $scheme = $request->getScheme();
            $host = $request->getHost();
            $port = $request->getPort();
            
            $url = $scheme . '://' . $host;
            if ($port && !in_array($port, [80, 443])) {
                $url .= ':' . $port;
            }
            
            config(['app.url' => $url]);
            URL::forceRootUrl($url);
        }
        
        return $next($request);
    }
}
