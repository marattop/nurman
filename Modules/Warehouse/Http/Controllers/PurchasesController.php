<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Warehouse\Models\Purchase;
use Modules\Warehouse\Models\ProductVariant;
use Modules\Warehouse\Models\Stock;
use Modules\Warehouse\Models\Shop;
use Modules\Warehouse\Models\Warehouse as WarehouseModel;

class PurchasesController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['variant.product', 'shop', 'warehouse', 'receivedBy']);
        
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $purchases = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('warehouse::purchases.index', compact('purchases'));
    }

    public function create()
    {
        $shops = Shop::all();
        $warehouses = WarehouseModel::all();
        $products = \Modules\Warehouse\Models\Product::with('variants')->get();
        return view('warehouse::purchases.create', compact('shops', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'nullable|string',
            'variant_id' => 'required|exists:product_variants,id',
            'shop_id' => 'nullable|exists:shops,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'supplier_price' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'comment' => 'nullable|string',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);
        $totalAmount = $validated['quantity'] * $validated['supplier_price'];

        $purchase = Purchase::create([
            'invoice_number' => $validated['invoice_number'] ?? ('PUR-' . strtoupper(uniqid())),
            'variant_id' => $validated['variant_id'],
            'shop_id' => $validated['shop_id'] ?? null,
            'warehouse_id' => $validated['warehouse_id'] ?? null,
            'quantity' => $validated['quantity'],
            'supplier_price' => $validated['supplier_price'],
            'total_amount' => $totalAmount,
            'received_by' => auth()->id(),
            'purchase_date' => $validated['purchase_date'] ?? now(),
            'comment' => $validated['comment'] ?? null,
        ]);

        // Обновить остаток
        $stock = Stock::firstOrCreate(
            [
                'variant_id' => $validated['variant_id'],
                'shop_id' => $validated['shop_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'] ?? null,
            ],
            ['quantity' => 0, 'min_stock' => 5]
        );

        $stock->addQuantity($validated['quantity']);

        // Логируем создание закупки
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'purchase_created',
            'model_type' => \Modules\Warehouse\Models\Purchase::class,
            'model_id' => $purchase->id,
            'description' => 'Создана закупка #' . ($purchase->invoice_number ?? $purchase->id) . ' от ' . $variant->product->name . ' (' . $validated['quantity'] . ' шт)',
            'new_values' => [
                'variant_id' => $validated['variant_id'],
                'quantity' => $validated['quantity'],
                'supplier_price' => $validated['supplier_price'],
                'total_amount' => $totalAmount,
                'location' => $validated['shop_id'] ? 'Магазин #' . $validated['shop_id'] : 'Склад #' . $validated['warehouse_id'],
            ],
        ]);

        return redirect()->route('warehouse.purchases.index')
            ->with('success', 'Закупка успешно добавлена');
    }
}
