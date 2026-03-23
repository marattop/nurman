<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Purchase;

class WarehouseController extends Controller
{
    public function dashboard()
    {
        $totalProducts = Product::count();
        $lowStock = Stock::whereColumn('quantity', '<=', 'min_stock')->with(['variant.product'])->count();
        $recentPurchases = Purchase::with(['variant.product', 'receivedBy'])->orderBy('created_at', 'desc')->limit(10)->get();
        $newProducts = Product::with(['category', 'createdBy'])->orderBy('created_at', 'desc')->limit(10)->get();

        return view('warehouse.dashboard', compact('totalProducts', 'lowStock', 'recentPurchases', 'newProducts'));
    }
}
