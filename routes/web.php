<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SellerController;
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
    return view('welcome');
})->name('home');


// ============================================================================
// АУТЕНТИФИКАЦИЯ
// ============================================================================

// ВТОРОЙ ЭТАП ЗАЩИТЫ (Вне групп guest/admin.security чтобы избежать петель)
Route::middleware(['auth'])->group(function () {
    Route::get('login/verify', [AuthController::class, 'showVerifyForm'])->name('login.verify');
    Route::post('login/verify', [AuthController::class, 'verify'])->name('login.verify.post');
});

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
// Защита: auth + role:owner + ensure.owner + session.activity
// ============================================================================

Route::middleware(['auth', 'admin.security', 'role:owner', 'ensure.owner', 'session.activity'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        // Дашборд
        Route::get('/', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('module/dashboard', [OwnerController::class, 'dashboard'])->name('module.dashboard');

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
        Route::get('activity-log', [OwnerController::class, 'activityLog'])->name('activity-log');

        // QR-сканер товаров
        Route::get('scan-product', [OwnerController::class, 'scanQr'])->name('scan-product');
        Route::post('scan-product/process', [OwnerController::class, 'processProductScan'])->name('scan-product.process');

        // Управление товарами (для владельца)
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('{product}/edit', [OwnerController::class, 'editProduct'])->name('edit');
            Route::post('{product}/update', [OwnerController::class, 'updateProduct'])->name('update');
        });

        // Отчёты
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [OwnerController::class, 'reports'])->name('index');
            Route::get('sales', [OwnerController::class, 'salesReport'])->name('sales');
            Route::get('products', [OwnerController::class, 'productsReport'])->name('products');
            Route::get('finance', [OwnerController::class, 'financeReport'])->name('finance');
            Route::get('purchases', [OwnerController::class, 'purchasesReport'])->name('purchases');
            Route::get('profit', [OwnerController::class, 'profitReport'])->name('profit');
            Route::get('expenses', [OwnerController::class, 'expensesReport'])->name('expenses');
        });
    });

// ============================================================================
// АДМИНИСТРАТОР (ADMIN) - Уровень 80
// Управление магазином, товарами, продажами, клиентами
// БЕЗ доступа к себестоимости и чистой прибыли
// Защита: auth + role:admin + ensure.admin + session.activity + 2fa.verified
// ============================================================================

Route::middleware(['auth', 'admin.security', 'role:admin', 'ensure.admin', 'session.activity', '2fa.verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Дашборд
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Audit Log - просмотр действий администратора
        Route::get('audit-log', [AdminController::class, 'auditLog'])->name('audit-log');

        // Управление пользователями
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::post('store', [AdminController::class, 'createUser'])->name('store');
            Route::get('edit/{user}', [AdminController::class, 'editUser'])->name('edit');
            Route::post('update/{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::post('delete/{user}', [AdminController::class, 'deleteUser'])->name('delete');
        });

        // Управление ролями и разрешениями
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [AdminController::class, 'roles'])->name('index');
            Route::get('{role}/permissions', [AdminController::class, 'rolePermissions'])->name('permissions');
            Route::post('{role}/permissions', [AdminController::class, 'updateRolePermissions'])->name('permissions.update');
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
            
            // Альтернативные маршруты через ProductController (для QR-кодов)
            Route::get('scan-qr', [ProductController::class, 'scanQr'])->name('scan-qr');
            Route::post('scan-page', [ProductController::class, 'scanPage'])->name('scan-page');
            Route::get('{product}/download-qr', [ProductController::class, 'downloadQr'])->name('download-qr');
            Route::post('{product}/print-qr', [ProductController::class, 'printQr'])->name('print-qr');
        });

        // Клиенты (CRM)
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [AdminController::class, 'customers'])->name('index');
            Route::get('check-phone', [AdminController::class, 'checkPhone'])->name('check-phone');
            Route::post('coupon/store', [AdminController::class, 'createCoupon'])->name('coupon.create');
            Route::post('coupon/{coupon}/update', [AdminController::class, 'updateCoupon'])->name('coupon.update');
            Route::post('coupon/{coupon}/block', [AdminController::class, 'toggleBlockCoupon'])->name('coupon.block');
            Route::post('coupon/{coupon}/unblock', [AdminController::class, 'unblockCoupon'])->name('coupon.unblock');
            Route::post('coupon/{coupon}/delete', [AdminController::class, 'deleteCoupon'])->name('coupon.delete');
            Route::post('{customer}/delete', [AdminController::class, 'deleteCustomer'])->name('delete');
            Route::post('{customer}/reset-password', [AdminController::class, 'resetPassword'])->name('reset-password');
        });

        // QR-сканер товаров (использует QrScannerController)
        // Маршруты определены отдельно ниже

        // Закупки товаров
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PurchaseController::class, 'index'])->name('index');
            Route::get('/deficit', [\App\Http\Controllers\PurchaseController::class, 'deficit'])->name('deficit');
            Route::get('/create', [\App\Http\Controllers\PurchaseController::class, 'create'])->name('create');
            Route::get('/variant/{id}/info', [\App\Http\Controllers\PurchaseController::class, 'getVariantInfo'])->name('variant.info');
            Route::post('/store', [\App\Http\Controllers\PurchaseController::class, 'store'])->name('store');
            Route::delete('/{purchase}', [\App\Http\Controllers\PurchaseController::class, 'destroy'])->name('delete');
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

        // Профиль пользователя
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [AdminController::class, 'profile'])->name('index');
            Route::post('update', [AdminController::class, 'updateProfile'])->name('update');
        });

        // Журнал действий
        Route::get('activity-log', [AdminController::class, 'activityLog'])->name('activity-log');

        // Журнал действий персонала (кладовщики и продавцы)
        Route::prefix('staff-logs')->name('staff-logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\StaffActivityLogController::class, 'index'])->name('index');
            Route::get('/{log}', [\App\Http\Controllers\StaffActivityLogController::class, 'show'])->name('show');
            Route::post('/clear', [\App\Http\Controllers\StaffActivityLogController::class, 'clear'])->name('clear');
            Route::get('/export/csv', [\App\Http\Controllers\StaffActivityLogController::class, 'export'])->name('export');
        });

        // QR-сканер
        Route::prefix('qr')->name('qr.')->group(function () {
            Route::get('scan', [QrScannerController::class, 'scan'])->name('scan');
            Route::post('process', [QrScannerController::class, 'processScan'])->name('process');
            Route::post('move-stock', [QrScannerController::class, 'moveStock'])->name('move-stock');
            Route::get('locations', [QrScannerController::class, 'getLocations'])->name('locations');

            // Управление QR-кодами
            Route::get('manage', [QrCodeController::class, 'index'])->name('manage');
            Route::get('manage/product/{product}', [QrCodeController::class, 'manageProduct'])->name('manage.product');
            Route::get('generate/{product}', [QrCodeController::class, 'generatePage'])->name('generate.page');
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

Route::middleware(['auth', 'admin.security', 'role:seller'])
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
            Route::get('{customerId}/history', [SellerController::class, 'customerHistory'])->name('history');
            Route::post('create-coupon', [SellerController::class, 'createCouponForCustomer'])->name('create-coupon');
            Route::post('store', [SellerController::class, 'storeCustomer'])->name('store');
            Route::get('find', [SellerController::class, 'findCustomer'])->name('find');
            Route::post('register', [SellerController::class, 'registerCustomer'])->name('register');
            Route::get('{customerId}/coupons', [SellerController::class, 'customerCoupons'])->name('coupons');
        });

        // Мои продажи
        Route::prefix('my-sales')->name('my-sales.')->group(function () {
            Route::get('/', [SellerController::class, 'mySales'])->name('index');
            Route::get('today', [SellerController::class, 'todaySales'])->name('today');
        });

        // Профиль продавца
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [SellerController::class, 'profile'])->name('index');
            Route::post('update', [SellerController::class, 'updateProfile'])->name('update');
        });

        // Смена
        Route::prefix('shift')->name('shift.')->group(function () {
            Route::get('/', [SellerController::class, 'shift'])->name('index');
            Route::post('open', [SellerController::class, 'openShift'])->name('open');
            Route::post('close', [SellerController::class, 'closeShift'])->name('close');
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
            Route::put('update', [ClientController::class, 'updateProfile'])->name('update-profile');
            Route::post('password', [ClientController::class, 'changePassword'])->name('password');
        });

        // Покупки
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [ClientController::class, 'purchases'])->name('index');
            Route::get('{purchase}', [ClientController::class, 'purchaseDetail'])->name('view');
            Route::get('{purchase}/receipt', [ClientController::class, 'printReceipt'])->name('receipt');
        });

        // Купоны
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [ClientController::class, 'coupons'])->name('index');
            Route::get('{coupon}/print', [ClientController::class, 'printCoupon'])->name('print');
        });
    });

// ============================================================================
// КЛАДОВЩИК (STOREKEEPER) - Уровень 20
// Склад, инвентаризация, сканирование товаров, остатки
// БЕЗ цен и финансовой информации
// ============================================================================

Route::middleware(['auth', 'admin.security', 'role:warehouse', 'ensure.warehouse'])
    ->prefix('storekeeper')
    ->name('storekeeper.')
    ->group(function () {
        // Дашборд
        Route::get('/', [App\Http\Controllers\StorekeeperController::class, 'dashboard'])->name('dashboard');

        // Профиль
        Route::get('profile', [App\Http\Controllers\StorekeeperController::class, 'profile'])->name('profile.index');
        Route::post('profile', [App\Http\Controllers\StorekeeperController::class, 'updateProfile'])->name('profile.update');

        // Наличие товаров (склад)
        Route::get('stock', [App\Http\Controllers\StorekeeperController::class, 'stock'])->name('stock.index');

        // Товары по категориям
        Route::get('products', [App\Http\Controllers\StorekeeperController::class, 'productsByCategory'])->name('products.index');
        Route::get('products/category/{categoryId}', [App\Http\Controllers\StorekeeperController::class, 'productsByCategory'])->name('products.category');
        Route::get('products/{id}', [App\Http\Controllers\StorekeeperController::class, 'productDetail'])->name('products.detail');

        // Поиск товара
        Route::get('search', [App\Http\Controllers\StorekeeperController::class, 'searchProduct'])->name('search');

        // Инвентаризация
        Route::get('inventory', [App\Http\Controllers\StorekeeperController::class, 'inventory'])->name('inventory.index');
        Route::post('inventory/scan', [App\Http\Controllers\StorekeeperController::class, 'scanInventory'])->name('inventory.scan');
        Route::post('inventory/save', [App\Http\Controllers\StorekeeperController::class, 'saveInventory'])->name('inventory.save');
    });
