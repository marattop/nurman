<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\AuthController;
use Modules\Core\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes for Core Module
|--------------------------------------------------------------------------
*/

// Главная страница - редирект по роли
Route::get('/', [DashboardController::class, 'redirectByRole'])->name('home');

// Аутентификация
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
});

// Выход
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
