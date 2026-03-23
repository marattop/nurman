<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Warehouse\Models\Product;
use Modules\Warehouse\Models\Stock;
use Modules\Warehouse\Models\Purchase;

class DashboardController extends Controller
{
    public function index()
    {
        // Общее количество товаров
        $totalProducts = Product::count();
        
        // Товары с низким остатком
        $lowStock = Stock::whereColumn('quantity', '<=', 'min_stock')
            ->with(['variant.product'])
            ->count();
        
        // Последние закупки
        $recentPurchases = Purchase::with(['variant.product', 'receivedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Новые товары
        $newProducts = Product::with(['category', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('warehouse::dashboard', compact('totalProducts', 'lowStock', 'recentPurchases', 'newProducts'));
    }
}
