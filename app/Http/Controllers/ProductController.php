<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\QrCode;
use App\Models\Shop;
use App\Models\Warehouse;
use App\Models\Stock;

class ProductController extends Controller
{
    /**
     * Список товаров
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants', 'createdBy']);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('article', 'like', "%{$request->search}%")
                  ->orWhere('brand', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        
        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Страниц создания товара
     */
    public function create()
    {
        $categories = Category::all();
        $nextArticle = $this->generateArticle();
        return view('admin.products.create', compact('categories', 'nextArticle'));
    }

    /**
     * Сохранить товар
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'required|string|unique:products,article',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'retail_price' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.supplier_price' => 'nullable|numeric|min:0',
            'variants.*.wholesale_price' => 'nullable|numeric|min:0',
            'variants.*.barcode' => 'nullable|numeric',
        ], [
            'name.required' => 'Введите название товара',
            'article.required' => 'Введите артикул',
            'article.unique' => 'Такой артикул уже существует',
            'variants.required' => 'Добавьте хотя бы один вариант товара',
            'variants.*.supplier_price.numeric' => 'Закупочная цена должна быть числом',
        ]);

        DB::beginTransaction();
        try {
            // Сохранение фото
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('products', 'public');
            }
            
            $photoPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $photoPaths[] = $photo->store('products', 'public');
                }
            }

            // Создание товара
            $product = Product::create([
                'name' => $validated['name'],
                'article' => $validated['article'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'brand' => $validated['brand'],
                'material' => $validated['material'],
                'country' => $validated['country'],
                'photo' => $photoPath,
                'photos' => $photoPaths,
                'retail_price' => $validated['retail_price'],
                'is_active' => $request->has('is_active'),
                'created_by' => auth()->id(),
            ]);

            // Создание вариантов и QR-кодов
            foreach ($validated['variants'] as $variantData) {
                $barcode = $variantData['barcode'] ?? $this->generateBarcode();
                $supplierPrice = $variantData['supplier_price'] ?? 0;
                $wholesalePrice = $variantData['wholesale_price'] ?? null;

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variantData['size'],
                    'color' => $variantData['color'],
                    'sku' => $product->article . '-' . strtoupper(substr(md5($variantData['size'] . $variantData['color']), 0, 6)),
                    'supplier_price' => $supplierPrice,
                    'wholesale_price' => $wholesalePrice,
                    'barcode' => $barcode,
                    'is_active' => true,
                ]);

                // Генерация QR-кода
                $qrData = [
                    'type' => 'product',
                    'variant_id' => $variant->id,
                    'product_id' => $product->id,
                    'article' => $product->article,
                    'barcode' => $barcode,
                ];
                
                // Генерируем QR-код с простыми данными
                $qrCodeContent = json_encode($qrData, JSON_UNESCAPED_UNICODE);
                $qrCodePath = 'qrcodes/' . $product->article . '-' . $variant->id . '.png';

                // QR Server API
                $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrCodeContent);
                
                // Скачиваем и сохраняем
                $qrImageContent = file_get_contents($qrImageUrl);
                if ($qrImageContent !== false) {
                    Storage::disk('public')->put($qrCodePath, $qrImageContent);
                } else {
                    Storage::disk('public')->put($qrCodePath, '');
                }
                
                QrCode::create([
                    'variant_id' => $variant->id,
                    'qr_code_path' => $qrCodePath,
                    'qr_data' => $qrData,
                    'is_printed' => false,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.products')->with('success', 'Товар создан');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ошибка при создании товара: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Ошибка: ' . $e->getMessage() . ' (файл: ' . $e->getFile() . ':' . $e->getLine() . ')'])->withInput();
        }
    }

    /**
     * Страница редактирования товара
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load(['variants.qrCodes', 'category']);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Обновить товар
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'required|string|unique:products,article,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'retail_price' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'article' => $validated['article'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'brand' => $validated['brand'],
            'material' => $validated['material'],
            'country' => $validated['country'],
            'retail_price' => $validated['retail_price'],
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('photo')) {
            if ($product->photo) Storage::disk('public')->delete($product->photo);
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }
        
        if ($request->hasFile('photos')) {
            if ($product->photos) {
                foreach ($product->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('products', 'public');
            }
            $data['photos'] = $photoPaths;
        }

        $product->update($data);
        return redirect()->route('admin.products')->with('success', 'Товар обновлён');
    }

    /**
     * Удалить товар
     */
    public function destroy(Product $product)
    {
        // Удаляем фото
        if ($product->photo) Storage::disk('public')->delete($product->photo);
        if ($product->photos) {
            foreach ($product->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }
        
        // Удаляем QR-коды
        foreach ($product->variants as $variant) {
            foreach ($variant->qrCodes as $qrCode) {
                Storage::disk('public')->delete($qrCode->qr_code_path);
            }
        }
        
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Товар удалён');
    }

    /**
     * Просмотр товара с полной информацией
     */
    public function show(Product $product)
    {
        $product->load(['variants.qrCodes', 'variants.stock.shop', 'variants.stock.warehouse', 'category']);
        
        $shops = Shop::all();
        $warehouses = Warehouse::all();
        
        return view('admin.products.show', compact('product', 'shops', 'warehouses'));
    }

    /**
     * Получить информацию по QR-коду
     */
    public function scanQr(Request $request)
    {
        $qrData = $request->input('qr_data');
        
        try {
            $data = json_decode($qrData, true);
            
            if (!isset($data['variant_id'])) {
                return response()->json(['error' => 'Неверный формат QR-кода'], 400);
            }
            
            $variant = ProductVariant::with([
                'product.category',
                'stock.shop',
                'stock.warehouse',
                'qrCodes'
            ])->findOrFail($data['variant_id']);
            
            $stockInfo = [];
            foreach ($variant->stock as $s) {
                $stockInfo[] = [
                    'location' => $s->shop ? 'Магазин: ' . $s->shop->name : 'Склад: ' . ($s->warehouse->name ?? 'Неизвестно'),
                    'quantity' => $s->quantity,
                    'reserved' => $s->reserved,
                    'available' => $s->available,
                    'min_stock' => $s->min_stock,
                    'is_low' => $s->isLowStock(),
                ];
            }
            
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $variant->product->id,
                    'name' => $variant->product->name,
                    'article' => $variant->product->article,
                    'category' => $variant->product->category->name ?? '-',
                    'brand' => $variant->product->brand ?? '-',
                    'retail_price' => $variant->product->retail_price,
                ],
                'variant' => [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'barcode' => $variant->barcode,
                    'size' => $variant->size ?? '-',
                    'color' => $variant->color ?? '-',
                    'supplier_price' => $variant->supplier_price,
                    'wholesale_price' => $variant->wholesale_price,
                ],
                'stock' => $stockInfo,
                'total' => [
                    'total_quantity' => $variant->total_stock,
                    'total_reserved' => $variant->stock->sum('reserved'),
                    'total_available' => $variant->available_stock,
                ],
                'qr_code' => $variant->qrCodes->first()?->qr_code_path,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Товар не найден', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Страница сканера QR-кода
     */
    public function scanPage()
    {
        return view('admin.products.scan');
    }

    /**
     * Сгенерировать артикул
     */
    private function generateArticle(): string
    {
        $prefix = date('Ymd');
        $lastProduct = Product::where('article', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastProduct) {
            $lastNum = (int) substr($lastProduct->article, -4);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }
        
        return $prefix . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Сгенерировать штрих-код
     */
    private function generateBarcode(): int
    {
        return random_int(100000000, 999999999);
    }

    /**
     * Скачать QR-код
     */
    public function downloadQr(QrCode $qrCode)
    {
        $file = Storage::disk('public')->path($qrCode->qr_code_path);
        return response()->download($file);
    }

    /**
     * Печать QR-кода
     */
    public function printQr(QrCode $qrCode)
    {
        $qrCode->markAsPrinted(auth()->id());
        $file = Storage::disk('public')->path($qrCode->qr_code_path);
        return response()->file($file);
    }
}
