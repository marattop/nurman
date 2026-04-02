<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\Http\Controllers\DashboardController;
use Modules\Client\Http\Controllers\ProfileController;
use Modules\Client\Http\Controllers\PurchaseHistoryController;
use Modules\Client\Http\Controllers\CouponsController;
use Modules\Client\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes for Client Module
|--------------------------------------------------------------------------
*/

// Маршруты аутентификации для клиентов
Route::middleware('guest')->group(function () {
    Route::get('client/login', [AuthController::class, 'showLoginForm'])->name('client.login');
    Route::post('client/login', [AuthController::class, 'login'])->name('client.login.post');
    Route::get('client/register', [AuthController::class, 'showRegistrationForm'])->name('client.register');
    Route::post('client/register', [AuthController::class, 'register'])->name('client.register.post');
});

// Выход
Route::post('client/logout', [AuthController::class, 'logout'])->name('client.logout')->middleware('auth');

// Авторизованные клиенты
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {

    // Дашборд клиента
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Профиль
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // История покупок
    Route::get('purchases', [PurchaseHistoryController::class, 'index'])->name('purchases');
    Route::get('purchases/{transaction}', [PurchaseHistoryController::class, 'show'])->name('purchases.show');

    // Купоны
    Route::get('coupons', [CouponsController::class, 'index'])->name('coupons');
    Route::get('coupons/{coupon}', [CouponsController::class, 'show'])->name('coupons.show');

    // Уведомления
    Route::get('notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::post('notifications/{notification}/read', [DashboardController::class, 'markAsRead'])->name('notifications.read');
});
