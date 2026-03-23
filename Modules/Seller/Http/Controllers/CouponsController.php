<?php

namespace Modules\Seller\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Seller\Models\Coupon;

class CouponsController extends Controller
{
    public function index()
    {
        $coupons = Coupon::with(['customer', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('seller::coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('seller::coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_phone' => 'required|string',
            'customer_name' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed,wholesale',
            'discount_value' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_until' => 'nullable|date',
            'comment' => 'nullable|string',
        ]);

        // Найти или создать клиента
        $customer = \Modules\Core\Models\User::where('phone', $validated['customer_phone'])->first();
        
        if (!$customer) {
            $customer = \Modules\Core\Models\User::create([
                'username' => 'client_' . time(),
                'phone' => $validated['customer_phone'],
                'full_name' => $validated['customer_name'] ?? null,
                'password' => bcrypt('123456'),
                'role_id' => 5, // ROLE_CLIENT
            ]);
            
            // Создать профиль клиента
            \Modules\Client\Models\CustomerProfile::create([
                'user_id' => $customer->id,
            ]);
        }

        // Создать купон
        $coupon = Coupon::create([
            'code' => 'COUPON-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'customer_id' => $customer->id,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_type'] === 'wholesale' ? null : $validated['discount_value'],
            'min_purchase_amount' => $validated['min_purchase_amount'] ?? 0,
            'usage_limit' => $validated['usage_limit'] ?? 1,
            'valid_until' => $validated['valid_until'] ?? null,
            'is_active' => true,
            'created_by' => auth()->id(),
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('seller.coupons')
            ->with('success', 'Купон создан для клиента ' . $customer->full_name);
    }

    public function scan()
    {
        return view('seller::coupons.scan');
    }
}
