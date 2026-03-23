<?php

use Illuminate\Support\Facades\Route;
use Modules\Seller\Http\Controllers\DashboardController;
use Modules\Seller\Http\Controllers\SaleController;
use Modules\Seller\Http\Controllers\CartController;
use Modules\Seller\Http\Controllers\ProductsController;
use Modules\Seller\Http\Controllers\CouponsController;
use Modules\Seller\Http\Controllers\ReceiptsController;
use Modules\Seller\Http\Controllers\CustomersController;

/*
|--------------------------------------------------------------------------
| Web Routes for Seller Module
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('seller')->name('seller.')->group(function () {
    
    // Дашборд продавца
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Продажа (POS система)
    Route::get('sale', [SaleController::class, 'index'])->name('sale');
    Route::post('sale/process', [SaleController::class, 'process'])->name('sale.process');
    
    // Корзина
    Route::get('cart', [CartController::class, 'index'])->name('cart');
    Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
    
    // Товары для продажи
    Route::get('products', [ProductsController::class, 'index'])->name('products');
    Route::get('products/search', [ProductsController::class, 'search'])->name('products.search');
    Route::get('products/{product}', [ProductsController::class, 'show'])->name('products.show');
    
    // Купоны
    Route::get('coupons', [CouponsController::class, 'index'])->name('coupons');
    Route::get('coupons/create', [CouponsController::class, 'create'])->name('coupons.create');
    Route::post('coupons', [CouponsController::class, 'store'])->name('coupons.store');
    Route::get('coupons/scan', [CouponsController::class, 'scan'])->name('coupons.scan');
    
    // Чеки
    Route::get('receipts', [ReceiptsController::class, 'index'])->name('receipts');
    Route::get('receipts/{transaction}', [ReceiptsController::class, 'show'])->name('receipts.show');
    Route::get('receipts/{transaction}/print', [ReceiptsController::class, 'print'])->name('receipts.print');
    
    // Клиенты
    Route::get('customers', [CustomersController::class, 'index'])->name('customers');
    Route::get('customers/{customer}', [CustomersController::class, 'show'])->name('customers.show');
});
