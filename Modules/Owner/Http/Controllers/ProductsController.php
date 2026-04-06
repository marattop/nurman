<?php

namespace Modules\Owner\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\Shop;
use App\Models\Warehouse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    /**
     * Display a listing of products with stock info
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants.stock.shop', 'variants.stock.warehouse'])
            ->where('is_active', true);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('article', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Stock filter
        if ($request->filled('stock_filter')) {
            if ($request->stock_filter === 'low') {
                $query->whereHas('variants.stock', function($q) {
                    $q->whereRaw('(quantity - reserved) < 10');
                });
            } elseif ($request->stock_filter === 'out') {
                $query->whereHas('variants.stock', function($q) {
                    $q->whereRaw('(quantity - reserved) <= 0');
                });
            } elseif ($request->stock_filter === 'available') {
                $query->whereHas('variants.stock', function($q) {
                    $q->whereRaw('(quantity - reserved) > 0');
                });
            }
        }

        $products = $query->orderBy('name')->paginate(20);

        // Calculate stats for each product
        $products->getCollection()->transform(function($product) {
            $totalQuantity = 0;
            $displayQuantity = 0;
            $warehouseQuantity = 0;
            $totalCost = 0;
            $totalValue = 0;
            $minStock = false;

            foreach ($product->variants as $variant) {
                $variantTotal = 0;
                $variantDisplay = 0;
                $variantWarehouse = 0;

                foreach ($variant->stock as $stock) {
                    $available = $stock->quantity - $stock->reserved;
                    $variantTotal += $available;

                    if ($stock->shop_id) {
                        $variantDisplay += $available;
                    }

                    if ($stock->warehouse_id) {
                        $variantWarehouse += $available;
                    }

                    if ($available < 10) {
                        $minStock = true;
                    }
                }

                $totalQuantity += $variantTotal;
                $displayQuantity += $variantDisplay;
                $warehouseQuantity += $variantWarehouse;

                // Calculate costs
                if ($variant->supplier_price) {
                    $totalCost += $variant->supplier_price * $variantTotal;
                }

                if ($product->retail_price) {
                    $totalValue += $product->retail_price * $variantTotal;
                }
            }

            $product->total_quantity = $totalQuantity;
            $product->display_quantity = $displayQuantity;
            $product->warehouse_quantity = $warehouseQuantity;
            $product->total_cost = $totalCost;
            $product->total_value = $totalValue;
            $product->potential_profit = $totalValue - $totalCost;
            $product->low_stock = $minStock;

            return $product;
        });

        $categories = Category::where('is_active', true)->get();

        return view('owner::products.index', compact('products', 'categories'));
    }

    /**
     * Show product details with variants
     */
    public function show(Product $product)
    {
        $product->load(['category', 'variants.stock.shop', 'variants.stock.warehouse']);

        $variantsData = [];

        foreach ($product->variants as $variant) {
            $displayStock = [];
            $warehouseStock = [];
            $totalAvailable = 0;

            foreach ($variant->stock as $stock) {
                $available = $stock->quantity - $stock->reserved;
                $totalAvailable += $available;

                if ($stock->shop_id) {
                    $displayStock[] = [
                        'shop_name' => $stock->shop->name,
                        'quantity' => $available,
                        'reserved' => $stock->reserved,
                        'low_stock' => $available < 10,
                    ];
                }

                if ($stock->warehouse_id) {
                    $warehouseStock[] = [
                        'warehouse_name' => $stock->warehouse->name,
                        'quantity' => $available,
                        'reserved' => $stock->reserved,
                        'low_stock' => $available < 10,
                    ];
                }
            }

            $variantsData[] = [
                'id' => $variant->id,
                'size' => $variant->size,
                'color' => $variant->color,
                'sku' => $variant->sku,
                'supplier_price' => $variant->supplier_price,
                'wholesale_price' => $variant->wholesale_price,
                'retail_price' => $product->retail_price,
                'total_stock' => $totalAvailable,
                'display_stock' => $displayStock,
                'warehouse_stock' => $warehouseStock,
                'low_stock' => $totalAvailable < 10,
                'barcode' => $variant->barcode,
            ];
        }

        return view('owner::products.show', compact('product', 'variantsData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $shops = Shop::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('owner::products.create', compact('categories', 'shops', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation for creating products
        return back()->with('success', 'Товар создан');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('owner::products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'retail_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'brand' => 'nullable|string|max:255',
        ]);

        $product->update($validated);

        return back()->with('success', 'Товар обновлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);
        return back()->with('success', 'Товар деактивирован');
    }
}
