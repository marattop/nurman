<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            base_path('Modules/Owner/routes/web.php'),
            base_path('Modules/Warehouse/routes/web.php'),
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\SetAppUrl::class);

        // Исключения для CSRF токена
        $middleware->validateCsrfTokens(except: [
            'warehouse/qrcodes/scan/process',
        ]);

        // Регистрация алиасов для RBAC middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \App\Http\Middleware\RoleOrPermissionMiddleware::class,

            // Security middleware
            'rate.limit.auth' => \App\Http\Middleware\RateLimitAuth::class,
            'ensure.owner' => \App\Http\Middleware\EnsureOwnerRole::class,
            'ensure.admin' => \App\Http\Middleware\EnsureAdminRole::class,
            'ensure.warehouse' => \App\Http\Middleware\EnsureWarehouseRole::class,
            'geo.block' => \App\Http\Middleware\GeoBlockMiddleware::class,
            'session.activity' => \App\Http\Middleware\SessionActivity::class,
            '2fa.verified' => \App\Http\Middleware\RequireTwoFactorAuthentication::class,
            'admin.security' => \App\Http\Middleware\AdminSecurityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
