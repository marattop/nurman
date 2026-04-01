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

class WriteoffController extends Controller
{
    public function index()
    {
        $writeoffs = \Modules\Warehouse\Models\Writeoff::with(['createdBy', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('warehouse::writeoff.index', compact('writeoffs'));
    }

    public function create()
    {
        $shops = Shop::where('is_active', true)->get();
        $warehouses = WarehouseModel::where('is_active', true)->get();
        $products = Product::with(['variants.stock'])->where('is_active', true)->get();
        
        return view('warehouse::writeoff.create', compact('shops', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        \Log::info('Writeoff store started', ['request' => $request->all()]);
        
        $validated = $request->validate([
            'reason' => 'required|in:damage,loss,expired,other',
            'location_type' => 'required|in:shop,warehouse',
            'shop_id' => 'nullable|integer',
            'warehouse_id' => 'nullable|integer',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'comment' => 'nullable|string',
        ]);

        $locationId = $validated['location_type'] === 'shop' ? $validated['shop_id'] : $validated['warehouse_id'];
        
        if (!$locationId) {
            return back()->withErrors(['error' => 'Выберите местоположение'])
                ->withInput();
        }

        \Log::info('Writeoff validated', ['validated' => $validated]);

        DB::beginTransaction();
        try {
            $writeoffCount = 0;
            
            foreach ($validated['items'] as $item) {
                $variant = ProductVariant::findOrFail($item['variant_id']);
                
                \Log::info('Processing writeoff item', ['variant' => $variant, 'quantity' => $item['quantity']]);
                
                // Находим остаток
                if ($validated['location_type'] === 'shop') {
                    $stock = Stock::where('variant_id', $item['variant_id'])
                        ->where('shop_id', $validated['shop_id'])
                        ->whereNull('warehouse_id')
                        ->first();
                } else {
                    $stock = Stock::where('variant_id', $item['variant_id'])
                        ->whereNull('shop_id')
                        ->where('warehouse_id', $validated['warehouse_id'])
                        ->first();
                }

                \Log::info('Source stock', ['stock' => $stock]);

                if (!$stock || $stock->quantity < $item['quantity']) {
                    DB::rollBack();
                    \Log::error('Not enough stock for writeoff', [
                        'variant_id' => $item['variant_id'],
                        'available' => $stock?->quantity ?? 0,
                        'requested' => $item['quantity']
                    ]);
                    return back()->withErrors(['error' => 'Недостаточно товара: ' . $variant->product->name . ' (доступно: ' . ($stock?->quantity ?? 0) . ', запрошено: ' . $item['quantity'] . ')'])
                        ->withInput();
                }

                // Уменьшаем остаток
                $stock->decrement('quantity', $item['quantity']);
                \Log::info('Decremented stock for writeoff', ['new_quantity' => $stock->quantity]);

                // Создаём запись о списании
                $writeoff = \Modules\Warehouse\Models\Writeoff::create([
                    'reason' => $validated['reason'],
                    'location_type' => $validated['location_type'],
                    'shop_id' => $validated['location_type'] === 'shop' ? $locationId : null,
                    'warehouse_id' => $validated['location_type'] === 'warehouse' ? $locationId : null,
                    'comment' => $validated['comment'] ?? null,
                    'created_by' => auth()->id(),
                ]);
                
                $writeoff->items()->create([
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                ]);
                
                $writeoffCount++;
                \Log::info('Writeoff created', ['writeoff_id' => $writeoff->id]);

                // Логируем списание
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'writeoff_created',
                    'model_type' => Stock::class,
                    'description' => "Списание: {$variant->product->name} ({$variant->size}), причина: " . 
                        $this->getReasonName($validated['reason']) . 
                        ", кол-во: {$item['quantity']}",
                    'new_values' => [
                        'variant_id' => $item['variant_id'],
                        'reason' => $validated['reason'],
                        'quantity' => $item['quantity'],
                    ],
                ]);
            }

            DB::commit();
            \Log::info('Writeoff completed successfully', ['count' => $writeoffCount]);
            
            return redirect()->route('warehouse.writeoff.index')
                ->with('success', 'Списание успешно создано (' . $writeoffCount . ' тов.)');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Writeoff failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Ошибка при списании: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function pending()
    {
        return view('warehouse::writeoff.pending');
    }

    private function getReasonName($reason)
    {
        $reasons = [
            'damage' => 'Брак / Повреждение',
            'loss' => 'Потеря',
            'expired' => 'Истёк срок годности',
            'other' => 'Другое',
        ];
        return $reasons[$reason] ?? $reason;
    }
}
