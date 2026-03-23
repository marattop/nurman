<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
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

        // Товары с низким остатком
        $lowStockProducts = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('stock', 'product_variants.id', '=', 'stock.variant_id')
            ->join('shops', 'stock.shop_id', '=', 'shops.id')
            ->where('stock.quantity', '<=', 'stock.min_stock')
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
            'stats', 'recentProducts', 'lowStockProducts', 'qrAutoGenerate', 'roles'
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

        return view('admin.products.create', compact('categories', 'shops', 'warehouses'));
    }

    /**
     * Сохранение товара.
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'article' => 'nullable|string|max:100|unique:products,article',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'retail_price' => 'required|numeric|min:0',
            'supplier_price' => 'nullable|numeric|min:0',
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
            'variants.*.price' => 'nullable|numeric|min:0',
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
        $supplierPrice = $request->input('supplier_price', 0);
        foreach ($validated['variants'] as $variantData) {
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'size' => $variantData['size'],
                'color' => $variantData['color'] ?? null,
                'sku' => $validated['article'] . '-' . strtoupper($variantData['size']),
                'supplier_price' => $variantData['supplier_price'] ?? $supplierPrice ?? 0,
                'wholesale_price' => $variantData['wholesale_price'] ?? null,
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

        return view('admin.products.edit', compact('product', 'categories', 'shops', 'warehouses'));
    }

    /**
     * Обновление товара.
     */
    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'article' => 'required|string|max:100|unique:products,article,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'retail_price' => 'required|numeric|min:0',
            'supplier_price' => 'nullable|numeric|min:0',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
            'camera_photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
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

        // Обновление цены закупки для всех вариантов
        if ($request->filled('supplier_price')) {
            foreach ($product->variants as $variant) {
                $variant->update(['supplier_price' => $request->supplier_price]);
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_update',
            'description' => "Обновлен товар {$oldName}",
            'model_type' => Product::class,
            'model_id' => $product->id,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Товар обновлён');
    }

    /**
     * Удаление/деактивация товара.
     */
    public function deleteProduct(Product $product)
    {
        // Проверка: есть ли продажи
        $hasSales = DB::table('transaction_items')
            ->join('product_variants', 'transaction_items.variant_id', '=', 'product_variants.id')
            ->where('product_variants.product_id', $product->id)
            ->exists();

        if ($hasSales) {
            // Деактивация вместо удаления
            $product->update(['is_active' => false]);
            $message = "Товар деактивирован (есть история продаж)";
        } else {
            $product->delete();
            $message = "Товар удалён";
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_delete',
            'description' => "{$message}: {$product->name}",
            'model_type' => Product::class,
            'model_id' => $product->id,
        ]);

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
        $qrData = $article;

        // Проверяем существующий QR
        $existingQr = QrCode::where('variant_id', $variant->id)->first();
        if ($existingQr) {
            // Обновляем существующий с новым артикулом
            $existingQr->update([
                'qr_data' => json_encode(['article' => $qrData]),
                'is_printed' => false,
            ]);
            return true;
        }

        // Создаем новый QR с пустым путем (будет сгенерирован при печати)
        QrCode::create([
            'variant_id' => $variant->id,
            'qr_code_path' => '', // Пустая строка вместо NULL
            'qr_data' => json_encode(['article' => $qrData]),
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
     * Создание сотрудника.
     */
    public function createUser(Request $request)
    {
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
            'email' => $validated['email'],
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
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя удалить самого себя');
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

        return back()->with('success', $message);
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

        $stats = [
            'total' => Shop::count(),
            'active' => Shop::where('is_active', true)->count(),
            'employees' => User::whereIn('role_id', [2, 3])->count(),
            'total_value' => DB::table('stock')
                ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
                ->join('shops', 'stock.shop_id', '=', 'shops.id')
                ->sum(DB::raw('IFNULL(product_variants.supplier_price, 0) * stock.quantity')),
        ];

        return view('admin.shops.index', compact('shops', 'stats'));
    }

    /**
     * Создание магазина.
     */
    public function createShop(Request $request)
    {
        try {
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
    public function deleteShop(Shop $shop)
    {
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

        return back()->with('success', $message);
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

        return redirect()->route('admin.customers.index')->with('success', 'Купон обновлён');
    }

    /**
     * Блокировка/разблокировка купона.
     */
    public function toggleBlockCoupon(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $action = $coupon->is_active ? 'разблокирован' : 'заблокирован';
        return redirect()->route('admin.customers.index')->with('success', "Купон {$action}");
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
     * Удаление купона.
     */
    public function deleteCoupon(Coupon $coupon)
    {
        $coupon->delete();
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
            ->with(['user', 'shop', 'items.variant.product']);

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(50);

        $totalAmount = $sales->sum('total_amount');
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
            ->selectRaw('products.name, product_variants.size, shops.name as shop_name,
                stock.quantity, stock.min_stock, products.retail_price,
                (products.retail_price * stock.quantity) as total_value')
            ->orderBy('shop_name')
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
}
