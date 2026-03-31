<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Warehouse\Models\Product;
use Modules\Warehouse\Models\ProductVariant;
use Modules\Warehouse\Models\Category;
use Modules\Warehouse\Models\Stock;
use Modules\Warehouse\Models\Warehouse as WarehouseModel;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants']);
        
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('article', 'like', "%{$search}%");
            });
        }
        
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }
        
        $products = $query->paginate(20);
        $categories = Category::all();
        
        return view('warehouse::products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('warehouse::products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'required|string|unique:products,article',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string',
            'material' => 'nullable|string',
            'country' => 'nullable|string',
            'retail_price' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'nullable|string',
            'variants.*.color' => 'nullable|string',
            'variants.*.supplier_price' => 'required|numeric|min:0',
            'variants.*.quantity' => 'required|integer|min:0',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'article' => $validated['article'],
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'material' => $validated['material'] ?? null,
            'country' => $validated['country'] ?? null,
            'retail_price' => $validated['retail_price'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Сохранить фото
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
            $product->update(['photo' => $path]);
        }

        // Создать варианты
        foreach ($validated['variants'] as $variantData) {
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'size' => $variantData['size'] ?? null,
                'color' => $variantData['color'] ?? null,
                'sku' => $product->article . '-' . strtoupper(uniqid()),
                'supplier_price' => $variantData['supplier_price'],
                'is_active' => true,
            ]);

            // Создать остаток на складе
            if ($variantData['quantity'] > 0) {
                Stock::create([
                    'variant_id' => $variant->id,
                    'warehouse_id' => 1, // Основной склад
                    'quantity' => $variantData['quantity'],
                    'min_stock' => 5,
                ]);
            }
        }

        return redirect()->route('warehouse.products.index')
            ->with('success', 'Товар успешно добавлен');
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'category']);
        $categories = Category::all();
        return view('warehouse::products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'required|string|unique:products,article,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string',
            'material' => 'nullable|string',
            'country' => 'nullable|string',
            'retail_price' => 'nullable|numeric|min:0',
        ]);

        $product->update($validated);

        return back()->with('success', 'Товар обновлен');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Товар удален');
    }
}
