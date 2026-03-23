<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Models\User;
use Modules\Client\Models\CustomerPurchaseHistory;
use Modules\Seller\Models\Coupon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->customerProfile;
        
        // Последние покупки
        $recentPurchases = CustomerPurchaseHistory::where('customer_id', $user->id)
            ->with('transaction')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Активный купон
        $activeCoupon = Coupon::where('customer_id', $user->id)
            ->where('is_active', true)
            ->first();
        
        // Уведомления
        $notifications = $user->notifications()->unread()->limit(5)->get();
        
        return view('client::dashboard', compact('profile', 'recentPurchases', 'activeCoupon', 'notifications'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('client::notifications', compact('notifications'));
    }

    public function markAsRead(\Modules\Client\Models\Notification $notification)
    {
        $notification->markAsRead();
        return back()->with('success', 'Уведомление прочитано');
    }
}
