<?php

namespace Modules\Client\Http\Controllers;

use Modules\Seller\Models\Coupon;

class CouponsController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where('customer_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('client::coupons.index', compact('coupons'));
    }

    public function show(Coupon $coupon)
    {
        if ($coupon->customer_id !== auth()->id()) {
            abort(403);
        }
        
        return view('client::coupons.show', compact('coupon'));
    }
}
