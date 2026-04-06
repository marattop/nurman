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
        try {
            $validated = $request->validate([
                'invoice_number' => 'nullable|string',
                'variant_id' => 'required|exists:product_variants,id',
                'shop_id' => 'nullable|exists:shops,id',
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'quantity' => 'required|integer|min:1',
                'supplier_price' => 'required|numeric|min:0',
                'purchase_date' => 'nullable|date',
                'comment' => 'nullable|string',
            ], [
                'shop_id.required_without' => 'Укажите магазин или склад',
            ]);

            // Проверка что хотя бы одно указано
            if (empty($validated['shop_id']) && empty($validated['warehouse_id'])) {
                return back()
                    ->withErrors(['location' => 'Необходимо указать магазин или склад'])
                    ->withInput();
            }

            \Log::info('ВАЛИДАЦИЯ ПРОШЛА', $validated);

            $variant = ProductVariant::with('product')->findOrFail($validated['variant_id']);
            $totalAmount = $validated['quantity'] * $validated['supplier_price'];

            \Log::info('Товар найден', [
                'variant_id' => $variant->id,
                'product_name' => $variant->product->name ?? 'N/A',
            ]);

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

            \Log::info('Закупка создана', ['purchase_id' => $purchase->id]);

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

            \Log::info('Остатки обновлены', [
                'stock_id' => $stock->id,
                'new_quantity' => $stock->quantity,
            ]);

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

            \Log::info('=== ЗАКУПКА УСПЕШНО ===', ['purchase_id' => $purchase->id]);

            return redirect()->route('warehouse.purchases.index')
                ->with('success', 'Закупка успешно добавлена: ' . $variant->product->name . ' x' . $validated['quantity']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('=== ЗАКУПКА ОШИБКА ВАЛИДАЦИИ ===', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            
            return back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            \Log::error('=== ЗАКУПКА ОШИБКА ===', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            
            return back()
                ->with('error', 'Ошибка при оформлении закупки: ' . $e->getMessage())
                ->withInput();
        }
    }
}
