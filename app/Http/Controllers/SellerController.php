<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\QrCode;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    // ========================================================================
    // 👤 ПРОФИЛЬ ПРОДАВЦА
    // ========================================================================

    /**
     * Страница профиля.
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Обновление профиля.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
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
        try {
            $user = auth()->user();

            $sales = Transaction::sales()
                ->where('user_id', $user->id)
                ->with(['items'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

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
        } catch (\Exception $e) {
            \Log::error('mySales error: ' . $e->getMessage());
            return redirect()->route('seller.dashboard')->with('error', 'Ошибка: ' . $e->getMessage());
        }
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
        $customers = User::where('role_id', 5)
            ->with(['myCoupons' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('seller.customers.index', compact('customers'));
    }

    /**
     * История покупок клиента
     */
    public function customerHistory($customerId)
    {
        $sales = Transaction::where('customer_id', $customerId)
            ->where('type', 'sale')
            ->with(['items.variant.product'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'sales' => $sales
        ]);
    }

    /**
     * Создание купона для клиента
     */
    public function createCouponForCustomer(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'discount_type' => 'required|in:percent,wholesale',
            'discount_value' => 'nullable|numeric|min:0',
            'validity_period' => 'nullable|numeric',
        ]);

        $customer = User::find($validated['customer_id']);
        
        // Проверяем, есть ли уже активный купон
        $existingCoupon = Coupon::where('customer_id', $customer->id)
            ->where('is_active', true)
            ->first();
        
        if ($existingCoupon) {
            return response()->json([
                'success' => false,
                'message' => 'У клиента уже есть активный купон'
            ]);
        }

        // Вычисляем дату окончания действия
        $validUntil = null;
        if ($validated['validity_period'] == '999') {
            $validUntil = now()->addYears(10);
        } else {
            $months = (int)($validated['validity_period'] ?? 12);
            $validUntil = now()->addMonths($months);
        }

        // Создание купона
        $coupon = Coupon::create([
            'code' => 'CPN-' . strtoupper(Str::random(8)),
            'customer_id' => $customer->id,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'] ?? 0,
            'usage_limit' => 999999,
            'usage_count' => 0,
            'valid_from' => now(),
            'valid_until' => $validUntil,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        // Генерация QR-кода для купона
        $qrData = json_encode([
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
        ], JSON_UNESCAPED_UNICODE);

        QrCode::create([
            'qr_data' => $qrData,
            'code' => $coupon->code,
            'product_id' => null,
            'variant_id' => null,
            'coupon_id' => $coupon->id,
            'is_printed' => false,
            'printed_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'coupon' => $coupon
        ]);
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
