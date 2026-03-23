<?php

use Illuminate\Support\Facades\Route;
use Modules\Warehouse\Http\Controllers\DashboardController;
use Modules\Warehouse\Http\Controllers\ProductsController;
use Modules\Warehouse\Http\Controllers\StockController;
use Modules\Warehouse\Http\Controllers\PurchasesController;
use Modules\Warehouse\Http\Controllers\QrCodeController;
use Modules\Warehouse\Http\Controllers\InventoryController;
use Modules\Warehouse\Http\Controllers\ShopsController;

/*
|--------------------------------------------------------------------------
| Web Routes for Warehouse Module
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('warehouse')->name('warehouse.')->group(function () {
    
    // Дашборд кладовщика
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Товары
    Route::get('products', [ProductsController::class, 'index'])->name('products');
    Route::get('products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductsController::class, 'update'])->name('products.update');
    
    // Остатки
    Route::get('stock', [StockController::class, 'index'])->name('stock');
    Route::get('stock/by-shop', [StockController::class, 'byShop'])->name('stock.by-shop');
    Route::get('stock/low', [StockController::class, 'lowStock'])->name('stock.low');
    Route::post('stock/add', [StockController::class, 'add'])->name('stock.add');
    
    // Закупки
    Route::get('purchases', [PurchasesController::class, 'index'])->name('purchases');
    Route::get('purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
    Route::post('purchases', [PurchasesController::class, 'store'])->name('purchases.store');
    
    // QR-коды
    Route::get('qrcodes', [QrCodeController::class, 'index'])->name('qrcodes');
    Route::post('qrcodes/generate', [QrCodeController::class, 'generate'])->name('qrcodes.generate');
    Route::get('qrcodes/{qrCode}/print', [QrCodeController::class, 'print'])->name('qrcodes.print');
    Route::get('qrcodes/batch-print', [QrCodeController::class, 'batchPrint'])->name('qrcodes.batch-print');
    
    // Инвентаризация
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('inventory/count', [InventoryController::class, 'count'])->name('inventory.count');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    
    // Магазины (точки)
    Route::get('shops', [ShopsController::class, 'index'])->name('shops');
    Route::get('shops/{shop}', [ShopsController::class, 'show'])->name('shops.show');
});
