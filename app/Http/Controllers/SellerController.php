<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $todaySales = Transaction::sales()->where('user_id', $user->id)->whereDate('created_at', today())->sum('total_amount');
        $todayCount = Transaction::sales()->where('user_id', $user->id)->whereDate('created_at', today())->count();
        $recentSales = Transaction::sales()->where('user_id', $user->id)->with(['items.variant.product'])->orderBy('created_at', 'desc')->limit(10)->get();

        return view('seller.dashboard', compact('todaySales', 'todayCount', 'recentSales'));
    }

    /**
     * Поиск товаров для POS-терминала
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $products = Product::query()
            ->with(['variants', 'category'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('article', 'like', "%{$query}%");
            });

        if ($categoryId) {
            $products->where('category_id', $categoryId);
        }

        $products = $products->limit(20)->get();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * История продаж продавца
     */
    public function mySales(Request $request)
    {
        $user = auth()->user();

        $query = Transaction::sales()
            ->where('user_id', $user->id)
            ->with(['items.variant.product', 'customer']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);

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

        return view('seller.pos.history', compact('sales', 'stats'));
    }

    /**
     * Продажи за сегодня
     */
    public function todaySales(Request $request)
    {
        $user = auth()->user();

        $query = Transaction::sales()
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->with(['items.variant.product', 'customer']);

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);

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

        return view('seller.pos.history', compact('sales', 'stats'));
    }

    /**
     * Страница смены
     */
    public function shift()
    {
        $user = auth()->user();
        $currentShift = null; // Заглушка: функционал смены не реализован

        return view('seller.shift.index', compact('currentShift'));
    }

    /**
     * Открытие смены
     */
    public function openShift(Request $request)
    {
        // Заглушка: функционал смены не реализован
        return response()->json([
            'success' => true,
            'message' => 'Смена открыта (функционал в разработке)',
        ]);
    }

    /**
     * Закрытие смены
     */
    public function closeShift(Request $request)
    {
        // Заглушка: функционал смены не реализован
        return response()->json([
            'success' => true,
            'message' => 'Смена закрыта (функционал в разработке)',
        ]);
    }

    /**
     * Список клиентов с купонами
     */
    public function customers()
    {
        $coupons = Coupon::with(['customer.customerProfile', 'qrCode'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('seller.customers.index', compact('coupons'));
    }

    /**
     * Сохранение клиента
     */
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
        ]);

        $customer = User::create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'role_id' => 5,
            'username' => 'customer_' . time(),
            'email' => 'customer_' . time() . '@temp.local',
            'password' => bcrypt(str()->random(16)),
        ]);

        // Создаём профиль клиента
        $customer->customerProfile()->create([
            'bonus_points' => 0,
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer,
        ]);
    }

    /**
     * Поиск клиента
     */
    public function findCustomer(Request $request)
    {
        $query = $request->get('q', '');

        $customers = User::where('role_id', 5)
            ->with('customerProfile')
            ->where(function($q) use ($query) {
                $q->where('full_name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'customers' => $customers,
        ]);
    }

    /**
     * Сканирование штрих-кода
     */
    public function scanBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $variant = \App\Models\ProductVariant::where('barcode', $request->barcode)
            ->with('product')
            ->first();

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Товар не найден',
            ]);
        }

        $shopId = auth()->user()->shop_id;
        $stock = \App\Models\Stock::where('variant_id', $variant->id)
            ->where('shop_id', $shopId)
            ->first();

        $availableStock = $stock ? ($stock->quantity - $stock->reserved) : 0;

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'product_id' => $variant->product_id,
                'name' => $variant->product->name,
                'article' => $variant->product->article,
                'size' => $variant->size,
                'color' => $variant->color,
                'price' => $variant->product->retail_price ?? 0,
                'quantity' => $availableStock,
            ],
        ]);
    }

    /**
     * Создание купона для клиента
     */
    public function createCoupon(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'discount_type' => 'required|in:percent,wholesale',
            'discount_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_until' => 'nullable|date|after:today',
            'comment' => 'nullable|string',
        ]);

        $code = 'CPN-' . strtoupper(substr(uniqid(), -8));

        // Для оптовой скидки discount_value = 0
        $discountValue = $request->discount_type === 'wholesale' ? 0 : ($request->discount_value ?? 0);

        $coupon = Coupon::create([
            'code' => $code,
            'customer_id' => $request->customer_id,
            'discount_type' => $request->discount_type,
            'discount_value' => $discountValue,
            'min_purchase_amount' => $request->min_purchase_amount ?? 0,
            'usage_limit' => $request->usage_limit ?? 1,
            'usage_count' => 0,
            'valid_from' => now(),
            'valid_until' => $request->valid_until ? $request->valid_until : now()->addMonth(30),
            'is_active' => true,
            'created_by' => auth()->id(),
            'comment' => $request->comment ?? null,
        ]);

        // Генерация QR-кода для купона
        $qrData = json_encode(['coupon_id' => $coupon->id, 'code' => $coupon->code]);
        \App\Models\QrCode::create([
            'variant_id' => null,
            'coupon_id' => $coupon->id,
            'qr_data' => $qrData,
            'is_printed' => false,
        ]);

        return response()->json([
            'success' => true,
            'coupon' => $coupon,
            'message' => 'Купон создан',
        ]);
    }

    /**
     * Регистрация нового клиента
     */
    public function registerCustomer(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
        ]);

        $customer = User::create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'username' => $request->phone,
            'email' => $request->phone . '@customer.local',
            'password' => bcrypt($request->password),
            'role_id' => 5,
            'is_active' => true,
        ]);

        // Создаём профиль клиента
        $customer->customerProfile()->create([
            'bonus_points' => 0,
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'message' => 'Клиент зарегистрирован',
        ]);
    }

    /**
     * Купоны клиента
     */
    public function customerCoupons($customerId)
    {
        $coupons = Coupon::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'coupons' => $coupons,
        ]);
    }
}
