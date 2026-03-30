<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\CustomerProfile;
use App\Models\Coupon;
use App\Models\QrCode;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Models\AdminAuditLog;
use App\Services\AdminSecurityService;
use App\Services\SecurityLogger;

/**
 * Контроллер Администратора.
 * 
 * Доступно:
 * - Управление товарами, ценами, скидками
 * - Управление пользователями (кроме Owner)
 * - Управление магазинами
 * - Управление клиентами и купонами
 * - Отчёты по продажам и товарам (без прибыли)
 * 
 * НЕ доступно:
 * - Закупочные цены (себестоимость)
 * - Чистая прибыль
 * - Маржинальность
 * - Общий баланс компании
 */
class AdminController extends Controller
{
    /**
     * Конструктор с внедрением зависимостей.
     */
    public function __construct(
        protected AdminSecurityService $securityService,
        protected SecurityLogger $securityLogger
    ) {}

    // ========================================================================
    // 🏠 ДАШБОРД
    // ========================================================================

    /**
     * Главная страница администратора.
     */
    public function dashboard()
    {
        $today = now()->format('Y-m-d');

        $stats = [
            // Считаем только сотрудников (роли 2, 3, 4), без клиентов
            'users_count' => User::whereIn('role_id', [2, 3, 4])->count(),
            'active_users' => User::where('is_active', true)->whereIn('role_id', [2, 3, 4])->count(),
            'products_count' => Product::count(),
            'products_active' => Product::where('is_active', true)->count(),
            'shops_count' => Shop::count(),
            'warehouses_count' => Warehouse::count(),
            'low_stock_count' => DB::table('stock')->whereColumn('quantity', '<=', 'min_stock')->count(),
            'total_stock_qty' => DB::table('stock')->sum('quantity'),
            // Продажи сегодня
            'today_sales' => Transaction::sales()->whereDate('created_at', $today)->count(),
            'today_revenue' => Transaction::sales()->whereDate('created_at', $today)->sum('total_amount'),
        ];

        // Последние 5 товаров
        $recentProducts = Product::with(['category', 'createdBy', 'variants.stock', 'variants.qrCodes'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Товары с низким остатком (дефицит)
        $deficitCount = DB::table('stock')
            ->whereRaw('quantity <= 5')
            ->count();
        
        $lowStockProducts = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('stock', 'product_variants.id', '=', 'stock.variant_id')
            ->join('shops', 'stock.shop_id', '=', 'shops.id')
            ->whereRaw('stock.quantity <= 5')
            ->select('products.id', 'products.name', 'products.article', 'products.photo',
                     'product_variants.size', 'shops.name as shop_name', 'stock.quantity')
            ->orderBy('stock.quantity')
            ->limit(5)
            ->get();

        // Настройка авто-генерации QR
        $qrAutoGenerate = Setting::get('qr_auto_generate', true);

        // Роли для модального окна
        $roles = Role::where('id', '!=', 1)->get();

        return view('admin.dashboard', compact(
            'stats', 'recentProducts', 'lowStockProducts', 'deficitCount', 'qrAutoGenerate', 'roles'
        ));
    }

    // ========================================================================
    // 📦 УПРАВЛЕНИЕ ТОВАРАМИ
    // ========================================================================

    /**
     * Список товаров с фильтрами.
     */
    public function products(Request $request)
    {
        $query = Product::with(['category', 'variants.stock', 'variants.qrCodes', 'createdBy']);

        // Поиск
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('article', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Фильтр по категории
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Фильтр по магазину (через остатки)
        if ($request->filled('shop')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('stock', function($qs) use ($request) {
                    $qs->where('shop_id', $request->shop);
                });
            });
        }

        // Фильтр по остаткам
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereHas('variants', function($q) {
                    $q->whereHas('stock', function($qs) {
                        $qs->whereColumn('quantity', '<=', 'min_stock');
                    });
                });
            } elseif ($request->stock_status === 'out') {
                $query->whereHas('variants', function($q) {
                    $q->whereHas('stock', function($qs) {
                        $qs->where('quantity', 0);
                    });
                });
            } elseif ($request->stock_status === 'in') {
                $query->whereHas('variants', function($q) {
                    $q->whereHas('stock', function($qs) {
                        $qs->where('quantity', '>', 0);
                    });
                });
            }
        }

        // Фильтр по наличию QR
        if ($request->filled('has_qr')) {
            if ($request->has_qr === '1') {
                $query->whereHas('variants', function($q) {
                    $q->whereHas('qrCodes');
                });
            } else {
                $query->whereDoesntHave('variants', function($q) {
                    $q->whereHas('qrCodes');
                });
            }
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Статистика
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'with_qr' => DB::table('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->join('qr_codes', 'product_variants.id', '=', 'qr_codes.variant_id')
                ->distinct('products.id')
                ->count('products.id'),
            'total_value' => DB::table('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->join('stock', 'product_variants.id', '=', 'stock.variant_id')
                ->sum(DB::raw('IFNULL(product_variants.supplier_price, 0) * stock.quantity')),
        ];

        $categories = Category::all();
        $shops = Shop::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'stats', 'categories', 'shops'));
    }

    /**
     * Форма добавления товара.
     */
    public function createProduct()
    {
        $categories = Category::all();
        $shops = Shop::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        // Получаем первый магазин для выбора по умолчанию
        $firstShopId = $shops->first()?->id ?? null;

        return view('admin.products.create', compact('categories', 'shops', 'warehouses', 'firstShopId'));
    }

    /**
     * Сохранение товара.
     */
    public function storeProduct(Request $request)
    {
        // Проверка rate limiting
        if (!$this->securityService->checkRateLimit(auth()->user(), 'product.delete')) {
            return back()->withErrors(['error' => 'Слишком много операций с товарами. Попробуйте позже.']);
        }

        $validated = $request->validate([
            'article' => 'nullable|string|max:100|unique:products,article',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'retail_price' => 'required|numeric|min:0',
            'supplier_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'shop_id' => 'nullable|exists:shops,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'is_active' => 'nullable|boolean',
            'photo' => 'nullable|image|max:2048',
            'camera_photo' => 'nullable|image|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string|max:20',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.quantity' => 'required|integer|min:0',
        ]);

        // Генерация артикула если не указан
        if (empty($validated['article'])) {
            $validated['article'] = $this->generateArticle();
        }

        // Загрузка фото (с устройства или с камеры)
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
        } elseif ($request->hasFile('camera_photo')) {
            $photoPath = $request->file('camera_photo')->store('products', 'public');
        }

        // Создание товара
        $product = Product::create([
            'article' => $validated['article'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'retail_price' => $validated['retail_price'],
            'brand' => $validated['brand'] ?? null,
            'material' => $validated['material'] ?? null,
            'country' => $validated['country'] ?? null,
            'photo' => $photoPath,
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        // Создание вариантов
        $shopId = $validated['shop_id'] ?? null;
        $warehouseId = $validated['warehouse_id'] ?? null;
        $supplierPrice = $request->input('supplier_price', 0);
        $wholesalePrice = $request->input('wholesale_price', null);
        
        foreach ($validated['variants'] as $variantData) {
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'size' => $variantData['size'],
                'color' => $variantData['color'] ?? null,
                'sku' => $validated['article'] . '-' . strtoupper($variantData['size']),
                'supplier_price' => $supplierPrice,
                'wholesale_price' => $wholesalePrice,
                'is_active' => true,
            ]);

            // Создание остатков
            if ($shopId) {
                Stock::create([
                    'variant_id' => $variant->id,
                    'shop_id' => $shopId,
                    'quantity' => $variantData['quantity'],
                    'min_stock' => 5,
                ]);
            } elseif ($warehouseId) {
                Stock::create([
                    'variant_id' => $variant->id,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $variantData['quantity'],
                    'min_stock' => 5,
                ]);
            }

            // Авто-генерация QR-кодов (если включено в настройках)
            if (Setting::get('qr_auto_generate', true)) {
                $this->generateQrCode($variant, $product->article);
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_create',
            'description' => "Создан товар {$product->name} ({$product->article})",
            'model_type' => Product::class,
            'model_id' => $product->id,
        ]);

        // Audit Log
        AdminAuditLog::logAction(
            auth()->user(),
            AdminAuditLog::ACTION_PRODUCT_CREATE,
            $product,
            null,
            [
                'name' => $product->name,
                'article' => $product->article,
                'retail_price' => $product->retail_price,
            ]
        );

        // Security Log
        $this->securityLogger->logAdminProductEdit(
            auth()->user(),
            $product->id,
            $product->name,
            ['action' => 'create']
        );

        return redirect()->route('admin.products.index')->with('success', 'Товар успешно добавлен');
    }

    /**
     * Редактирование товара.
     */
    public function editProduct(Product $product)
    {
        $categories = Category::all();
        $shops = Shop::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        $product->load(['variants.stock.shop', 'variants.qrCodes']);

        // Получаем shop_id из первого stock
        $shopId = null;
        $warehouseId = null;
        if ($product->variants->isNotEmpty()) {
            $firstStock = $product->variants->first()->stock->first();
            if ($firstStock) {
                $shopId = $firstStock->shop_id;
                $warehouseId = $firstStock->warehouse_id;
            }
        }

        return view('admin.products.edit', compact('product', 'categories', 'shops', 'warehouses', 'shopId', 'warehouseId'));
    }

    /**
     * Обновление товара.
     */
    public function updateProduct(Request $request, Product $product)
    {
        // Проверка прав
        if (!$this->securityService->canEditProduct(auth()->user(), $product)) {
            abort(403, 'Доступ запрещён');
        }

        // Проверка rate limiting для изменения цен
        if ($request->filled('retail_price') || $request->filled('supplier_price')) {
            if (!$this->securityService->checkRateLimit(auth()->user(), 'price.change')) {
                return back()->withErrors(['error' => 'Слишком много изменений цен. Попробуйте позже.']);
            }
        }

        $validated = $request->validate([
            'article' => 'required|string|max:100|unique:products,article,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'retail_price' => 'required|numeric|min:0',
            'supplier_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
            'camera_photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'shop_id' => 'nullable|exists:shops,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.quantity' => 'nullable|integer|min:0',
        ]);

        $oldName = $product->name;

        // Обновление фото (с устройства или с камеры)
        if ($request->hasFile('photo')) {
            // Удаление старого фото
            if ($product->photo) {
                \Storage::disk('public')->delete($product->photo);
            }
            $validated['photo'] = $request->file('photo')->store('products', 'public');
        } elseif ($request->hasFile('camera_photo')) {
            // Удаление старого фото
            if ($product->photo) {
                \Storage::disk('public')->delete($product->photo);
            }
            $validated['photo'] = $request->file('camera_photo')->store('products', 'public');
        } else {
            unset($validated['photo']);
        }

        $product->update($validated);

        // Обновление цены закупки и оптовой цены для всех вариантов
        if ($request->filled('supplier_price') || $request->filled('wholesale_price')) {
            foreach ($product->variants as $variant) {
                $updateData = [];
                if ($request->filled('supplier_price')) {
                    $updateData['supplier_price'] = $request->supplier_price;
                }
                if ($request->filled('wholesale_price')) {
                    $updateData['wholesale_price'] = $request->wholesale_price;
                }
                $variant->update($updateData);
            }
        }

        // Обновление вариантов и остатков
        if ($request->filled('variants')) {
            $shopId = $request->shop_id;
            $warehouseId = $request->warehouse_id;

            foreach ($request->variants as $index => $variantData) {
                $variant = $product->variants[$index] ?? null;

                if (!$variant) {
                    // Создаём новый вариант если нет существующего
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $variantData['size'] ?? '',
                        'color' => $variantData['color'] ?? '',
                        'sku' => $product->article . '-' . strtoupper(substr(md5($variantData['size'] . $variantData['color'] . time() . $index), 0, 6)),
                        'supplier_price' => $variantData['price'] ?? $request->supplier_price ?? 0,
                        'wholesale_price' => null,
                        'barcode' => null,
                        'is_active' => true,
                    ]);
                } else {
                    // Обновляем существующий вариант
                    $variant->update([
                        'size' => $variantData['size'] ?? $variant->size,
                        'color' => $variantData['color'] ?? $variant->color,
                        'supplier_price' => $variantData['price'] ?? $variant->supplier_price,
                    ]);
                }

                // Обновляем остатки
                if ($variant && isset($variantData['quantity'])) {
                    $newQuantity = (int) $variantData['quantity'];

                    // Если указан shop_id, обновляем/создаём для магазина
                    if ($shopId) {
                        // Удаляем складские остатки для этого варианта
                        Stock::where('variant_id', $variant->id)
                            ->whereNotNull('warehouse_id')
                            ->delete();

                        // Обновляем или создаём остаток для магазина
                        $stock = Stock::where('variant_id', $variant->id)
                            ->where('shop_id', $shopId)
                            ->first();

                        if ($stock) {
                            $stock->update(['quantity' => $newQuantity]);
                        } else {
                            Stock::create([
                                'variant_id' => $variant->id,
                                'shop_id' => $shopId,
                                'quantity' => $newQuantity,
                                'reserved' => 0,
                                'min_stock' => 5,
                            ]);
                        }
                    }
                    // Если указан warehouse_id, обновляем/создаём для склада
                    elseif ($warehouseId) {
                        // Удаляем магазинные остатки для этого варианта
                        Stock::where('variant_id', $variant->id)
                            ->whereNotNull('shop_id')
                            ->delete();

                        // Обновляем или создаём остаток для склада
                        $stock = Stock::where('variant_id', $variant->id)
                            ->where('warehouse_id', $warehouseId)
                            ->first();

                        if ($stock) {
                            $stock->update(['quantity' => $newQuantity]);
                        } else {
                            Stock::create([
                                'variant_id' => $variant->id,
                                'warehouse_id' => $warehouseId,
                                'quantity' => $newQuantity,
                                'reserved' => 0,
                                'min_stock' => 5,
                            ]);
                        }
                    }
                }
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_update',
            'description' => "Обновлен товар {$oldName}",
            'model_type' => Product::class,
            'model_id' => $product->id,
        ]);

        // Audit Log
        AdminAuditLog::logAction(
            auth()->user(),
            AdminAuditLog::ACTION_PRODUCT_UPDATE,
            $product,
            ['name' => $oldName],
            ['name' => $product->name, 'article' => $product->article]
        );

        // Security Log
        $this->securityLogger->logAdminProductEdit(
            auth()->user(),
            $product->id,
            $product->name,
            ['action' => 'update', 'old_name' => $oldName]
        );

        return redirect()->route('admin.products.index')->with('success', 'Товар обновлён');
    }

    /**
     * Удаление/деактивация товара.
     */
    public function deleteProduct(Product $product)
    {
        // Проверка прав
        if (!$this->securityService->canDeleteProduct(auth()->user(), $product)) {
            abort(403, 'Доступ запрещён');
        }

        // Проверка: есть ли продажи
        $hasSales = DB::table('transaction_items')
            ->join('product_variants', 'transaction_items.variant_id', '=', 'product_variants.id')
            ->where('product_variants.product_id', $product->id)
            ->exists();

        if ($hasSales) {
            // Деактивация вместо удаления
            $product->update(['is_active' => false]);
            $message = "Товар деактивирован (есть история продаж)";
            $deactivated = true;
        } else {
            $product->delete();
            $message = "Товар удалён";
            $deactivated = false;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_delete',
            'description' => "{$message}: {$product->name}",
            'model_type' => Product::class,
            'model_id' => $product->id,
        ]);

        // Audit Log
        AdminAuditLog::logAction(
            auth()->user(),
            AdminAuditLog::ACTION_PRODUCT_DELETE,
            $product,
            ['name' => $product->name, 'article' => $product->article],
            ['deleted' => true, 'deactivated' => $deactivated]
        );

        // Security Log
        $this->securityLogger->logAdminProductDelete(
            auth()->user(),
            $product->id,
            $product->name,
            $deactivated
        );

        return back()->with('success', $message);
    }

    /**
     * Копирование товара.
     */
    public function copyProduct(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->article = $product->article . '-COPY-' . time();
        $newProduct->name = $product->name . ' (Копия)';
        $newProduct->save();

        // Копирование вариантов
        foreach ($product->variants as $variant) {
            $newVariant = $variant->replicate();
            $newVariant->product_id = $newProduct->id;
            $newVariant->sku = $newProduct->article . '-' . strtoupper($variant->size);
            $newVariant->save();

            // Копирование остатков
            foreach ($variant->stock as $stock) {
                $newStock = $stock->replicate();
                $newStock->variant_id = $newVariant->id;
                $newStock->save();
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_copy',
            'description' => "Скопирован товар {$product->name}",
            'model_type' => Product::class,
            'model_id' => $newProduct->id,
        ]);

        return redirect()->route('admin.products.edit', $newProduct)
            ->with('success', 'Товар скопирован. Измените артикул.');
    }

    /**
     * Генерация QR-кодов для товара.
     */
    public function generateProductQr(Product $product)
    {
        $count = 0;
        foreach ($product->variants as $variant) {
            $count += $this->generateQrCode($variant) ? 1 : 0;
        }

        return back()->with('success', "Сгенерировано QR-кодов: {$count}");
    }

    /**
     * Генерация уникального артикула.
     * Формат: ART + случайные 6 цифр (например: ART734829)
     */
    private function generateArticle(): string
    {
        do {
            // Генерируем случайные 6 цифр
            $randomDigits = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $article = 'ART' . $randomDigits;
            
            // Проверяем уникальность
            $exists = Product::where('article', $article)->exists();
        } while ($exists);
        
        return $article;
    }

    /**
     * Внутренний метод генерации QR для варианта.
     * QR-код содержит ТОЛЬКО артикул товара.
     */
    private function generateQrCode(ProductVariant $variant, string $article): bool
    {
        // QR-код содержит только артикул (без размера и других данных)
        $qrData = ['article' => $article];
        $qrCodeContent = json_encode($qrData, JSON_UNESCAPED_UNICODE);
        $qrCodePath = 'qrcodes/' . $article . '-' . $variant->id . '.png';

        // Проверяем существующий QR
        $existingQr = QrCode::where('variant_id', $variant->id)->first();
        if ($existingQr) {
            // Если QR уже имеет путь и файл существует - ничего не делаем
            if (!empty($existingQr->qr_code_path) && file_exists(public_path('storage/' . $existingQr->qr_code_path))) {
                return true;
            }
            
            // Обновляем существующий с новым артикулом
            $existingQr->update([
                'qr_data' => $qrData,
                'is_printed' => false,
            ]);
            
            // Генерируем новый QR-код через API
            $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrCodeContent);
            try {
                $qrImageContent = file_get_contents($qrImageUrl);
                if ($qrImageContent !== false && strlen($qrImageContent) > 0) {
                    \Illuminate\Support\Facades\Storage::disk('public')->put($qrCodePath, $qrImageContent);
                    $existingQr->update(['qr_code_path' => $qrCodePath]);
                    \Log::info("QR сгенерирован: {$qrCodePath}");
                } else {
                    \Log::error("QR не сгенерировался - пустой ответ от API: {$qrImageUrl}");
                }
            } catch (\Exception $e) {
                \Log::error("QR ошибка генерации: " . $e->getMessage());
            }
            return true;
        }

        // Генерируем QR-код через API
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrCodeContent);
        try {
            $qrImageContent = file_get_contents($qrImageUrl);
            if ($qrImageContent !== false && strlen($qrImageContent) > 0) {
                \Illuminate\Support\Facades\Storage::disk('public')->put($qrCodePath, $qrImageContent);
                \Log::info("QR сгенерирован: {$qrCodePath}");
            } else {
                \Log::error("QR не сгенерировался - пустой ответ от API");
            }
        } catch (\Exception $e) {
            \Log::error("QR ошибка генерации: " . $e->getMessage());
        }

        // Создаем новый QR
        QrCode::create([
            'variant_id' => $variant->id,
            'qr_code_path' => $qrCodePath,
            'qr_data' => $qrData,
            'is_printed' => false,
        ]);

        return true;
    }

    // ========================================================================
    // 👥 УПРАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯМИ
    // ========================================================================

    /**
     * Список сотрудников (администраторы, продавцы, кладовщики).
     * Клиенты отображаются в разделе "Клиенты".
     */
    public function users(Request $request)
    {
        // Показываем только сотрудников (роли 2, 3, 4), исключая Owner (1) и Клиентов (5)
        $query = User::with(['role', 'shop'])->whereIn('role_id', [2, 3, 4]);

        // Поиск
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('full_name', 'like', "%{$request->search}%");
            });
        }

        // Фильтр по роли
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => User::whereIn('role_id', [2, 3, 4])->count(),
            'active' => User::where('is_active', true)->whereIn('role_id', [2, 3, 4])->count(),
            'admins' => User::where('role_id', 2)->count(),
            'sellers' => User::where('role_id', 3)->count(),
        ];

        // Показываем только роли сотрудников (Admin, Seller, Warehouse)
        $roles = Role::whereIn('id', [2, 3, 4])->get();
        $shops = Shop::where('is_active', true)->get();

        return view('admin.users.index', compact('users', 'stats', 'roles', 'shops'));
    }

    /**
     * Список ролей.
     */
    public function roles()
    {
        // Показываем только роль кладовщика
        $roles = Role::where('name', 'warehouse')->withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Управление разрешениями роли.
     */
    public function rolePermissions(Role $role)
    {
        // Разрешаем только для роли кладовщика
        if ($role->name !== 'warehouse') {
            abort(403, 'Доступно только для роли Кладовщика');
        }

        // Получаем только разрешения склада
        $warehousePermissions = Permission::where('group', 'warehouse')
            ->orderBy('name')
            ->get(['id', 'name', 'display_name', 'description'])
            ->toArray();

        $rolePermissions = $role->getAllPermissions();
        $rolePermissions = is_array($rolePermissions) ? $rolePermissions : $rolePermissions->pluck('name')->toArray();

        // Формируем структуру как в getGroupedPermissions
        $permissions = [
            'warehouse' => [
                'label' => 'Склад',
                'permissions' => $warehousePermissions,
            ],
        ];

        return view('admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Обновление разрешений роли.
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        // Разрешаем только для роли кладовщика
        if ($role->name !== 'warehouse') {
            abort(403, 'Доступно только для роли Кладовщика');
        }

        $validated = $request->validate([
            'permissions' => 'array',
        ]);

        $allOldPermissions = $role->getAllPermissions();
        $oldPermissions = is_array($allOldPermissions) ? $allOldPermissions : $allOldPermissions->pluck('name')->toArray();
        $newPermissions = $validated['permissions'] ?? [];

        $role->syncPermissions($newPermissions);

        // Логирование
        AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_permissions_update',
            'description' => "Обновлены разрешения для роли '{$role->display_name}'",
            'model_type' => Role::class,
            'model_id' => $role->id,
            'old_values' => ['permissions' => $oldPermissions],
            'new_values' => ['permissions' => $newPermissions],
        ]);

        return redirect()->route('admin.roles.permissions', $role)
            ->with('success', 'Разрешения успешно обновлены');
    }

    /**
     * Создание сотрудника.
     */
    public function createUser(Request $request)
    {
        // Проверка прав
        if (!$this->securityService->canCreateUser(auth()->user())) {
            abort(403, 'Доступ запрещён');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|min:6',
            'full_name' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id|in:2,3,4', // Admin, Seller, Warehouse
            'shop_id' => 'nullable|exists:shops,id',
            'is_active' => 'nullable',
        ]);

        \Log::info('Создание сотрудника', [
            'data' => $validated,
            'shop_id' => $request->input('shop_id'),
            'is_active' => $request->input('is_active'),
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'full_name' => $validated['full_name'] ?? null,
            'role_id' => $validated['role_id'],
            'shop_id' => $validated['shop_id'] ?? null,
            'is_active' => $request->has('is_active') || ($request->input('is_active') !== null),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_create',
            'description' => "Создан сотрудник {$user->username}",
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        // Audit Log
        AdminAuditLog::logAction(
            auth()->user(),
            AdminAuditLog::ACTION_USER_CREATE,
            $user,
            null,
            [
                'username' => $user->username,
                'email' => $user->email,
                'role_id' => $user->role_id,
            ]
        );

        // Security Log
        $role = Role::find($user->role_id);
        $this->securityLogger->logAdminUserCreate(
            auth()->user(),
            $user->id,
            $user->username,
            $role?->name ?? 'unknown'
        );

        return redirect()->route('admin.users.index')->with('success', 'Сотрудник создан');
    }

    /**
     * Редактирование сотрудника.
     */
    public function editUser(User $user)
    {
        $roles = Role::whereIn('id', [2, 3, 4])->get();
        $shops = Shop::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'roles', 'shops'));
    }

    /**
     * Обновление сотрудника.
     */
    public function updateUser(Request $request, User $user)
    {
        // Приводим пустые строки от формы к null, чтобы `nullable|exists` работали корректно.
        $request->merge([
            'shop_id' => $request->input('shop_id') === '' ? null : $request->input('shop_id'),
            'email' => $request->input('email') === '' ? null : $request->input('email'),
            'phone' => $request->input('phone') === '' ? null : $request->input('phone'),
            'full_name' => $request->input('full_name') === '' ? null : $request->input('full_name'),
        ]);

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'password' => 'nullable|min:6',
            'full_name' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id|in:2,3,4',
            'shop_id' => 'nullable|exists:shops,id',
            'is_active' => 'boolean',
        ]);

        $data = [
            'username' => $validated['username'],
            // email может быть nullable, но чтобы случайно не перезаписать его на null —
            // используем значение из запроса только если поле реально пришло.
            'email' => $request->has('email') ? $validated['email'] : $user->email,
            'phone' => $validated['phone'],
            'full_name' => $validated['full_name'],
            'role_id' => $validated['role_id'],
            'shop_id' => $validated['shop_id'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_update',
            'description' => "Обновлен сотрудник {$user->username}",
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Сотрудник обновлён');
    }

    /**
     * Удаление сотрудника.
     */
    public function deleteUser(User $user)
    {
        // Проверка прав
        if (!$this->securityService->canDeleteUser(auth()->user(), $user)) {
            abort(403, 'Доступ запрещён');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Нельзя удалить самого себя');
        }

        // Проверка: есть ли продажи
        $hasSales = Transaction::where('user_id', $user->id)->exists();

        if ($hasSales) {
            $user->update(['is_active' => false]);
            $message = "Сотрудник деактивирован (есть история продаж)";
        } else {
            $user->delete();
            $message = "Сотрудник удалён";
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_delete',
            'description' => "{$message}: {$user->username}",
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        // Audit Log
        AdminAuditLog::logAction(
            auth()->user(),
            AdminAuditLog::ACTION_USER_DELETE,
            $user,
            [
                'username' => $user->username,
                'email' => $user->email,
                'role_id' => $user->role_id,
            ],
            ['deleted' => true]
        );

        // Security Log
        $this->securityLogger->logAdminUserDelete(
            auth()->user(),
            $user->id,
            $user->username,
            $user->role?->name ?? 'unknown'
        );

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    // ========================================================================
    // 🏪 УПРАВЛЕНИЕ МАГАЗИНАМИ
    // ========================================================================

    /**
     * Список магазинов.
     */
    public function shops()
    {
        $shops = Shop::withCount(['transactions'])
            ->get();

        // Основной магазин (в seed лежит KARAKOL_MAIN, на всякий случай ищем и по шаблону)
        $mainShop = Shop::where('code', 'KARAKOL_MAIN')->first()
            ?? Shop::where('code', 'like', '%_MAIN')->first();

        $stats = [
            'total' => Shop::count(),
            'active' => Shop::where('is_active', true)->count(),
            'employees' => User::whereIn('role_id', [2, 3])->count(),
            'total_value' => DB::table('stock')
                ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
                ->join('shops', 'stock.shop_id', '=', 'shops.id')
                ->sum(DB::raw('IFNULL(product_variants.supplier_price, 0) * stock.quantity')),
        ];

        return view('admin.shops.index', compact('shops', 'stats', 'mainShop'));
    }

    /**
     * Создание магазина.
     */
    public function createShop(Request $request)
    {
        try {
            // Проверка прав
            if (!$this->securityService->canManageShops(auth()->user())) {
                abort(403, 'Доступ запрещён');
            }

            // Сначала обрабатываем is_active вручную до валидации
            $isActive = false;
            if ($request->has('is_active')) {
                $isActive = $request->input('is_active') === 'on' || $request->input('is_active') === '1' || $request->input('is_active') === 1 || $request->input('is_active') === true;
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:shops,code',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
            ]);
            
            $validated['is_active'] = $isActive;

            $shop = Shop::create($validated);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'shop_create',
                'description' => "Создан магазин {$shop->name}",
                'model_type' => Shop::class,
                'model_id' => $shop->id,
            ]);

            // Audit Log
            AdminAuditLog::logAction(
                auth()->user(),
                AdminAuditLog::ACTION_SHOP_CREATE,
                $shop,
                null,
                ['name' => $shop->name, 'code' => $shop->code]
            );

            // Security Log
            $this->securityLogger->logAdminShopManage(
                auth()->user(),
                $shop->id,
                $shop->name,
                'create'
            );

            return redirect()->route('admin.shops.index')->with('success', 'Магазин создан');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ошибка при создании магазина: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Обновление магазина.
     */
    public function updateShop(Request $request, Shop $shop)
    {
        try {
            // Сначала обрабатываем is_active вручную до валидации
            $isActive = false;
            if ($request->has('is_active')) {
                $isActive = $request->input('is_active') === 'on' || $request->input('is_active') === '1' || $request->input('is_active') === 1 || $request->input('is_active') === true;
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:shops,code,' . $shop->id,
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
            ]);
            
            $validated['is_active'] = $isActive;

            $shop->update($validated);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'shop_update',
                'description' => "Обновлен магазин {$shop->name}",
                'model_type' => Shop::class,
                'model_id' => $shop->id,
            ]);

            return redirect()->route('admin.shops.index')->with('success', 'Магазин обновлён');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ошибка при обновлении магазина: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Удаление магазина.
     */
    public function deleteShop(Request $request, Shop $shop)
    {
        // Проверка прав
        if (!$this->securityService->canManageShops(auth()->user())) {
            abort(403, 'Доступ запрещён');
        }

        $mainShop = Shop::where('code', 'KARAKOL_MAIN')->first()
            ?? Shop::where('code', 'like', '%_MAIN')->first();

        $isMainShop = $mainShop && (int)$mainShop->id === (int)$shop->id;

        // Если основной магазин — запретим обычное удаление,
        // разрешим только с переносом на другой магазин.
        if ($isMainShop) {
            $targetShopId = $request->input('transfer_to_shop_id');
            if (empty($targetShopId)) {
                return redirect()->route('admin.shops.index')->with('error', 'Нельзя удалить основной магазин без переноса на новый.');
            }

            $targetShop = Shop::find($targetShopId);
            if (!$targetShop || (int)$targetShop->id === (int)$shop->id) {
                return redirect()->route('admin.shops.index')->with('error', 'Выбран некорректный магазин для переноса.');
            }

            DB::beginTransaction();
            try {
                // Перенос сотрудников
                User::where('shop_id', $shop->id)->update(['shop_id' => $targetShop->id]);

                // Перенос остатков (склады в этом проекте не трогаем, только shop_id)
                $oldAggregated = DB::table('stock')
                    ->where('shop_id', $shop->id)
                    ->select(
                        'variant_id',
                        'warehouse_id',
                        DB::raw('SUM(quantity) as quantity'),
                        DB::raw('SUM(reserved) as reserved'),
                        DB::raw('MAX(min_stock) as min_stock')
                    )
                    ->groupBy('variant_id', 'warehouse_id')
                    ->get();

                $targetStocks = DB::table('stock')
                    ->where('shop_id', $targetShop->id)
                    ->select('id', 'variant_id', 'warehouse_id', 'quantity', 'reserved', 'min_stock')
                    ->get();

                $targetMap = [];
                foreach ($targetStocks as $ts) {
                    $key = $ts->variant_id . ':' . ($ts->warehouse_id ?? 'null');
                    $targetMap[$key] = $ts;
                }

                $now = now();
                foreach ($oldAggregated as $row) {
                    $key = $row->variant_id . ':' . ($row->warehouse_id ?? 'null');
                    if (isset($targetMap[$key])) {
                        $existing = $targetMap[$key];
                        DB::table('stock')->where('id', $existing->id)->update([
                            'quantity' => (int)$existing->quantity + (int)$row->quantity,
                            'reserved' => (int)$existing->reserved + (int)$row->reserved,
                            'min_stock' => max((int)$existing->min_stock, (int)$row->min_stock),
                            'updated_at' => $now,
                        ]);
                    } else {
                        DB::table('stock')->insert([
                            'variant_id' => $row->variant_id,
                            'shop_id' => $targetShop->id,
                            'warehouse_id' => $row->warehouse_id,
                            'quantity' => (int)$row->quantity,
                            'reserved' => (int)$row->reserved,
                            'min_stock' => (int)$row->min_stock,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }

                // Удаляем старые остатки основного магазина
                DB::table('stock')->where('shop_id', $shop->id)->delete();

                // Деактивируем основной магазин после переноса
                // Если у основного магазина нет транзакций, можно удалить запись.
                // Если транзакции есть — только деактивация, чтобы не потерять историю.
                $hasTransactionsAfter = Transaction::where('shop_id', $shop->id)->exists();
                if ($hasTransactionsAfter) {
                    $shop->update(['is_active' => false]);
                } else {
                    $shop->delete();
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('admin.shops.index')->with('error', 'Ошибка при переносе: ' . $e->getMessage());
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'shop_delete_main_transferred',
                'description' => "Основной магазин {$shop->name} деактивирован. Перенос -> {$targetShop->name}",
                'model_type' => Shop::class,
                'model_id' => $shop->id,
            ]);

            $deletedOrDeactivatedMessage = $shop->fresh()
                ? 'Основной магазин деактивирован и данные перенесены на новый.'
                : 'Основной магазин удалён и данные перенесены на новый.';

            return redirect()->route('admin.shops.index')->with('success', $deletedOrDeactivatedMessage);
        }

        // Проверка на связанные данные
        $hasStock = Stock::where('shop_id', $shop->id)->exists();
        $hasTransactions = Transaction::where('shop_id', $shop->id)->exists();
        $hasUsers = User::where('shop_id', $shop->id)->exists();

        if ($hasStock || $hasTransactions || $hasUsers) {
            $shop->update(['is_active' => false]);
            $message = "Магазин деактивирован (есть связанные данные)";
        } else {
            $shop->delete();
            $message = "Магазин удалён";
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'shop_delete',
            'description' => "{$message}: {$shop->name}",
            'model_type' => Shop::class,
            'model_id' => $shop->id,
        ]);

        // Audit Log
        AdminAuditLog::logAction(
            auth()->user(),
            AdminAuditLog::ACTION_SHOP_DELETE,
            $shop,
            ['name' => $shop->name, 'code' => $shop->code],
            ['deleted' => true]
        );

        // Security Log
        $this->securityLogger->logAdminShopManage(
            auth()->user(),
            $shop->id,
            $shop->name,
            'delete'
        );

        return redirect()->route('admin.shops.index')->with('success', $message);
    }

    // ========================================================================
    // 🎁 КУПОНЫ И КЛИЕНТЫ
    // ========================================================================

    /**
     * Проверка телефона (AJAX).
     */
    public function checkPhone(Request $request)
    {
        $exists = User::where('phone', $request->phone)->where('role_id', 5)->exists();
        
        return response()->json([
            'exists' => $exists,
        ]);
    }

    /**
     * Список всех клиентов.
     */
    public function customers(Request $request)
    {
        // Получаем ВСЕХ клиентов (роль = 5)
        $query = User::where('role_id', 5)
            ->with(['customerProfile', 'myCoupons' => function($q) {
                $q->with('qrCode')->orderBy('created_at', 'desc');
            }]);

        // Поиск по телефону или ФИО
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);

        // Декодируем qr_data для каждого купона
        foreach ($customers as $customer) {
            foreach ($customer->myCoupons as $coupon) {
                if ($coupon->qrCode && is_string($coupon->qrCode->qr_data)) {
                    $coupon->qrCode->qr_data = json_decode($coupon->qrCode->qr_data, true);
                }
            }
        }

        $stats = [
            'total' => User::where('role_id', 5)->count(),
            'with_coupons' => User::where('role_id', 5)->has('myCoupons')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * Создание купона.
     */
    public function createCoupon(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
            'discount_type' => 'required|in:percent,wholesale',
            'discount_value' => 'nullable|numeric|min:0',
            'password' => 'nullable|string|min:6',
            'validity_period' => 'nullable|numeric',
            'valid_until' => 'nullable|date|after:today',
        ]);

        // Поиск или создание клиента
        $customer = User::where('phone', $validated['phone'])->first();
        $generatedPassword = null;
        $isNewCustomer = false;

        if (!$customer) {
            // Новый клиент - пароль обязателен
            if (empty($validated['password'])) {
                return back()->withErrors(['password' => 'Укажите пароль для нового клиента'])->withInput();
            }
            
            $isNewCustomer = true;
            $generatedPassword = $validated['password'];

            $customer = User::create([
                'username' => 'client_' . time(),
                'phone' => $validated['phone'],
                'full_name' => $validated['full_name'],
                'password' => Hash::make($generatedPassword),
                'role_id' => 5, // Client
                'is_active' => true,
            ]);

            CustomerProfile::create(['user_id' => $customer->id]);
        } else {
            // Обновляем ФИО клиента если указано
            if (!empty($validated['full_name'])) {
                $customer->update(['full_name' => $validated['full_name']]);
            }
        }

        // Вычисляем дату окончания действия
        $validUntil = null;
        if ($validated['validity_period'] == '999') {
            $validUntil = now()->addYear(10); // Бессрочно (10 лет)
        } elseif ($validated['validity_period'] == 'custom' && !empty($validated['valid_until'])) {
            $validUntil = \Carbon\Carbon::parse($validated['valid_until']);
        } else {
            $months = (int)($validated['validity_period'] ?? 12);
            $validUntil = now()->addMonths($months);
        }

        // Создание купона
        $coupon = Coupon::create([
            'code' => 'CPN-' . strtoupper(uniqid()),
            'customer_id' => $customer->id,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'] ?? 0,
            'usage_limit' => 999999, // Бессрочный (до истечения срока действия)
            'usage_count' => 0,
            'valid_from' => now(),
            'valid_until' => $validUntil,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        // Генерация QR-кода для купона
        $qrData = json_encode(['coupon_id' => $coupon->id, 'code' => $coupon->code]);
        \App\Models\QrCode::create([
            'variant_id' => null,
            'coupon_id' => $coupon->id,
            'qr_data' => $qrData,
            'is_printed' => false,
        ]);

        $message = $isNewCustomer 
            ? 'Клиент зарегистрирован, купон создан' 
            : 'Купон создан';

        return redirect()->route('admin.customers.index')
            ->with('success', $message)
            ->with('generated_password', $isNewCustomer ? $generatedPassword : null)
            ->with('customer_phone', $customer->phone);
    }

    /**
     * Обновление купона.
     */
    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,wholesale',
            'discount_value' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
            'valid_until' => 'nullable|date|after:today',
        ]);

        // Обновление данных клиента
        if ($coupon->customer) {
            $coupon->customer->update([
                'full_name' => $validated['full_name'] ?? $coupon->customer->full_name,
            ]);
        }

        // Обновление купона
        $updateData = [
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'] ?? 0,
            'is_active' => (bool)$validated['is_active'],
        ];

        // Обновляем срок действия если указан
        if (!empty($validated['valid_until'])) {
            $updateData['valid_until'] = \Carbon\Carbon::parse($validated['valid_until']);
        }

        $coupon->update($updateData);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Купон обновлён']);
        }
        return redirect()->route('admin.customers.index')->with('success', 'Купон обновлён');
    }

    /**
     * Блокировка купона.
     */
    public function toggleBlockCoupon(Coupon $coupon)
    {
        $coupon->update(['is_active' => false]);
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Купон заблокирован']);
        }
        return redirect()->route('admin.customers.index')->with('success', 'Купон заблокирован');
    }

    /**
     * Разблокировка купона.
     */
    public function unblockCoupon(Coupon $coupon)
    {
        $coupon->update(['is_active' => true]);
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Купон разблокирован']);
        }
        return redirect()->route('admin.customers.index')->with('success', 'Купон разблокирован');
    }

    /**
     * Сброс пароля клиента.
     */
    public function resetPassword($customerId)
    {
        $customer = User::findOrFail($customerId);

        // Генерируем новый пароль
        $password = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        $customer->update([
            'password' => Hash::make($password),
        ]);

        return response()->json([
            'success' => true,
            'password' => $password,
            'message' => 'Пароль сброшен'
        ]);
    }

    /**
     * Удаление клиента полностью из базы.
     */
    public function deleteCustomer(User $customer)
    {
        // Проверяем что это клиент (роль 5)
        if ($customer->role_id != 5) {
            return response()->json(['success' => false, 'message' => 'Можно удалять только клиентов'], 403);
        }

        // Удаляем все купоны клиента
        $customer->myCoupons()->delete();

        // Удаляем профиль клиента
        $customer->customerProfile()->delete();

        // Удаляем самого клиента
        $customer->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'customer_delete',
            'description' => 'Удалён клиент: ' . ($customer->full_name ?? $customer->phone),
        ]);

        return response()->json(['success' => true, 'message' => 'Клиент удалён']);
    }

    /**
     * Удаление купона.
     */
    public function deleteCoupon(Coupon $coupon)
    {
        $coupon->delete();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Купон удалён']);
        }
        return redirect()->route('admin.customers.index')->with('success', 'Купон удалён');
    }

    // ========================================================================
    // 📊 ОТЧЁТЫ
    // ========================================================================

    /**
     * Страница отчётов.
     */
    public function reports()
    {
        return view('admin.reports.index');
    }

    /**
     * Отчёт по продажам.
     */
    public function salesReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $shopId = $request->get('shop_id');

        $query = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['user', 'shop', 'items']);

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(50);

        $totalAmount = $sales->sum(function($sale) {
            return $sale->total_amount ?? 0;
        });
        $totalTransactions = $sales->total();

        $shops = Shop::where('is_active', true)->get();

        return view('admin.reports.sales', compact('sales', 'totalAmount', 'totalTransactions', 'shops', 'dateFrom', 'dateTo'));
    }

    /**
     * Отчёт по товарам.
     */
    public function productsReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $products = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('transaction_items', 'product_variants.id', '=', 'transaction_items.variant_id')
            ->leftJoin('transactions', fn($j) => $j->on('transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]))
            ->selectRaw('products.id, products.name, products.article,
                COALESCE(SUM(transaction_items.quantity), 0) as sold_qty,
                COALESCE(SUM(transaction_items.price * transaction_items.quantity), 0) as revenue')
            ->groupBy('products.id')
            ->orderBy('sold_qty', 'desc')
            ->paginate(50);

        return view('admin.reports.products', compact('products', 'dateFrom', 'dateTo'));
    }

    /**
     * Отчёт по складу.
     */
    public function stockReport()
    {
        $stock = DB::table('stock')
            ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('shops', 'stock.shop_id', '=', 'shops.id')
            ->leftJoin('warehouses', 'stock.warehouse_id', '=', 'warehouses.id')
            ->selectRaw('products.name, product_variants.size, products.retail_price,
                SUM(CASE WHEN stock.shop_id IS NOT NULL THEN stock.quantity ELSE 0 END) as shop_quantity,
                SUM(CASE WHEN stock.warehouse_id IS NOT NULL THEN stock.quantity ELSE 0 END) as warehouse_quantity,
                SUM(stock.quantity) as total_quantity,
                (products.retail_price * SUM(stock.quantity)) as total_value')
            ->groupBy('products.id', 'product_variants.id', 'product_variants.size', 'products.retail_price', 'products.name')
            ->orderBy('products.name')
            ->paginate(50);

        $totalValue = DB::table('stock')
            ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->sum(DB::raw('products.retail_price * stock.quantity'));

        return view('admin.reports.stock', compact('stock', 'totalValue'));
    }

    // ========================================================================
    // ⚙️ НАСТРОЙКИ
    // ========================================================================

    /**
     * Страница настроек.
     */
    public function settings()
    {
        $settings = [
            'qr_auto_generate' => Setting::get('qr_auto_generate', true),
            'shop_name' => Setting::get('shop_name', 'NURMAN'),
        ];

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.default'),
            'app_url' => config('app.url'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'post_max_size' => ini_get('post_max_size'),
        ];

        return view('admin.settings', compact('settings', 'systemInfo'));
    }

    /**
     * Сохранение настроек.
     */
    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'qr_auto_generate' => 'nullable|in:0,1',
            'shop_name' => 'string|max:255',
        ]);

        // Сохраняем настройки в базу данных
        Setting::set('qr_auto_generate', $request->input('qr_auto_generate', '0') === '1');
        Setting::set('shop_name', $request->input('shop_name', 'NURMAN'));

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'settings_update',
            'description' => 'Обновлены системные настройки',
        ]);

        return back()->with('success', 'Настройки сохранены');
    }

    // ========================================================================
    // 👤 ПРОФИЛЬ ПОЛЬЗОВАТЕЛЯ
    // ========================================================================

    /**
     * Страница профиля пользователя.
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Обновление профиля пользователя.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|max:2048', // до 2MB
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Загрузка аватара
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Обновление данных
        if ($request->filled('full_name')) {
            $user->full_name = $validated['full_name'];
        }
        
        if ($request->filled('email')) {
            $user->email = $validated['email'];
        }
        
        if ($request->filled('phone')) {
            $user->phone = $validated['phone'];
        }
        
        // Смена пароля
        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'profile_update',
            'description' => 'Обновлён профиль пользователя',
        ]);

        return back()->with('success', 'Профиль обновлён');
    }

    // ========================================================================
    // 📋 ЖУРНАЛ ДЕЙСТВИЙ
    // ========================================================================

    /**
     * Просмотр журнала действий.
     */
    public function activityLog(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(50);
        $users = User::all();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.activity-log', compact('logs', 'users', 'actions'));
    }

    // ========================================================================
    // 🔒 AUDIT LOG - ЖУРНАЛ АУДИТА АДМИНИСТРАТОРА
    // ========================================================================

    /**
     * Просмотр журнала аудита действий администратора.
     */
    public function auditLog(Request $request)
    {
        $query = AdminAuditLog::with(['user', 'user.role'])
            ->orderBy('created_at', 'desc');

        // Фильтр по пользователю
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Фильтр по действию
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Фильтр по модели
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Фильтр по дате
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Фильтр по IP
        if ($request->filled('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        $auditLogs = $query->paginate(50);

        // Статистика
        $stats = [
            'total' => AdminAuditLog::count(),
            'today' => AdminAuditLog::whereDate('created_at', today())->count(),
            'this_week' => AdminAuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => AdminAuditLog::whereMonth('created_at', now()->month)->count(),
        ];

        // Уникальные действия для фильтра
        $actions = AdminAuditLog::select('action')->distinct()->pluck('action');

        // Пользователи для фильтра
        $users = User::whereIn('role_id', [2, 3, 4])->get();

        // Типы моделей
        $modelTypes = AdminAuditLog::select('model_type')
            ->distinct()
            ->whereNotNull('model_type')
            ->pluck('model_type');

        return view('admin.audit-log', compact('auditLogs', 'stats', 'actions', 'users', 'modelTypes'));
    }

    /**
     * Страница сканера товаров (QR/Штрих-коды)
     */
    public function scanProduct()
    {
        return view('admin.scan-product');
    }

    /**
     * Обработка сканирования QR-кода или штрих-кода товара
     */
    public function processProductScan(Request $request)
    {
        $request->validate([
            'qr_data' => ['required', 'string', 'max:255'],
        ]);

        $qrData = trim($request->qr_data);

        \Log::info('Admin QR/Barcode Scan received: ' . $qrData);

        $product = null;
        $foundBy = null;

        try {
            $parsed = json_decode($qrData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                if (isset($parsed['article'])) {
                    $article = $parsed['article'];
                    \Log::info('Extracted article from JSON: ' . $article);
                    $product = Product::where('article', $article)->first();
                    $foundBy = 'json_article';
                }

                if (!$product && isset($parsed['barcode'])) {
                    $barcode = $parsed['barcode'];
                    \Log::info('Trying barcode from JSON: ' . $barcode);
                    $variant = ProductVariant::where('barcode', $barcode)->first();
                    if ($variant) {
                        $product = $variant->product;
                        $foundBy = 'json_barcode';
                    }
                }
            } else {
                $product = Product::where('article', $qrData)->first();
                if ($product) {
                    $foundBy = 'article';
                    \Log::info('Found by article: ' . $qrData);
                } else {
                    $variant = ProductVariant::where('barcode', $qrData)->first();
                    if ($variant) {
                        $product = $variant->product;
                        $foundBy = 'barcode';
                        \Log::info('Found by barcode: ' . $qrData);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('JSON parse error: ' . $e->getMessage());

            $product = Product::where('article', $qrData)->first();
            if ($product) {
                $foundBy = 'article';
            } else {
                $variant = ProductVariant::where('barcode', $qrData)->first();
                if ($variant) {
                    $product = $variant->product;
                    $foundBy = 'barcode';
                }
            }
        }

        if (!$product) {
            \Log::warning('Product not found by: ' . $qrData);

            return response()->json([
                'success' => false,
                'message' => 'Товар не найден',
                'scanned_data' => $qrData,
                'searched_by' => $foundBy,
            ]);
        }

        \Log::info('Product found: ' . $product->name . ' (found by: ' . $foundBy . ')');

        // Получаем варианты с остатками
        $variants = $product->variants()->with(['stock'])->get()->map(function ($variant) {
            $totalStock = $variant->stock->sum('quantity');
            $shopStock = $variant->stock->filter(fn($s) => $s->shop_id)->sum('quantity');
            $warehouseStock = $variant->stock->filter(fn($s) => $s->warehouse_id)->sum('quantity');

            return [
                'id' => $variant->id,
                'size' => $variant->size,
                'color' => $variant->color,
                'barcode' => $variant->barcode,
                'total_stock' => $totalStock,
                'shop_stock' => $shopStock,
                'warehouse_stock' => $warehouseStock,
            ];
        });

        // Проверяем есть ли QR в системе
        $variantIds = $product->variants()->pluck('id');
        $isKnownQr = QrCode::whereIn('variant_id', $variantIds)->exists();

        // Считаем остатки
        $totalStock = $variants->sum('total_stock');
        $shopStock = $variants->sum('shop_stock');
        $warehouseStock = $variants->sum('warehouse_stock');

        // Считаем проданные
        $totalSold = TransactionItem::whereIn('variant_id', $variantIds)->sum('quantity');

        return response()->json([
            'success' => true,
            'is_known_qr' => $isKnownQr,
            'found_by' => $foundBy,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'article' => $product->article,
                'photo' => $product->photo,
                'retail_price' => $product->retail_price,
                'wholesale_price' => $product->wholesale_price,
                'supplier_price' => $product->supplier_price,
                'variants' => $variants,
            ],
            'summary' => [
                'total_stock' => $totalStock,
                'shop_stock' => $shopStock,
                'warehouse_stock' => $warehouseStock,
                'total_sold' => $totalSold,
            ],
        ]);
    }
}
