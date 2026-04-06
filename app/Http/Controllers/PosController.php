<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Stock;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    /**
     * POS-терминал - главная страница
     */
    public function index()
    {
        $user = auth()->user();

        // Если у продавца нет магазина, показываем ошибку
        if (!$user->shop_id) {
            abort(403, 'У вас нет привязки к магазину. Обратитесь к администратору.');
        }

        $categories = Category::where('is_active', true)->get();
        $products = Product::with(['variants.stock', 'category'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Загружаем остатки для каждого варианта
        $shopId = $user->shop_id;
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $variant->stock_quantity = 0;
                if ($variant->stock) {
                    $stock = $variant->stock->firstWhere('shop_id', $shopId);
                    if ($stock) {
                        $variant->stock_quantity = $stock->quantity - $stock->reserved;
                    }
                }
            }
        }

        return view('seller.pos.index', compact('categories', 'products'));
    }

    /**
     * Проверка прав продавца
     */
    public function checkPermissions()
    {
        $user = auth()->user();
        
        return response()->json([
            'success' => true,
            'can_sell_from_warehouse' => (bool) $user->can_sell_from_warehouse,
        ]);
    }

    /**
     * Загрузка товаров с учётом источника
     */
    public function getProducts(Request $request)
    {
        $source = $request->get('source', 'showcase');
        $shopId = auth()->user()->shop_id;

        // Продавец может продавать только товары с витрины (shop_id)
        // Товары со склада (warehouse_id) недоступны для продажи
        $query = Product::with(['variants' => function($q) use ($shopId) {
            // Загружаем только остатки по этому магазину (витрина)
            $q->with(['stock' => function($qs) use ($shopId) {
                $qs->where('shop_id', $shopId);
            }]);
        }, 'category'])
            ->where('is_active', true);

        // Фильтруем товары, которые есть на витрине этого магазина
        $query->whereHas('variants', function($q) use ($shopId) {
            $q->whereHas('stock', function($qs) use ($shopId) {
                $qs->where('shop_id', $shopId)
                   ->whereRaw('quantity - reserved > 0');
            });
        });

        $products = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'products' => $products,
            'source' => 'showcase', // Всегда showcase для продавца
        ]);
    }

    /**
     * Поиск товаров по названию или артикулу
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        
        $products = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('article', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    /**
     * Товары по категории
     */
    public function productsByCategory($categoryId)
    {
        $products = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->where('category_id', $categoryId)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    /**
     * Сканирование QR-кода клиента
     */
    public function scanCustomer(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);
            $customerId = null;
            $phone = null;
            $couponId = null;
            $couponCode = null;
            $customer = null;

            \Log::info('scanCustomer - Входные данные:', [
                'qr_data' => $request->qr_data,
                'decoded' => $qrData
            ]);

            // Формат QR: coupon_id:code (например, "5:CPN-12345")
            if (is_string($request->qr_data) && strpos($request->qr_data, ':') !== false) {
                $parts = explode(':', $request->qr_data);
                if (count($parts) === 2 && is_numeric($parts[0])) {
                    $couponId = (int) $parts[0];
                    $couponCode = $parts[1];
                    \Log::info('scanCustomer - Обнаружен формат coupon_id:code', [
                        'couponId' => $couponId,
                        'couponCode' => $couponCode
                    ]);
                }
            }

            if (is_array($qrData)) {
                $customerId = $qrData['customer_id'] ?? null;
                $phone = $qrData['phone'] ?? null;
                $couponCode = $couponCode ?? ($qrData['code'] ?? null);
                $couponId = $couponId ?? ($qrData['coupon_id'] ?? null);
            } else {
                // Если не JSON, возможно это номер телефона или ID напрямую
                if (is_numeric($request->qr_data)) {
                    if (strlen($request->qr_data) >= 9) {
                        $phone = $request->qr_data;
                    } else {
                        $customerId = $request->qr_data;
                    }
                }
            }

            $query = User::with(['customerProfile', 'myCoupons' => function($q) {
                $q->where('is_active', true)
                  ->where(function($query) {
                      $query->whereNull('valid_from')
                            ->orWhere('valid_from', '<=', now());
                      $query->whereNull('valid_until')
                            ->orWhere('valid_until', '>=', now());
                  });
            }]);

            \Log::info('scanCustomer - Поиск:', [
                'customerId' => $customerId,
                'phone' => $phone,
                'couponId' => $couponId,
                'couponCode' => $couponCode
            ]);

            // Поиск по coupon_id:code формату
            if ($couponId && $couponCode) {
                $coupon = Coupon::where('id', $couponId)
                    ->where('code', $couponCode)
                    ->where('is_active', true)
                    ->first();
                \Log::info('scanCustomer - Поиск по coupon_id:code', [
                    'found' => $coupon ? true : false,
                    'couponId' => $couponId,
                    'couponCode' => $couponCode
                ]);
                if ($coupon && $coupon->customer_id) {
                    $customer = $query->find($coupon->customer_id);
                    \Log::info('scanCustomer - Клиент найден по coupon_id:code', ['customerId' => $customer->id]);
                }
            }

            // Поиск по ID клиента
            if (!$customer && $customerId) {
                $customer = $query->find($customerId);
                \Log::info('scanCustomer - Поиск по ID:', ['found' => $customer ? true : false]);
            }

            // Поиск по телефону
            if (!$customer && $phone) {
                // Очистка телефона от лишних символов
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $customer = $query->where('phone', 'like', "%{$cleanPhone}%")->first();
                \Log::info('scanCustomer - Поиск по телефону:', ['cleanPhone' => $cleanPhone, 'found' => $customer ? true : false]);
            }

            // Поиск по коду купона (если только код)
            if (!$customer && $couponCode) {
                $coupon = Coupon::where('code', $couponCode)
                    ->where('is_active', true)
                    ->first();
                if ($coupon && $coupon->customer_id) {
                    $customer = $query->find($coupon->customer_id);
                }
                \Log::info('scanCustomer - Поиск по коду купона:', ['found' => $customer ? true : false]);
            }

            if (!$customer) {
                // Логируем всех клиентов для отладки
                $allCustomers = User::where('role_id', 5)->get(['id', 'full_name', 'phone']);
                \Log::error('scanCustomer - Клиент не найден. Все клиенты в БД:', [
                    'customers' => $allCustomers->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Клиент не найден'
                ]);
            }

            // Получаем активный купон клиента
            $activeCoupon = $customer->myCoupons->first();

            // Получаем процент лояльности клиента
            $loyaltyDiscountPercent = $customer->customerProfile?->loyalty_discount_percent ?? 0;

            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'full_name' => $customer->full_name,
                    'phone' => $customer->phone,
                    'bonus_points' => $customer->customerProfile?->bonus_points ?? 0,
                    'loyalty_discount_percent' => $loyaltyDiscountPercent,
                ],
                'coupon' => $activeCoupon ? [
                    'id' => $activeCoupon->id,
                    'code' => $activeCoupon->code,
                    'discount_type' => $activeCoupon->discount_type,
                    'discount_value' => (float) $activeCoupon->discount_value,
                ] : null,
                'message' => 'Клиент найден'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при поиске клиента: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Сканирование QR-кода купона
     */
    public function scanCoupon(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);
            $couponCode = $request->qr_data;

            // Если JSON с coupon_id или code
            if ($qrData) {
                if (isset($qrData['coupon_id'])) {
                    $coupon = Coupon::where('id', $qrData['coupon_id'])
                        ->where('is_active', true)
                        ->first();
                } elseif (isset($qrData['code'])) {
                    $coupon = Coupon::where('code', $qrData['code'])
                        ->where('is_active', true)
                        ->first();
                }
            }

            // Если не нашли по JSON, ищем по коду напрямую
            if (!$coupon) {
                $coupon = Coupon::where('code', $couponCode)
                    ->where('is_active', true)
                    ->first();
            }

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Купон не найден или не активен'
                ]);
            }

            // Проверяем, не использован ли купон
            if ($coupon->used_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Купон уже использован'
                ]);
            }

            return response()->json([
                'success' => true,
                'coupon' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => (float) $coupon->discount_value,
                ],
                'message' => 'Купон найден'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сканировании: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Сканирование QR-кода товара
     */
    public function scanProduct(Request $request)
    {
        \Log::info('scanProduct - Вход:', ['qr_data' => $request->qr_data]);
        
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);
            \Log::info('scanProduct - Decoded:', ['qrData' => $qrData]);

            // Если JSON с variant_id (старый формат)
            if ($qrData && isset($qrData['variant_id'])) {
                $variant = ProductVariant::with(['product'])
                    ->find($qrData['variant_id']);

                if ($variant) {
                    \Log::info('scanProduct - Найдено по variant_id:', ['variant_id' => $variant->id]);
                    return $this->getVariantData($variant);
                }
            }

            // Если JSON с article (новый формат от админки)
            if ($qrData && isset($qrData['article'])) {
                $article = $qrData['article'];
                \Log::info('scanProduct - Поиск по article:', ['article' => $article]);

                // Ищем товар по артикулу
                $product = Product::where('article', $article)->first();

                if (!$product) {
                    \Log::warning('scanProduct - Товар не найден:', ['article' => $article]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Товар с артикулом "' . $article . '" не найден'
                    ]);
                }
                
                \Log::info('scanProduct - Товар найден:', ['product_id' => $product->id, 'name' => $product->name]);

                // Получаем первый вариант товара
                $variant = ProductVariant::with(['product'])
                    ->where('product_id', $product->id)
                    ->first();

                if ($variant) {
                    \Log::info('scanProduct - Вариант найден:', ['variant_id' => $variant->id]);
                    return $this->getVariantData($variant);
                }
                
                \Log::warning('scanProduct - Вариант не найден для товара:', ['product_id' => $product->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'У товара нет вариантов (размеров)'
                ]);
            }

            // Если просто строка/число - ищем по barcode или article
            if (is_numeric($request->qr_data)) {
                // Пробуем найти по штрих-коду
                $variant = ProductVariant::with(['product'])
                    ->where('barcode', $request->qr_data)
                    ->first();

                if ($variant) {
                    return $this->getVariantData($variant);
                }
            }

            // Пробуем найти по артикулу напрямую
            $product = Product::where('article', $request->qr_data)->first();
            if ($product) {
                $variant = ProductVariant::with(['product'])
                    ->where('product_id', $product->id)
                    ->first();

                if ($variant) {
                    return $this->getVariantData($variant);
                }
            }

            \Log::info('scanProduct - Не найдено, возвращаем:', ['success' => false]);
            
            return response()->json([
                'success' => false,
                'message' => 'Товар не найден'
            ]);

        } catch (\Exception $e) {
            \Log::error('scanProduct - Ошибка:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сканировании: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Получение данных о варианте товара
     */
    private function getVariantData($variant)
    {
        // Проверяем остатки
        $shopId = auth()->user()->shop_id ?? null;
        $stock = null;
        
        if ($shopId) {
            $stock = Stock::where('variant_id', $variant->id)
                ->where('shop_id', $shopId)
                ->first();
        }

        $quantity = $stock ? ($stock->quantity - $stock->reserved) : 0;

        // Определяем цену (оптовая или розничная)
        $price = $variant->product->retail_price ?? 0;

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'product_id' => $variant->product_id,
                'name' => $variant->product->name,
                'article' => $variant->product->article,
                'size' => $variant->size,
                'color' => $variant->color,
                'sku' => $variant->sku,
                'barcode' => $variant->barcode,
                'price' => (float) $price,
                'supplier_price' => (float) $variant->supplier_price,
                'wholesale_price' => (float) $variant->wholesale_price,
                'quantity' => $quantity,
                'photo' => $variant->product->photo,
            ],
            'message' => 'Товар найден'
        ]);
    }

    /**
     * Проверка остатков товара
     */
    public function checkStock(Request $request)
    {
        $variantIds = explode(',', $request->get('variant_ids', ''));
        $shopId = auth()->user()->shop_id;
        $source = $request->get('source', 'showcase');
        
        $stock = [];
        
        if ($shopId && !empty($variantIds)) {
            $query = Stock::whereIn('variant_id', $variantIds);
            
            // Если витрина - проверяем остатки магазина
            if ($source === 'showcase') {
                $query->where('shop_id', $shopId);
            } else {
                // Если склад - проверяем наличие на складе
                $query->where('shop_id', $shopId);
            }
            
            $stocks = $query->get();
            
            foreach ($stocks as $s) {
                $stock[$s->variant_id] = $s->quantity - $s->reserved;
            }
        }

        return response()->json([
            'success' => true,
            'stock' => $stock
        ]);
    }

    /**
     * Оформление продажи
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'payment_method' => 'required|in:cash,card,transfer,split',
            'amount_paid' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:users,id',
            'coupon_id' => 'nullable|exists:coupons,id',
            'split_payment' => 'nullable|array',
            'split_payment.cash' => 'nullable|numeric|min:0',
            'split_payment.card' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();
            $shopId = $user->shop_id;
            $items = $request->items;
            $couponId = $request->coupon_id;
            $paymentMethod = $request->payment_method;

            // Генерируем номер транзакции
            $transactionNumber = 'POS-' . strtoupper(Str::random(8));

            // Считаем общую сумму
            $totalAmount = 0;
            $totalSupplierPrice = 0;
            
            // Проверяем, есть ли купон с оптовой ценой
            $useWholesalePrice = false;
            if ($couponId) {
                $coupon = Coupon::find($couponId);
                if ($coupon && $coupon->discount_type === 'wholesale' && $coupon->isValid()) {
                    $useWholesalePrice = true;
                }
            }

            foreach ($items as $item) {
                $variant = ProductVariant::with('product')->find($item['variant_id']);
                if (!$variant) {
                    throw new \Exception("Товар не найден: {$item['variant_id']}");
                }

                // Если оптовый купон — продаём по цене закупки, иначе — розничная цена
                if ($useWholesalePrice) {
                    $price = $variant->supplier_price ?? 0;
                } else {
                    $price = $variant->product->retail_price ?? 0;
                }

                $subtotal = $price * $item['quantity'];
                $totalAmount += $subtotal;
                $totalSupplierPrice += $variant->supplier_price * $item['quantity'];

                // Проверяем и обновляем остатки
                $stock = Stock::where('variant_id', $variant->id)
                    ->where('shop_id', $shopId)
                    ->first();

                $availableStock = $stock ? ($stock->quantity - $stock->reserved) : 0;

                if (!$stock || $availableStock < $item['quantity']) {
                    throw new \Exception("Недостаточно товара: {$variant->product->name} (в наличии: {$availableStock} шт.)");
                }

                $stock->decrement('quantity', $item['quantity']);
            }

            // Применяем скидку купона (только процентную)
            $discountAmount = 0;
            if ($couponId && !$useWholesalePrice) {
                $coupon = Coupon::find($couponId);
                if ($coupon && $coupon->isValid() && $coupon->discount_type === 'percent') {
                    $discountAmount = $coupon->calculateDiscount($totalAmount);
                    $coupon->use();
                }
            } elseif ($request->customer_id) {
                // Применяем скидку лояльности клиента, если нет купона
                $customer = User::with('customerProfile')->find($request->customer_id);
                $loyaltyPercent = $customer->customerProfile?->loyalty_discount_percent ?? 0;
                if ($loyaltyPercent > 0) {
                    $discountAmount = $totalAmount * ($loyaltyPercent / 100);
                }
            }

            $finalAmount = $totalAmount - $discountAmount;

            // Мета данные для оплаты
            $metaData = [
                'amount_paid' => $request->amount_paid,
                'change' => $request->amount_paid - $finalAmount,
            ];

            // Разделённая оплата
            if ($paymentMethod === 'split' && $request->split_payment) {
                $metaData['split_payment'] = [
                    'cash' => $request->split_payment['cash'] ?? 0,
                    'card' => $request->split_payment['card'] ?? 0,
                ];
            }

            // Создаём транзакцию
            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'type' => 'sale',
                'total_amount' => $finalAmount,
                'discount_amount' => $discountAmount,
                'payment_method' => $paymentMethod,
                'shop_id' => $shopId,
                'user_id' => $user->id,
                'customer_id' => $request->customer_id,
                'coupon_id' => $couponId,
                'comment' => $request->comment ?? null,
                'meta' => $metaData,
            ]);

            // Создаём позиции транзакции
            foreach ($items as $item) {
                $variant = ProductVariant::with('product')->find($item['variant_id']);
                $price = $variant->product->retail_price ?? 0;
                $subtotal = $price * $item['quantity'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'supplier_price' => $variant->supplier_price,
                    'discount' => 0,
                    'subtotal' => $subtotal,
                ]);
            }

            // Обновляем профиль клиента (покупки и т.д.)
            if ($request->customer_id) {
                $customer = User::with('customerProfile')->find($request->customer_id);
                if ($customer && $customer->customerProfile) {
                    $customer->customerProfile->updateAfterPurchase($finalAmount);
                }
            }

            DB::commit();

            // Возвращаем данные для чека с загруженными связями
            $transaction->load(['items.variant.product', 'customer', 'shop']);

            // Возвращаем данные для чека
            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'message' => 'Продажа оформлена'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Печать чека
     */
    public function printReceipt($transactionId)
    {
        $transaction = Transaction::with(['items.variant.product', 'customer', 'shop', 'user'])
            ->where('id', $transactionId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$transaction) {
            abort(404, 'Чек не найден или у вас нет доступа');
        }

        return view('seller.pos.receipt', compact('transaction'));
    }

    /**
     * История продаж продавца
     */
    public function salesHistory(Request $request)
    {
        $user = auth()->user();
        $limit = $request->get('limit', 20);

        $query = Transaction::sales()
            ->where('user_id', $user->id)
            ->with(['customer', 'items.variant.product']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->orderBy('created_at', 'desc')->limit($limit)->get();

        $stats = [
            'today' => Transaction::sales()
                ->where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->sum('total_amount'),
            'today_count' => Transaction::sales()
                ->where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            'total' => Transaction::sales()
                ->where('user_id', $user->id)
                ->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'sales' => $sales,
            'stats' => $stats
        ]);
    }

    /**
     * Поиск продажи для возврата
     */
    public function searchSale(Request $request)
    {
        $query = $request->get('query', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Введите номер чека или телефон'
            ]);
        }

        // Ищем по номеру чека
        $sale = Transaction::sales()
            ->where('transaction_number', 'LIKE', "%{$query}%")
            ->with(['customer', 'items.variant.product'])
            ->first();

        // Если не нашли, ищем по телефону клиента
        if (!$sale) {
            $customer = User::where('phone', 'LIKE', "%{$query}%")->first();
            if ($customer) {
                $sale = Transaction::sales()
                    ->where('customer_id', $customer->id)
                    ->with(['customer', 'items.variant.product'])
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Продажа не найдена'
            ]);
        }

        return response()->json([
            'success' => true,
            'sale' => $sale
        ]);
    }

    /**
     * Обработка возврата товара
     */
    public function processReturn(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:transaction_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $transaction = Transaction::with(['items', 'shop'])->find($request->transaction_id);

            if (!$transaction) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Продажа не найдена'
                ]);
            }

            // Проверяем, что продавец имеет право оформлять возврат по этой транзакции
            // (транзакция должна быть из его магазина)
            $currentUser = auth()->user();
            if ($transaction->shop_id !== $currentUser->shop_id) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа к этой транзакции'
                ], 403);
            }

            // Проверяем, не был ли уже возврат
            if ($transaction->is_returned) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'По этой продаже уже оформлен возврат'
                ]);
            }

            // Возвращаем товары на витрину
            foreach ($request->items as $returnItem) {
                $transactionItem = TransactionItem::find($returnItem['item_id']);
                
                if ($transactionItem) {
                    // Восстанавливаем остатки
                    $stock = Stock::where('variant_id', $transactionItem->variant_id)
                        ->where('shop_id', $transaction->shop_id)
                        ->first();

                    if ($stock) {
                        $stock->increment('quantity', $returnItem['quantity']);
                    } else {
                        Stock::create([
                            'variant_id' => $transactionItem->variant_id,
                            'shop_id' => $transaction->shop_id,
                            'quantity' => $returnItem['quantity'],
                            'reserved' => 0,
                        ]);
                    }
                }
            }

            // Считаем сумму возврата
            $returnAmount = 0;
            foreach ($request->items as $returnItem) {
                $transactionItem = TransactionItem::find($returnItem['item_id']);
                if ($transactionItem) {
                    $returnAmount += $transactionItem->price * $returnItem['quantity'];
                }
            }

            // Создаём запись о возврате
            $returnTransaction = Transaction::create([
                'transaction_number' => 'RET-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'type' => 'return',
                'total_amount' => $returnAmount,
                'payment_method' => $transaction->payment_method,
                'shop_id' => $transaction->shop_id,
                'user_id' => auth()->id(),
                'customer_id' => $transaction->customer_id,
                'meta' => [
                    'original_transaction_id' => $transaction->id,
                    'reason' => $request->reason,
                    'items' => $request->items,
                ],
            ]);

            // Создаём позиции возврата
            foreach ($request->items as $returnItem) {
                $transactionItem = TransactionItem::find($returnItem['item_id']);
                if ($transactionItem) {
                    TransactionItem::create([
                        'transaction_id' => $returnTransaction->id,
                        'variant_id' => $transactionItem->variant_id,
                        'quantity' => $returnItem['quantity'],
                        'price' => $transactionItem->price,
                        'supplier_price' => $transactionItem->supplier_price,
                        'discount' => 0,
                        'subtotal' => $transactionItem->price * $returnItem['quantity'],
                    ]);
                }
            }

            // Помечаем оригинальную транзакцию как возвращённую (если все товары возвращены)
            $originalItemsCount = $transaction->items->sum('quantity');
            $returnedItemsCount = collect($request->items)->sum('quantity');
            
            if ($originalItemsCount === $returnedItemsCount) {
                $transaction->update(['is_returned' => true]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Возврат оформлен',
                'return_transaction' => $returnTransaction
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при оформлении возврата: ' . $e->getMessage()
            ], 400);
        }
    }
}
