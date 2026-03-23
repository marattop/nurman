<?php

namespace Modules\Seller\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Warehouse\Models\ProductVariant;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'stock']);
        
        if ($search = $request->get('search')) {
            $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('article', 'like', "%{$search}%"));
        }
        
        $products = $query->paginate(20);
        
        return view('seller::products.index', compact('products'));
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $products = ProductVariant::with(['product', 'stock'])
            ->whereHas('product', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('article', 'like', "%{$search}%"))
            ->limit(10)
            ->get();
        
        return response()->json(['products' => $products]);
    }

    public function show(ProductVariant $product)
    {
        $product->load(['product', 'stock.shop']);
        return view('seller::products.show', compact('product'));
    }
}
