<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Purchase;
use App\Models\ProductVariant;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Страница профиля кладовщика.
     */
    public function profile()
    {
        $user = auth()->user();
        return view('warehouse.profile.index', compact('user'));
    }

    /**
     * Обновление профиля кладовщика.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        if ($request->filled('full_name')) {
            $user->full_name = $validated['full_name'];
        }
        
        if ($request->filled('email')) {
            $user->email = $validated['email'];
        }
        
        if ($request->filled('phone')) {
            $user->phone = $validated['phone'];
        }
        
        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'profile_update',
            'description' => 'Обновлён профиль пользователя',
        ]);

        return back()->with('success', 'Профиль обновлён');
    }

    public function purchases()
    {
        $purchases = Purchase::with(['variant.product', 'receivedBy'])->orderBy('created_at', 'desc')->paginate(20);
        return view('warehouse.purchases.index', compact('purchases'));
    }

    public function createPurchase()
    {
        $products = Product::with(['variants'])->get();
        return view('warehouse.purchases.create', compact('products'));
    }

    public function storePurchase(Request $request)
    {
        // ОТЛАДКА - записываем все данные запроса
        \Log::info('=== ЗАКУПКА НАЧАЛО ===', [
            'all_data' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'variant_id' => 'nullable|exists:product_variants,id',
                'quantity' => 'required|integer|min:1',
                'supplier_price' => 'required|numeric|min:0',
                'supplier_name' => 'nullable|string|max:255',
                'comment' => 'nullable|string',
                'shop_id' => 'nullable|exists:shops,id',
                'warehouse_id' => 'nullable|exists:warehouses,id',
            ]);

            \Log::info('ВАЛИДАЦИЯ ПРОШЛА', $validated);

            $variant = null;
            
            // Если variant_id не указан, создаём новый вариант
            if (empty($validated['variant_id'])) {
                $product = Product::findOrFail($validated['product_id']);
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => 'Стандарт',
                    'color' => null,
                    'sku' => $product->article . '-STD-' . time(),
                    'supplier_price' => $validated['supplier_price'],
                    'wholesale_price' => null,
                    'is_active' => true,
                ]);
                \Log::info('Создан новый вариант', ['variant_id' => $variant->id]);
            } else {
                $variant = ProductVariant::findOrFail($validated['variant_id']);
                // Обновляем цену закупки
                $variant->update(['supplier_price' => $validated['supplier_price']]);
                \Log::info('Используем существующий вариант', ['variant_id' => $variant->id]);
            }

            // Создаём запись закупки
            $purchase = Purchase::create([
                'variant_id' => $variant->id,
                'quantity' => $validated['quantity'],
                'price' => $validated['supplier_price'],
                'total_price' => $validated['quantity'] * $validated['supplier_price'],
                'supplier_name' => $validated['supplier_name'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'shop_id' => $validated['shop_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'received_by' => auth()->id(),
                'status' => 'completed',
            ]);

            \Log::info('Создана закупка', ['purchase_id' => $purchase->id]);

            // Обновляем остатки на складе/магазине
            $stock = null;
            
            if (!empty($validated['shop_id'])) {
                $stock = Stock::firstOrCreate(
                    [
                        'variant_id' => $variant->id,
                        'shop_id' => $validated['shop_id'],
                    ],
                    ['quantity' => 0, 'min_stock' => 5]
                );
                $stock->increment('quantity', $validated['quantity']);
                \Log::info('Обновлены остатки в магазине', [
                    'shop_id' => $validated['shop_id'],
                    'new_quantity' => $stock->quantity,
                ]);
            } elseif (!empty($validated['warehouse_id'])) {
                $stock = Stock::firstOrCreate(
                    [
                        'variant_id' => $variant->id,
                        'warehouse_id' => $validated['warehouse_id'],
                    ],
                    ['quantity' => 0, 'min_stock' => 5]
                );
                $stock->increment('quantity', $validated['quantity']);
                \Log::info('Обновлены остатки на складе', [
                    'warehouse_id' => $validated['warehouse_id'],
                    'new_quantity' => $stock->quantity,
                ]);
            }

            // Логируем активность
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'purchase_create',
                'description' => "Создана закупка: {$variant->product->name} x{$validated['quantity']}",
                'model_type' => Purchase::class,
                'model_id' => $purchase->id,
            ]);

            \Log::info('=== ЗАКУПКА УСПЕШНО ===', ['purchase_id' => $purchase->id]);

            return redirect()->route('warehouse.purchases.index')
                ->with('success', 'Приёмка оформлена: ' . $variant->product->name . ' x' . $validated['quantity']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('=== ЗАКУПКА ОШИБКА ВАЛИДАЦИИ ===', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('=== ЗАКУПКА ОШИБКА ===', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            
            return back()->with('error', 'Ошибка при оформлении закупки: ' . $e->getMessage())->withInput();
        }
    }

    public function editPurchase($id)
    {
        $purchase = Purchase::with(['variant.product'])->findOrFail($id);
        return view('warehouse.purchases.edit', compact('purchase'));
    }

    public function updatePurchase(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        return redirect()->route('warehouse.purchases.index')->with('success', 'Приёмка обновлена');
    }

    public function stock()
    {
        $stock = Stock::with(['variant.product', 'shop', 'warehouse'])->paginate(50);
        return view('warehouse.stock.index', compact('stock'));
    }

    public function lowStock()
    {
        $lowStock = Stock::whereColumn('quantity', '<=', 'min_stock')
            ->with(['variant.product', 'shop', 'warehouse'])
            ->paginate(50);
        return view('warehouse.stock.low', compact('lowStock'));
    }

    public function adjustStock(Request $request)
    {
        return redirect()->back()->with('success', 'Остатки обновлены');
    }

    public function inventory()
    {
        return view('warehouse.inventory.index');
    }

    public function startInventory(Request $request)
    {
        return redirect()->back()->with('success', 'Инвентаризация начата');
    }

    public function completeInventory(Request $request)
    {
        return redirect()->back()->with('success', 'Инвентаризация завершена');
    }

    public function qrCodes()
    {
        return view('warehouse.qr.index');
    }

    public function generateQr(Request $request)
    {
        return redirect()->back()->with('success', 'QR сгенерирован');
    }

    public function printQr(Request $request)
    {
        return redirect()->back()->with('success', 'QR распечатан');
    }

    public function writeoff()
    {
        return view('warehouse.writeoff.index');
    }

    public function createWriteoff(Request $request)
    {
        return redirect()->back()->with('success', 'Списание создано');
    }

    public function pendingWriteoff()
    {
        return view('warehouse.writeoff.pending');
    }

    public function transfers()
    {
        return view('warehouse.transfers.index');
    }

    public function createTransfer(Request $request)
    {
        return redirect()->back()->with('success', 'Перемещение создано');
    }

    public function products()
    {
        $products = Product::with(['category', 'variants'])->paginate(50);
        return view('warehouse.products.index', compact('products'));
    }

    public function shops()
    {
        return view('warehouse.shops.index');
    }
}
