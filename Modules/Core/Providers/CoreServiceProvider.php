<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Загрузка любых пакетных сервисов.
     */
    public function boot(): void
    {
        $this->registerViews();
        $this->loadMigrations();
        $this->loadRoutes();
        $this->registerComposers();
        $this->registerMiddleware();
    }

    /**
     * Регистрация представлений.
     */
    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'core');
    }

    /**
     * Регистрация миграций.
     */
    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Регистрация маршрутов.
     */
    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    /**
     * Регистрация view композеров.
     */
    private function registerComposers(): void
    {
        //
    }

    /**
     * Регистрация middleware.
     */
    private function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('role', \Modules\Core\Http\Middleware\CheckRole::class);
    }

    /**
     * Регистрация сервисов.
     */
    public function register(): void
    {
        //
    }
}
