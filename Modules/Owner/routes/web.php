<?php

use Illuminate\Support\Facades\Route;
use Modules\Owner\Http\Controllers\DashboardController;
use Modules\Owner\Http\Controllers\AnalyticsController;
use Modules\Owner\Http\Controllers\CustomersController;
use Modules\Owner\Http\Controllers\ProductsController;
use Modules\Owner\Http\Controllers\ShopsController;
use Modules\Owner\Http\Controllers\UsersController;
use Modules\Owner\Http\Controllers\CouponsController;
use Modules\Owner\Http\Controllers\ExpensesController;
use Modules\Owner\Http\Controllers\ReportsController;
use Modules\Owner\Http\Controllers\BackupController;

/*
|--------------------------------------------------------------------------
| Web Routes for Owner Module
|--------------------------------------------------------------------------
| Примечание: Эти маршруты используют префикс 'owner.module.' чтобы
| избежать конфликтов с основными маршрутами в routes/web.php
*/

Route::middleware(['auth', 'admin.security'])->prefix('owner/module')->name('owner.module.')->group(function () {

    // Дашборд владельца
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Аналитика
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('analytics/api', [AnalyticsController::class, 'api'])->name('analytics.api');

    // Клиенты
    Route::get('customers', [CustomersController::class, 'index'])->name('customers');
    Route::get('customers/{customer}', [CustomersController::class, 'show'])->name('customers.show');
    Route::post('customers/{customer}/update', [CustomersController::class, 'update'])->name('customers.update');

    // Товары
    Route::get('products', [ProductsController::class, 'index'])->name('products');
    Route::get('products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('products/{product}', [ProductsController::class, 'show'])->name('products.show');
    Route::get('products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');

    // Магазины
    Route::get('shops', [ShopsController::class, 'index'])->name('shops');
    Route::get('shops/create', [ShopsController::class, 'create'])->name('shops.create');
    Route::post('shops', [ShopsController::class, 'store'])->name('shops.store');
    Route::get('shops/{shop}/edit', [ShopsController::class, 'edit'])->name('shops.edit');
    Route::put('shops/{shop}', [ShopsController::class, 'update'])->name('shops.update');
    Route::delete('shops/{shop}', [ShopsController::class, 'destroy'])->name('shops.destroy');

    // Сотрудники
    Route::get('users', [UsersController::class, 'index'])->name('users');
    Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('users', [UsersController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

    // Купоны
    Route::get('coupons', [CouponsController::class, 'index'])->name('coupons');
    Route::get('coupons/create', [CouponsController::class, 'create'])->name('coupons.create');
    Route::post('coupons', [CouponsController::class, 'store'])->name('coupons.store');
    Route::delete('coupons/{coupon}', [CouponsController::class, 'destroy'])->name('coupons.destroy');

    // Расходы
    Route::get('expenses', [ExpensesController::class, 'index'])->name('expenses');
    Route::get('expenses/create', [ExpensesController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [ExpensesController::class, 'store'])->name('expenses.store');

    // Отчёты
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
    Route::get('reports/purchases', [ReportsController::class, 'purchases'])->name('reports.purchases');
    Route::get('reports/expenses', [ReportsController::class, 'expenses'])->name('reports.expenses');
    Route::get('reports/profit', [ReportsController::class, 'profit'])->name('reports.profit');
    Route::post('reports/export', [ReportsController::class, 'export'])->name('reports.export');

    // Резервное копирование
    Route::get('backup', [BackupController::class, 'index'])->name('backup');
    Route::post('backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('backup/{backup}/download', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('backup/{backup}', [BackupController::class, 'destroy'])->name('backup.destroy');
});
