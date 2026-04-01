<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\TransactionItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Список закупок.
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['variant.product', 'shop', 'warehouse', 'receivedBy'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $query->where('purchase_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        $purchases = $query->paginate(20);
        
        // Статистика
        $stats = [
            'total_amount' => Purchase::whereBetween('purchase_date', [
                    $request->date_from ?? now()->subMonth(),
                    $request->date_to ?? now()
                ])->sum('total_amount'),
            'count' => Purchase::whereBetween('purchase_date', [
                    $request->date_from ?? now()->subMonth(),
                    $request->date_to ?? now()
                ])->count(),
        ];
        
        return view('admin.purchases.index', compact('purchases', 'stats'));
    }

    /**
     * Дефицит товаров (критические остатки).
     */
    public function deficit()
    {
        $products = Product::where('is_active', true)
            ->with(['variants' => function($q) {
                $q->with(['stock', 'product']);
            }])
            ->get();

        $deficitItems = [];
        
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $totalQty = $variant->stock->sum('quantity');
                $minStock = 5; // Критический минимум
                
                if ($totalQty <= $minStock) {
                    // Рассчитываем средние продажи в день
                    $salesData = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                        ->where('transactions.type', 'sale')
                        ->where('transaction_items.variant_id', $variant->id)
                        ->where('transactions.created_at', '>=', now()->subDays(30))
                        ->selectRaw('SUM(transaction_items.quantity) as total_qty, DATEDIFF(MAX(transactions.created_at), MIN(transactions.created_at)) as days')
                        ->first();
                    
                    $avgDailySales = $salesData && $salesData->days > 0 
                        ? $salesData->total_qty / $salesData->days 
                        : 0;
                    
                    $recommendedQty = max(0, ceil($avgDailySales * 30) - $totalQty); // На 30 дней
                    
                    $deficitItems[] = [
                        'variant' => $variant,
                        'current_qty' => $totalQty,
                        'min_stock' => $minStock,
                        'avg_daily_sales' => round($avgDailySales, 2),
                        'recommended_qty' => $recommendedQty,
                        'last_sale_at' => $salesData ? DB::table('transactions')
                            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
                            ->where('transaction_items.variant_id', $variant->id)
                            ->orderBy('transactions.created_at', 'desc')
                            ->value('transactions.created_at') : null,
                    ];
                }
            }
        }

        return view('admin.purchases.deficit', compact('deficitItems'));
    }

    /**
     * Форма создания закупки.
     */
    public function create(Request $request)
    {
        $mode = $request->get('mode', 'all'); // all, deficit, slow
        
        $products = Product::where('is_active', true)
            ->with(['variants' => function($q) {
                $q->with(['stock']);
            }])
            ->orderBy('name')
            ->get();

        // Фильтрация по режиму
        if ($mode === 'deficit') {
            $products = $products->filter(function($product) {
                foreach ($product->variants as $variant) {
                    $totalQty = $variant->stock->sum('quantity');
                    if ($totalQty <= 5) return true;
                }
                return false;
            });
        }

        $shops = Shop::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('admin.purchases.create', compact('products', 'shops', 'warehouses', 'mode'));
    }

    /**
     * Получение данных о товаре для формы закупки (AJAX).
     */
    public function getVariantInfo($variantId)
    {
        $variant = ProductVariant::with(['product', 'stock'])->findOrFail($variantId);
        
        $totalQty = $variant->stock->sum('quantity');
        
        // Продажи за последние 30 дней
        $salesData = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->where('transaction_items.variant_id', $variant->id)
            ->where('transactions.created_at', '>=', now()->subDays(30))
            ->selectRaw('SUM(transaction_items.quantity) as total_qty, COUNT(*) as sales_count, MAX(transactions.created_at) as last_sale_at')
            ->first();
        
        // Последняя продажа
        $lastSale = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transaction_items.variant_id', $variant->id)
            ->where('transactions.type', 'sale')
            ->orderBy('transactions.created_at', 'desc')
            ->value('transactions.created_at');
        
        // Средняя продажа в день
        $avgDailySales = $salesData && $salesData->total_qty > 0 
            ? $salesData->total_qty / 30 
            : 0;
        
        // Оборачиваемость (дней между продажами)
        $turnoverDays = $avgDailySales > 0 ? round(1 / $avgDailySales, 1) : 999;
        
        // Статус
        $isSlowMoving = $lastSale && strtotime($lastSale) < strtotime('-30 days');
        $isOutOfStock = $totalQty <= 0;
        
        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'name' => $variant->product->name,
                'size' => $variant->size,
                'color' => $variant->color,
                'current_qty' => $totalQty,
                'supplier_price' => $variant->supplier_price,
                'last_sale_at' => $lastSale,
                'sales_last_30_days' => $salesData ? $salesData->total_qty : 0,
                'avg_daily_sales' => round($avgDailySales, 2),
                'turnover_days' => $turnoverDays,
                'is_slow_moving' => $isSlowMoving,
                'is_out_of_stock' => $isOutOfStock,
            ]
        ]);
    }

    /**
     * Сохранение закупки.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.supplier_price' => 'required|numeric|min:0',
            'location_type' => 'required|in:shop,warehouse',
            'shop_id' => 'nullable|exists:shops,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'comment' => 'nullable|string',
            'purchase_date' => 'required|date',
            'invoice_number' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $invoiceNumber = $validated['invoice_number'] ?? null;

            foreach ($validated['items'] as $item) {
                $variant = ProductVariant::findOrFail($item['variant_id']);

                // Генерируем номер накладной если не указан
                if (!$invoiceNumber) {
                    $invoiceNumber = 'PUR-' . strtoupper(uniqid());
                }

                $purchase = Purchase::create([
                    'invoice_number' => $invoiceNumber,
                    'variant_id' => $item['variant_id'],
                    'shop_id' => $validated['location_type'] === 'shop' ? ($validated['shop_id'] ?? null) : null,
                    'warehouse_id' => $validated['location_type'] === 'warehouse' ? ($validated['warehouse_id'] ?? null) : null,
                    'quantity' => $item['quantity'],
                    'supplier_price' => $item['supplier_price'],
                    'total_amount' => $item['supplier_price'] * $item['quantity'],
                    'received_by' => auth()->id(),
                    'comment' => $validated['comment'] ?? null,
                    'purchase_date' => $validated['purchase_date'],
                ]);

                // Обновляем цену закупки в варианте товара
                $variant->update([
                    'supplier_price' => $item['supplier_price']
                ]);

                // Создаем или обновляем остатки
                if ($validated['location_type'] === 'shop') {
                    $shopId = $validated['shop_id'] ?? null;
                    if ($shopId) {
                        Stock::updateOrCreate(
                            ['variant_id' => $item['variant_id'], 'shop_id' => $shopId],
                            ['quantity' => DB::raw('quantity + ' . $item['quantity'])]
                        );
                    }
                } else {
                    $warehouseId = $validated['warehouse_id'] ?? null;
                    if ($warehouseId) {
                        Stock::updateOrCreate(
                            ['variant_id' => $item['variant_id'], 'warehouse_id' => $warehouseId],
                            ['quantity' => DB::raw('quantity + ' . $item['quantity'])]
                        );
                    }
                }
                
                $totalAmount += $item['supplier_price'] * $item['quantity'];
            }

            DB::commit();

            return redirect()->route('admin.purchases.index')
                ->with('success', 'Закупка успешно создана на сумму ' . number_format($totalAmount, 2) . ' сом');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ошибка при создании закупки: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Удаление закупки.
     */
    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();
        try {
            // Возвращаем товар на склад (уменьшаем остаток)
            if ($purchase->shop_id) {
                $stock = Stock::where('variant_id', $purchase->variant_id)
                    ->where('shop_id', $purchase->shop_id)
                    ->first();
                if ($stock) {
                    $stock->decrement('quantity', $purchase->quantity);
                }
            } elseif ($purchase->warehouse_id) {
                $stock = Stock::where('variant_id', $purchase->variant_id)
                    ->where('warehouse_id', $purchase->warehouse_id)
                    ->first();
                if ($stock) {
                    $stock->decrement('quantity', $purchase->quantity);
                }
            }

            // Логируем удаление
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'purchase_deleted',
                'model_type' => Purchase::class,
                'model_id' => $purchase->id,
                'description' => 'Удалена закупка #' . ($purchase->invoice_number ?? $purchase->id) . ' от ' . $purchase->variant->product->name,
                'old_values' => [
                    'variant_id' => $purchase->variant_id,
                    'quantity' => $purchase->quantity,
                    'supplier_price' => $purchase->supplier_price,
                    'total_amount' => $purchase->total_amount,
                ],
            ]);

            $purchase->delete();

            DB::commit();

            return back()->with('success', 'Закупка удалена');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ошибка при удалении: ' . $e->getMessage()]);
        }
    }
}
