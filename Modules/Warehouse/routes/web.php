<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use Modules\Warehouse\Http\Controllers\DashboardController;
use Modules\Warehouse\Http\Controllers\ProductsController;
use Modules\Warehouse\Http\Controllers\StockController;
use Modules\Warehouse\Http\Controllers\PurchasesController;
use Modules\Warehouse\Http\Controllers\QrCodeController;
use Modules\Warehouse\Http\Controllers\InventoryController;
use Modules\Warehouse\Http\Controllers\ShopsController;
use Modules\Warehouse\Http\Controllers\WriteoffController;
use Modules\Warehouse\Http\Controllers\TransferController;

/*
|--------------------------------------------------------------------------
| Web Routes for Warehouse Module
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:warehouse'])->prefix('warehouse')->name('warehouse.')->group(function () {

    // Дашборд кладовщика
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Профиль кладовщика
    Route::get('profile', [WarehouseController::class, 'profile'])->name('profile.index');
    Route::post('profile', [WarehouseController::class, 'updateProfile'])->name('profile.update');

    // Товары
    Route::get('products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductsController::class, 'update'])->name('products.update');

    // Остатки
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/by-shop', [StockController::class, 'byShop'])->name('stock.by-shop');
    Route::get('stock/low', [StockController::class, 'lowStock'])->name('stock.low');
    Route::post('stock/add', [StockController::class, 'add'])->name('stock.add');

    // Закупки
    Route::get('purchases', [PurchasesController::class, 'index'])->name('purchases.index');
    Route::get('purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
    Route::post('purchases', [PurchasesController::class, 'store'])->name('purchases.store');

    // QR-коды
    Route::get('qrcodes', [QrCodeController::class, 'index'])->name('qrcodes.index');
    Route::get('qrcodes/scan', [QrCodeController::class, 'scan'])->name('qrcodes.scan');
    Route::post('qrcodes/scan', [QrCodeController::class, 'processScan'])->name('qrcodes.scan.process');
    Route::post('qrcodes/generate', [QrCodeController::class, 'generate'])->name('qrcodes.generate');
    Route::get('qrcodes/{qrCode}/print', [QrCodeController::class, 'print'])->name('qrcodes.print');
    Route::get('qrcodes/batch-print', [QrCodeController::class, 'batchPrint'])->name('qrcodes.batch-print');

    // Инвентаризация
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/count', [InventoryController::class, 'count'])->name('inventory.count');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Магазины (точки)
    Route::get('shops', [ShopsController::class, 'index'])->name('shops.index');
    Route::get('shops/{shop}', [ShopsController::class, 'show'])->name('shops.show');

    // Списания (брак, потеря)
    Route::get('writeoff', [WriteoffController::class, 'index'])->name('writeoff.index');
    Route::get('writeoff/create', [WriteoffController::class, 'create'])->name('writeoff.create');
    Route::post('writeoff', [WriteoffController::class, 'store'])->name('writeoff.store');
    Route::get('writeoff/pending', [WriteoffController::class, 'pending'])->name('writeoff.pending');

    // Перемещения между магазинами
    Route::get('transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('transfers/create', [TransferController::class, 'create'])->name('transfers.create');
    Route::post('transfers', [TransferController::class, 'store'])->name('transfers.store');
});
