<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Warehouse\Models\Stock;
use Modules\Warehouse\Models\ProductVariant;

class InventoryController extends Controller
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
        
        $stock = $query->paginate(50);
        
        return view('warehouse::inventory.index', compact('stock'));
    }

    public function count(Request $request)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stock,id',
            'actual_quantity' => 'required|integer|min:0',
        ]);

        $stock = Stock::findOrFail($validated['stock_id']);
        $difference = $validated['actual_quantity'] - $stock->quantity;

        return response()->json([
            'success' => true,
            'stock_id' => $stock->id,
            'recorded_quantity' => $stock->quantity,
            'actual_quantity' => $validated['actual_quantity'],
            'difference' => $difference,
        ]);
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stock,id',
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string',
        ]);

        $stock = Stock::findOrFail($validated['stock_id']);
        $oldQuantity = $stock->quantity;
        
        $stock->update(['quantity' => $validated['new_quantity']]);

        // Записать в лог (можно добавить таблицу inventory_adjustments)

        return back()->with('success', "Остаток изменен с {$oldQuantity} до {$validated['new_quantity']}");
    }
}
