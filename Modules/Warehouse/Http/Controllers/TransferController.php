<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\Shop;
use App\Models\Warehouse as WarehouseModel;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = \Modules\Warehouse\Models\Transfer::with(['createdBy', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('warehouse::transfers.index', compact('transfers'));
    }

    public function create()
    {
        $shops = Shop::where('is_active', true)->get();
        $warehouses = WarehouseModel::where('is_active', true)->get();
        // Используем App\Models\Product вместо Modules\Warehouse\Models\Product
        $products = \App\Models\Product::with(['variants.stock'])->where('is_active', true)->get();
        
        return view('warehouse::transfers.create', compact('shops', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        \Log::info('Transfer store started', ['request' => $request->all()]);
        
        $validated = $request->validate([
            'from_type' => 'required|in:shop,warehouse',
            'from_id' => 'nullable|integer',
            'from_shop_id' => 'nullable|integer',
            'to_type' => 'required|in:shop,warehouse',
            'to_id' => 'nullable|integer',
            'to_shop_id' => 'nullable|integer',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'comment' => 'nullable|string',
        ]);

        // Определяем откуда и куда на основе типа
        $fromId = $validated['from_type'] === 'warehouse' ? $validated['from_id'] : $validated['from_shop_id'];
        $toId = $validated['to_type'] === 'warehouse' ? $validated['to_id'] : $validated['to_shop_id'];
        
        if (!$fromId || !$toId) {
            return back()->withErrors(['error' => 'Выберите пункты отправления и назначения'])
                ->withInput();
        }

        \Log::info('Transfer validated', [
            'from_type' => $validated['from_type'],
            'from_id' => $fromId,
            'to_type' => $validated['to_type'],
            'to_id' => $toId,
            'items' => $validated['items']
        ]);

        DB::beginTransaction();
        try {
            $transfersCount = 0;
            
            foreach ($validated['items'] as $item) {
                // Используем App\Models\ProductVariant
                $variant = \App\Models\ProductVariant::findOrFail($item['variant_id']);
                
                \Log::info('Processing item', ['variant' => $variant, 'quantity' => $item['quantity']]);
                
                // Находим остаток откуда
                if ($validated['from_type'] === 'shop') {
                    $sourceStock = Stock::where('variant_id', $item['variant_id'])
                        ->where('shop_id', $fromId)
                        ->whereNull('warehouse_id')
                        ->first();
                } else {
                    $sourceStock = Stock::where('variant_id', $item['variant_id'])
                        ->whereNull('shop_id')
                        ->where('warehouse_id', $fromId)
                        ->first();
                }

                \Log::info('Source stock', ['sourceStock' => $sourceStock]);

                if (!$sourceStock || $sourceStock->quantity < $item['quantity']) {
                    DB::rollBack();
                    \Log::error('Not enough stock', [
                        'variant_id' => $item['variant_id'],
                        'available' => $sourceStock?->quantity ?? 0,
                        'requested' => $item['quantity']
                    ]);
                    return back()->withErrors(['error' => 'Недостаточно товара: ' . $variant->product->name . ' (доступно: ' . ($sourceStock?->quantity ?? 0) . ', запрошено: ' . $item['quantity'] . ')'])
                        ->withInput();
                }

                // Уменьшаем остаток откуда
                $sourceStock->decrement('quantity', $item['quantity']);
                \Log::info('Decremented source stock', ['new_quantity' => $sourceStock->quantity]);

                // Находим остаток куда
                if ($validated['to_type'] === 'shop') {
                    $targetStock = Stock::firstOrCreate(
                        ['variant_id' => $item['variant_id'], 'shop_id' => $toId, 'warehouse_id' => null],
                        ['quantity' => 0]
                    );
                } else {
                    $targetStock = Stock::firstOrCreate(
                        ['variant_id' => $item['variant_id'], 'shop_id' => null, 'warehouse_id' => $toId],
                        ['quantity' => 0]
                    );
                }

                // Увеличиваем остаток куда
                $targetStock->increment('quantity', $item['quantity']);
                \Log::info('Incremented target stock', ['new_quantity' => $targetStock->quantity]);

                // Создаём запись о перемещении
                $transfer = \Modules\Warehouse\Models\Transfer::create([
                    'from_type' => $validated['from_type'],
                    'from_id' => $fromId,
                    'to_type' => $validated['to_type'],
                    'to_id' => $toId,
                    'comment' => $validated['comment'] ?? null,
                    'created_by' => auth()->id(),
                ]);
                
                $transfer->items()->create([
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                ]);
                
                $transfersCount++;
                \Log::info('Transfer created', ['transfer_id' => $transfer->id]);

                // Логируем перемещение
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'stock_transfer',
                    'model_type' => Stock::class,
                    'description' => "Перемещение: {$variant->product->name} ({$variant->size}) из " . 
                        ($validated['from_type'] === 'shop' ? 'магазина #' . $fromId : 'склада #' . $fromId) . 
                        ' в ' . ($validated['to_type'] === 'shop' ? 'магазин #' . $toId : 'склад #' . $toId) . 
                        ", кол-во: {$item['quantity']}",
                    'new_values' => [
                        'variant_id' => $item['variant_id'],
                        'from_type' => $validated['from_type'],
                        'from_id' => $fromId,
                        'to_type' => $validated['to_type'],
                        'to_id' => $toId,
                        'quantity' => $item['quantity'],
                    ],
                ]);
            }

            DB::commit();
            \Log::info('Transfer completed successfully', ['count' => $transfersCount]);
            
            return redirect()->route('warehouse.transfers.index')
                ->with('success', 'Перемещение успешно создано (' . $transfersCount . ' тов.)');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transfer failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Ошибка при перемещении: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
