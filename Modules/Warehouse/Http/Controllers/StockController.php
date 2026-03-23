<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Warehouse\Models\Stock;
use Modules\Warehouse\Models\ProductVariant;
use Modules\Warehouse\Models\Shop;
use Modules\Warehouse\Models\Warehouse as WarehouseModel;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::with(['variant.product', 'shop', 'warehouse']);
        
        if ($shopId = $request->get('shop_id')) {
            $query->where('shop_id', $shopId);
        }
        
        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        if ($request->get('low_stock')) {
            $query->whereColumn('quantity', '<=', 'min_stock');
        }
        
        $stock = $query->paginate(50);
        $shops = Shop::all();
        $warehouses = WarehouseModel::all();
        
        return view('warehouse::stock.index', compact('stock', 'shops', 'warehouses'));
    }

    public function byShop(Request $request)
    {
        $shopId = $request->get('shop_id');
        
        $stock = Stock::where('shop_id', $shopId)
            ->with(['variant.product'])
            ->get();
        
        return view('warehouse::stock.by-shop', compact('stock'));
    }

    public function lowStock()
    {
        $stock = Stock::whereColumn('quantity', '<=', 'min_stock')
            ->with(['variant.product', 'shop', 'warehouse'])
            ->get();
        
        return view('warehouse::stock.low-stock', compact('stock'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'shop_id' => 'nullable|exists:shops,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $stock = Stock::firstOrCreate(
            [
                'variant_id' => $validated['variant_id'],
                'shop_id' => $validated['shop_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'] ?? null,
            ],
            ['quantity' => 0, 'min_stock' => 5]
        );

        $stock->addQuantity($validated['quantity']);

        return back()->with('success', 'Остаток пополнен');
    }
}
