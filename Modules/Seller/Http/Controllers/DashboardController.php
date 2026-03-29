<?php

namespace Modules\Seller\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Seller\Models\Transaction;
use Modules\Warehouse\Models\ProductVariant;
use Modules\Warehouse\Models\Shop;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Продажи за сегодня
        $todaySales = Transaction::sales()
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('total_amount');
        
        // Количество продаж за сегодня
        $todayCount = Transaction::sales()
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();
        
        // Последние продажи
        $recentSales = Transaction::sales()
            ->where('user_id', $user->id)
            ->with(['items', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('seller::dashboard', compact('todaySales', 'todayCount', 'recentSales'));
    }
}
