<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QrScannerController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\TwoFactorController;

// ============================================================================
// ГЛАВНАЯ СТРАНИЦА
// ============================================================================

Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->getRedirectPath());
    }
    return redirect()->route('login');
})->name('home');

// Тест фото товаров (временный маршрут)
Route::get('/test-photo', function() {
    $products = App\Models\Product::all();
    
    echo "<h2>Проверка фото товаров</h2>";
    echo "<h3>Всего товаров: " . $products->count() . "</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Название</th><th>Артикул</th><th>Фото (путь в БД)</th><th>Файл существует</th><th>URL</th></tr>";

    foreach ($products as $product) {
        $hasPhoto = !empty($product->photo);
        $fullPath = storage_path('app/public/' . $product->photo);
        $fileExists = $hasPhoto && file_exists($fullPath);
        $url = $hasPhoto ? asset('storage/' . $product->photo) : '—';
        
        echo "<tr>";
        echo "<td>{$product->id}</td>";
        echo "<td>{$product->name}</td>";
        echo "<td>{$product->article}</td>";
        echo "<td>" . ($hasPhoto ? $product->photo : '<span style="color:red">НЕТ ФОТО</span>') . "</td>";
        echo "<td>" . ($fileExists ? '<span style="color:green">✓ ДА</span>' : '<span style="color:red">✗ НЕТ</span>') . "</td>";
        echo "<td>" . ($hasPhoto ? "<a href='{$url}' target='_blank'>Открыть</a><br><img src='{$url}' style='max-width:100px;'>" : '—') . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    echo "<h3>Проверка symlink</h3>";
    $publicPath = public_path('storage');
    echo "Путь: {$publicPath}<br>";
    echo "Существует: " . (file_exists($publicPath) ? '✓ ДА' : '✗ НЕТ') . "<br>";
    echo "Ссылка: " . (is_link($publicPath) ? '✓ ДА' : '✗ НЕТ') . "<br>";
    
    return;
});

// ============================================================================
// АУТЕНТИФИКАЦИЯ
// ============================================================================

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    
    // Регистрация для клиентов (покупателей)
    Route::get('client/register', [ClientAuthController::class, 'showRegistrationForm'])->name('client.register');
    Route::post('client/register', [ClientAuthController::class, 'register']);
    Route::post('client/register/check-phone', [ClientAuthController::class, 'checkPhone']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================================================
// 2FA АУТЕНТИФИКАЦИЯ
// ============================================================================

Route::middleware('auth')->prefix('2fa')->name('auth.2fa.')->group(function () {
    Route::get('/', [TwoFactorController::class, 'show'])->name('show');
    Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify');
    Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
    Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
    Route::post('/regenerate-codes', [TwoFactorController::class, 'regenerateCodes'])->name('regenerate');
});

// ============================================================================
// ВЛАДЕЛЕЦ (OWNER) - Уровень 100
// Полный доступ ко всем функциям системы
// ============================================================================

Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        // Дашборд
        Route::get('/', [OwnerController::class, 'dashboard'])->name('dashboard');

        // Финансы
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/', [OwnerController::class, 'finance'])->name('index');
            Route::get('profit', [OwnerController::class, 'profit'])->name('profit');
            Route::get('margin', [OwnerController::class, 'margin'])->name('margin');
            Route::get('balance', [OwnerController::class, 'balance'])->name('balance');
        });

        // Управление пользователями
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [OwnerController::class, 'users'])->name('index');
            Route::get('create', [OwnerController::class, 'createUser'])->name('create');
            Route::post('store', [OwnerController::class, 'storeUser'])->name('store');
            Route::get('edit/{user}', [OwnerController::class, 'editUser'])->name('edit');
            Route::put('update/{user}', [OwnerController::class, 'updateUser'])->name('update');
            Route::delete('delete/{user}', [OwnerController::class, 'deleteUser'])->name('delete');
        });

        // Управление ролями и разрешениями
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('create', [RoleController::class, 'create'])->name('create');
            Route::post('store', [RoleController::class, 'store'])->name('store');
            Route::get('edit/{role}', [RoleController::class, 'edit'])->name('edit');
            Route::put('update/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('delete/{role}', [RoleController::class, 'destroy'])->name('delete');
            Route::get('{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
            Route::post('{role}/permissions', [RoleController::class, 'syncPermissions'])->name('permissions.sync');
        });

        // Журнал действий (Activity Logs)
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [OwnerController::class, 'activityLog'])->name('index');
            Route::get('export', [OwnerController::class, 'exportLogs'])->name('export');
        });

        // Отчёты
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [OwnerController::class, 'reports'])->name('index');
            Route::get('sales', [OwnerController::class, 'salesReport'])->name('sales');
            Route::get('products', [OwnerController::class, 'productsReport'])->name('products');
            Route::get('finance', [OwnerController::class, 'financeReport'])->name('finance');
        });
    });

// ============================================================================
// АДМИНИСТРАТОР (ADMIN) - Уровень 80
// Управление магазином, товарами, продажами, клиентами
// БЕЗ доступа к себестоимости и чистой прибыли
// ============================================================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Дашборд
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Управление пользователями
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::post('store', [AdminController::class, 'createUser'])->name('store');
            Route::get('edit/{user}', [AdminController::class, 'editUser'])->name('edit');
            Route::post('update/{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::post('delete/{user}', [AdminController::class, 'deleteUser'])->name('delete');
        });

        // Управление товарами
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [AdminController::class, 'products'])->name('index');
            Route::get('create', [AdminController::class, 'createProduct'])->name('create');
            Route::post('store', [AdminController::class, 'storeProduct'])->name('store');
            // Специфичные маршруты должны идти ДО {product}
            Route::post('delete/{product}', [AdminController::class, 'deleteProduct'])->name('delete');
            Route::post('copy/{product}', [AdminController::class, 'copyProduct'])->name('copy');
            Route::get('edit/{product}', [AdminController::class, 'editProduct'])->name('edit');
            Route::post('update/{product}', [AdminController::class, 'updateProduct'])->name('update');
        });

        // Продажи
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [AdminController::class, 'sales'])->name('index');
            Route::get('create', [AdminController::class, 'createSale'])->name('create');
            Route::post('store', [AdminController::class, 'storeSale'])->name('store');
            Route::get('edit/{transaction}', [AdminController::class, 'editSale'])->name('edit');
            Route::post('update/{transaction}', [AdminController::class, 'updateSale'])->name('update');
        });

        // Возвраты
        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('/', [AdminController::class, 'returns'])->name('index');
            Route::get('pending', [AdminController::class, 'pendingReturns'])->name('pending');
            Route::post('approve/{return}', [AdminController::class, 'approveReturn'])->name('approve');
            Route::post('reject/{return}', [AdminController::class, 'rejectReturn'])->name('reject');
        });

        // Клиенты (CRM)
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [AdminController::class, 'customers'])->name('index');
            Route::get('check-phone', [AdminController::class, 'checkPhone'])->name('check-phone');
            Route::post('store', [AdminController::class, 'createCoupon'])->name('store');
            Route::put('update/{coupon}', [AdminController::class, 'updateCoupon'])->name('update');
            Route::put('toggle-block/{coupon}', [AdminController::class, 'toggleBlockCoupon'])->name('toggle-block');
            Route::delete('delete/{coupon}', [AdminController::class, 'deleteCoupon'])->name('delete');
            Route::post('{customerId}/reset-password', [AdminController::class, 'resetPassword'])->name('reset-password');
        });

        // Привязка продавцов к магазинам
        Route::prefix('sellers')->name('sellers.')->group(function () {
            Route::get('shops', [\App\Http\Controllers\SellerShopController::class, 'index'])->name('shops');
            Route::post('shops/assign', [\App\Http\Controllers\SellerShopController::class, 'assign'])->name('shops.assign');
            Route::post('shops/detach/{sellerId}', [\App\Http\Controllers\SellerShopController::class, 'detach'])->name('shops.detach');
            Route::get('{sellerId}/settings', [\App\Http\Controllers\SellerSettingsController::class, 'index'])->name('settings');
            Route::post('{sellerId}/settings', [\App\Http\Controllers\SellerSettingsController::class, 'update'])->name('settings.update');
        });

        // Магазины
        Route::prefix('shops')->name('shops.')->group(function () {
            Route::get('/', [AdminController::class, 'shops'])->name('index');
            Route::post('store', [AdminController::class, 'createShop'])->name('store');
            Route::post('update/{shop}', [AdminController::class, 'updateShop'])->name('update');
            Route::delete('delete/{shop}', [AdminController::class, 'deleteShop'])->name('delete');
        });

        // Отчёты
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [AdminController::class, 'reports'])->name('index');
            Route::get('sales', [AdminController::class, 'salesReport'])->name('sales');
            Route::get('products', [AdminController::class, 'productsReport'])->name('products');
            Route::get('stock', [AdminController::class, 'stockReport'])->name('stock');
        });

        // Настройки
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminController::class, 'settings'])->name('index');
            Route::post('save', [AdminController::class, 'saveSettings'])->name('save');
        });

        // Журнал действий
        Route::get('activity-log', [AdminController::class, 'activityLog'])->name('activity-log');

        // QR-сканер
        Route::prefix('qr')->name('qr.')->group(function () {
            Route::get('scan', [QrScannerController::class, 'scan'])->name('scan');
            Route::post('process', [QrScannerController::class, 'processScan'])->name('process');
            Route::post('move-stock', [QrScannerController::class, 'moveStock'])->name('move-stock');
            Route::get('locations', [QrScannerController::class, 'getLocations'])->name('locations');
            
            // Управление QR-кодами
            Route::get('manage', [QrCodeController::class, 'index'])->name('manage');
            Route::post('generate', [QrCodeController::class, 'generate'])->name('generate');
            Route::post('generate-all', [QrCodeController::class, 'generateAll'])->name('generate-all');
            Route::post('print', [QrCodeController::class, 'print'])->name('print');
            Route::delete('delete/{id}', [QrCodeController::class, 'delete'])->name('delete');
        });
    });

// ============================================================================
// ПРОДАВЕЦ (SELLER) - Уровень 50
// POS-терминал, оформление продаж, работа с клиентами
// ============================================================================

Route::middleware(['auth', 'role:seller'])
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {
        // Дашборд
        Route::get('/', [SellerController::class, 'dashboard'])->name('dashboard');

        // POS-терминал
        Route::prefix('pos')->name('pos.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PosController::class, 'index'])->name('index');
            Route::get('products', [\App\Http\Controllers\PosController::class, 'getProducts'])->name('products');
            Route::get('products/search', [\App\Http\Controllers\PosController::class, 'searchProducts'])->name('products.search');
            Route::get('products/category/{categoryId}', [\App\Http\Controllers\PosController::class, 'productsByCategory'])->name('products.category');
            Route::get('check-permissions', [\App\Http\Controllers\PosController::class, 'checkPermissions'])->name('check-permissions');
            Route::post('scan-product', [\App\Http\Controllers\PosController::class, 'scanProduct'])->name('scan-product');
            Route::post('scan-customer', [\App\Http\Controllers\PosController::class, 'scanCustomer'])->name('scan-customer');
            Route::post('scan-coupon', [\App\Http\Controllers\PosController::class, 'scanCoupon'])->name('scan-coupon');
            Route::get('check-stock', [\App\Http\Controllers\PosController::class, 'checkStock'])->name('check-stock');
            Route::post('checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('checkout');
            Route::get('receipt/{transaction}', [\App\Http\Controllers\PosController::class, 'printReceipt'])->name('receipt');
            Route::get('history', [\App\Http\Controllers\PosController::class, 'salesHistory'])->name('history');
            Route::get('search-sale', [\App\Http\Controllers\PosController::class, 'searchSale'])->name('search-sale');
            Route::post('process-return', [\App\Http\Controllers\PosController::class, 'processReturn'])->name('process-return');
        });

        // Поиск товаров (для POS)
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('search', [SellerController::class, 'searchProducts'])->name('search');
            Route::get('scan', [SellerController::class, 'scanBarcode'])->name('scan');
        });

        // Клиенты
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [SellerController::class, 'customers'])->name('index');
            Route::post('store', [SellerController::class, 'storeCustomer'])->name('store');
            Route::get('find', [SellerController::class, 'findCustomer'])->name('find');
            Route::post('create-coupon', [SellerController::class, 'createCoupon'])->name('create-coupon');
            Route::post('register', [SellerController::class, 'registerCustomer'])->name('register');
            Route::get('{customerId}/coupons', [SellerController::class, 'customerCoupons'])->name('coupons');
        });

        // Мои продажи
        Route::prefix('my-sales')->name('my-sales.')->group(function () {
            Route::get('/', [SellerController::class, 'mySales'])->name('index');
            Route::get('today', [SellerController::class, 'todaySales'])->name('today');
        });

        // Смена
        Route::prefix('shift')->name('shift.')->group(function () {
            Route::get('/', [SellerController::class, 'shift'])->name('index');
            Route::post('open', [SellerController::class, 'openShift'])->name('open');
            Route::post('close', [SellerController::class, 'closeShift'])->name('close');
        });
    });

// ============================================================================
// КЛАДОВЩИК (WAREHOUSE) - Уровень 20
// Управление складом, приёмка, инвентаризация, QR-коды
// ============================================================================

Route::middleware(['auth', 'role:warehouse'])
    ->prefix('warehouse')
    ->name('warehouse.')
    ->group(function () {
        // Дашборд
        Route::get('/', [WarehouseController::class, 'dashboard'])->name('dashboard');

        // Приёмка товара
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [WarehouseController::class, 'purchases'])->name('index');
            Route::get('create', [WarehouseController::class, 'createPurchase'])->name('create');
            Route::post('store', [WarehouseController::class, 'storePurchase'])->name('store');
            Route::get('edit/{purchase}', [WarehouseController::class, 'editPurchase'])->name('edit');
            Route::put('update/{purchase}', [WarehouseController::class, 'updatePurchase'])->name('update');
        });

        // Остатки
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/', [WarehouseController::class, 'stock'])->name('index');
            Route::get('low', [WarehouseController::class, 'lowStock'])->name('low');
            Route::post('adjust', [WarehouseController::class, 'adjustStock'])->name('adjust');
        });

        // Инвентаризация
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [WarehouseController::class, 'inventory'])->name('index');
            Route::post('start', [WarehouseController::class, 'startInventory'])->name('start');
            Route::post('complete', [WarehouseController::class, 'completeInventory'])->name('complete');
        });

        // QR-коды
        Route::prefix('qr-codes')->name('qr.')->group(function () {
            Route::get('/', [WarehouseController::class, 'qrCodes'])->name('index');
            Route::post('generate', [WarehouseController::class, 'generateQr'])->name('generate');
            Route::post('print', [WarehouseController::class, 'printQr'])->name('print');
        });

        // Списание товара
        Route::prefix('writeoff')->name('writeoff.')->group(function () {
            Route::get('/', [WarehouseController::class, 'writeoff'])->name('index');
            Route::post('create', [WarehouseController::class, 'createWriteoff'])->name('create');
            Route::get('pending', [WarehouseController::class, 'pendingWriteoff'])->name('pending');
        });

        // Перемещение товара
        Route::prefix('transfers')->name('transfers.')->group(function () {
            Route::get('/', [WarehouseController::class, 'transfers'])->name('index');
            Route::post('create', [WarehouseController::class, 'createTransfer'])->name('create');
        });
    });

// ============================================================================
// КЛИЕНТ (CLIENT) - Уровень 10
// Профиль покупателя, история покупок, бонусы
// ============================================================================

Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        // Дашборд
        Route::get('/', [ClientController::class, 'dashboard'])->name('dashboard');

        // Профиль
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ClientController::class, 'profile'])->name('index');
            Route::put('update', [ClientController::class, 'updateProfile'])->name('update');
            Route::post('password', [ClientController::class, 'changePassword'])->name('password');
        });

        // Покупки
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [ClientController::class, 'purchases'])->name('index');
            Route::get('{purchase}', [ClientController::class, 'purchaseDetail'])->name('show');
            Route::get('{purchase}/receipt', [ClientController::class, 'printReceipt'])->name('receipt');
        });

        // Купоны
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [ClientController::class, 'coupons'])->name('index');
            Route::get('{coupon}/print', [ClientController::class, 'printCoupon'])->name('print');
        });
    });
