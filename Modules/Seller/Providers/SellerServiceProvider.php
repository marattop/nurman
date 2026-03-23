<?php

namespace Modules\Seller\Providers;

use Illuminate\Support\ServiceProvider;

class SellerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'seller');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    public function register(): void
    {
        //
    }
}
