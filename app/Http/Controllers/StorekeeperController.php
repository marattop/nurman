<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorekeeperController extends Controller
{
    /**
     * Дашборд кладовщика
     */
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalVariants = ProductVariant::count();
        $totalStock = Stock::sum('quantity');
        $lowStock = Stock::whereColumn('quantity', '<=', 'min_stock')->count();

        $recentProducts = Product::with(['variants', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('storekeeper.dashboard', compact(
            'totalProducts',
            'totalVariants',
            'totalStock',
            'lowStock',
            'recentProducts'
        ));
    }

    /**
     * Наличие товаров (без цен)
     */
    public function stock()
    {
        $stock = Stock::with(['variant.product', 'variant.product.category', 'shop', 'warehouse'])
            ->orderBy('updated_at', 'desc')
            ->paginate(50);

        return view('storekeeper.stock.index', compact('stock'));
    }

    /**
     * Детальная информация о товаре (характеристики)
     */
    public function productDetail($id)
    {
        $product = Product::with(['variants.stock', 'category'])->findOrFail($id);
        
        // Группируем варианты по размеру и цвету
        $variantsBySize = $product->variants->groupBy('size');
        
        return view('storekeeper.products.detail', compact('product', 'variantsBySize'));
    }

    /**
     * Инвентаризация - главная страница
     */
    public function inventory()
    {
        return view('storekeeper.inventory.index');
    }

    /**
     * Сканирование товара для инвентаризации
     */
    public function scanInventory(Request $request)
    {
        $barcode = $request->input('barcode');
        
        if (!$barcode) {
            return response()->json(['error' => 'Штрих-код не указан'], 400);
        }

        $variant = ProductVariant::with(['product', 'product.category', 'stock'])
            ->where('barcode', $barcode)
            ->first();

        if (!$variant) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }

        $totalQuantity = $variant->stock->sum('quantity');

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $variant->id,
                'name' => $variant->product->name,
                'article' => $variant->product->article,
                'category' => $variant->product->category?->name ?? 'Без категории',
                'size' => $variant->size,
                'color' => $variant->color,
                'barcode' => $variant->barcode,
                'sku' => $variant->sku,
                'quantity' => $totalQuantity,
                'stock_locations' => $variant->stock->map(function ($s) {
                    return [
                        'location' => $s->shop?->name ?? $s->warehouse?->name ?? 'Не указано',
                        'quantity' => $s->quantity,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Сохранение результатов инвентаризации
     */
    public function saveInventory(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'actual_quantity' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:500',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);
        $currentQuantity = $variant->stock->sum('quantity');
        $difference = $validated['actual_quantity'] - $currentQuantity;

        DB::transaction(function () use ($variant, $validated, $difference) {
            // Обновляем остатки
            foreach ($variant->stock as $stockItem) {
                if ($difference >= 0) {
                    $stockItem->increment('quantity', $difference);
                } else {
                    $stockItem->decrement('quantity', abs($difference));
                }
                
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'inventory_adjustment',
                    'description' => "Инвентаризация: {$variant->product->name} ({$variant->size}/{$variant->color}), изменение: {$difference}",
                    'meta' => [
                        'variant_id' => $variant->id,
                        'old_quantity' => $stockItem->quantity,
                        'new_quantity' => $validated['actual_quantity'],
                        'comment' => $validated['comment'] ?? null,
                    ],
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Инвентаризация сохранена']);
    }

    /**
     * Товары по категориям
     */
    public function productsByCategory($categoryId = null)
    {
        $query = Product::with(['variants.stock', 'category']);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->paginate(30);
        $categories = \App\Models\Category::all();

        return view('storekeeper.products.index', compact('products', 'categories'));
    }

    /**
     * Поиск товара по штрих-коду или названию
     */
    public function searchProduct(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['error' => 'Запрос не указан'], 400);
        }

        // Поиск по штрих-коду
        $variant = ProductVariant::with(['product', 'product.category', 'stock'])
            ->where('barcode', $query)
            ->first();

        if ($variant) {
            $totalQuantity = $variant->stock->sum('quantity');
            return response()->json([
                'type' => 'variant',
                'data' => [
                    'id' => $variant->id,
                    'name' => $variant->product->name,
                    'article' => $variant->product->article,
                    'category' => $variant->product->category?->name ?? 'Без категории',
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'barcode' => $variant->barcode,
                    'sku' => $variant->sku,
                    'quantity' => $totalQuantity,
                ]
            ]);
        }

        // Поиск по названию товара
        $products = Product::with(['variants.stock', 'category'])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('article', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'article' => $product->article,
                    'category' => $product->category?->name ?? 'Без категории',
                    'variants_count' => $product->variants->count(),
                    'total_stock' => $product->variants->sum(function ($v) {
                        return $v->stock->sum('quantity');
                    }),
                ];
            });

        return response()->json([
            'type' => 'products',
            'data' => $products
        ]);
    }

    /**
     * Профиль кладовщика
     */
    public function profile()
    {
        $user = auth()->user();
        return view('storekeeper.profile.index', compact('user'));
    }

    /**
     * Обновление профиля
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
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

        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'profile_update',
            'description' => 'Обновлён профиль кладовщика',
        ]);

        return back()->with('success', 'Профиль обновлён');
    }
}
