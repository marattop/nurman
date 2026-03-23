<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class ClientController extends Controller
{
    /**
     * Главная страница клиента (Dashboard)
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Проверка: есть ли профиль
        if (!$user->customerProfile) {
            return redirect()->route('client.profile')
                ->with('warning', 'Ваш профиль не создан. Обратитесь к администратору.');
        }

        $profile = $user->customerProfile;

        // История покупок (последние 10)
        $recentPurchases = Transaction::where('customer_id', $user->id)
            ->where('type', 'sale')
            ->with(['items.variant.product', 'shop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Активные купоны
        $activeCoupons = Coupon::where('customer_id', $user->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            })
            ->where('usage_count', '<', DB::raw('usage_limit'))
            ->with('qrCode')
            ->get();

        // Статистика
        $stats = [
            'total_purchases' => Transaction::where('customer_id', $user->id)
                ->where('type', 'sale')
                ->count(),
            'total_spent' => Transaction::where('customer_id', $user->id)
                ->where('type', 'sale')
                ->sum('total_amount'),
            'bonus_points' => $profile->bonus_points ?? 0,
            'active_coupons' => $activeCoupons->count(),
        ];

        // Последние уведомления
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('client.dashboard', compact(
            'profile', 'recentPurchases', 'activeCoupons', 'stats', 'notifications'
        ));
    }

    /**
     * Профиль клиента
     */
    public function profile()
    {
        $user = auth()->user();
        $profile = $user->customerProfile;

        // Если профиль не создан — создаём
        if (!$profile) {
            $profile = $user->customerProfile()->create([
                'bonus_points' => 0,
            ]);
        }

        return view('client.profile.index', compact('profile'));
    }

    /**
     * Обновление профиля
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
        ]);

        $user->update(['full_name' => $validated['full_name']]);

        if ($user->customerProfile) {
            $user->customerProfile->update([
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
            ]);
        }

        return back()->with('success', 'Профиль обновлён');
    }

    /**
     * Смена пароля
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ]);

        // Проверка текущего пароля
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Неверный текущий пароль']);
        }

        // Обновление пароля
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Пароль успешно изменён');
    }

    /**
     * История покупок
     */
    public function purchases(Request $request)
    {
        $user = auth()->user();
        
        $query = Transaction::where('customer_id', $user->id)
            ->where('type', 'sale')
            ->with(['items.variant.product', 'shop', 'coupon']);

        // Фильтры
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(20);

        // Магазины для фильтра
        $shops = DB::table('shops')
            ->select('id', 'name')
            ->where('is_active', true)
            ->get();

        return view('client.purchases.index', compact('purchases', 'shops'));
    }

    /**
     * Детали покупки
     */
    public function purchaseDetail($transactionId)
    {
        $user = auth()->user();
        
        $purchase = Transaction::where('id', $transactionId)
            ->where('customer_id', $user->id)
            ->where('type', 'sale')
            ->with(['items.variant.product', 'shop', 'coupon', 'user'])
            ->first();

        if (!$purchase) {
            abort(404, 'Покупка не найдена');
        }

        return view('client.purchases.show', compact('purchase'));
    }

    /**
     * Печать чека
     */
    public function printReceipt($transactionId)
    {
        $user = auth()->user();
        
        $transaction = Transaction::where('id', $transactionId)
            ->where('customer_id', $user->id)
            ->with(['items.variant.product', 'shop', 'user'])
            ->first();

        if (!$transaction) {
            abort(404);
        }

        return view('client.purchases.receipt', compact('transaction'));
    }

    /**
     * Купоны клиента
     */
    public function coupons()
    {
        $user = auth()->user();
        
        $coupons = Coupon::where('customer_id', $user->id)
            ->with(['qrCode', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Группировка по статусу
        $activeCoupons = $coupons->filter(function($c) {
            return $c->is_active && (!$c->valid_until || $c->valid_until->gte(now()));
        });

        $expiredCoupons = $coupons->filter(function($c) {
            return !$c->is_active || ($c->valid_until && $c->valid_until->lt(now()));
        });

        return view('client.coupons.index', compact('coupons', 'activeCoupons', 'expiredCoupons'));
    }

    /**
     * Печать купона
     */
    public function printCoupon($couponId)
    {
        $user = auth()->user();
        
        $coupon = Coupon::where('id', $couponId)
            ->where('customer_id', $user->id)
            ->with(['customer.customerProfile', 'qrCode'])
            ->first();

        if (!$coupon) {
            abort(404);
        }

        return view('client.coupons.print', compact('coupon'));
    }

    /**
     * Детали заказа (для совместимости)
     */
    public function orderDetail($orderId)
    {
        return $this->purchaseDetail($orderId);
    }

    /**
     * Чек покупки (для совместимости)
     */
    public function receipt($transactionId)
    {
        return $this->printReceipt($transactionId);
    }
}
