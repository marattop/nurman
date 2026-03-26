<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\Shop;
use App\Models\Warehouse;
use App\Models\QrCode;
use App\Models\TransactionItem;
use App\Models\ActivityLog;

/**
 * Контроллер сканера QR-кодов
 * QR-код содержит только артикул товара
 * Показывает полную информацию: цена, остатки, размеры, магазины, склад/витрина
 */
class QrScannerController extends Controller
{
    /**
     * Страница сканера QR-кодов
     */
    public function scan()
    {
        return view('admin.qr.scan');
    }

    /**
     * Обработка отсканированного QR-кода
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_data' => ['required', 'string', 'max:255'],
        ]);

        $qrData = trim($request->qr_data);
        
        // Логирование для отладки
        \Log::info('QR Scan received: ' . $qrData);

        // Пытаемся извлечь артикул из JSON или используем как есть
        $article = $qrData;
        try {
            $parsed = json_decode($qrData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                if (isset($parsed['article'])) {
                    $article = $parsed['article'];
                    \Log::info('Extracted article from JSON: ' . $article);
                } elseif (isset($parsed['coupon_id'])) {
                    // Это купонный QR
                    return $this->processCouponScan($parsed);
                }
            }
        } catch (\Exception $e) {
            // Игнорируем ошибки парсинга, используем как есть
            \Log::error('JSON parse error: ' . $e->getMessage());
        }

        \Log::info('Searching for product with article: ' . $article);

        // Ищем товар по артикулу
        $product = Product::where('article', $article)->first();

        if (!$product) {
            \Log::warning('Product not found: ' . $article);
            
            // Для отладки - покажем все артикулы в базе
            $allArticles = Product::pluck('article')->toArray();
            
            return response()->json([
                'success' => false,
                'message' => 'Товар с артикулом "' . $article . '" не найден',
                'scanned_data' => $qrData,
                'parsed_article' => $article,
                'available_articles' => $allArticles,
            ]);
        }
        
        \Log::info('Product found: ' . $product->name);

        // Получаем все варианты товара с полной информацией
        $variants = $this->getVariantsWithStock($product);

        // "Наш" QR: проверяем, существует ли QR-код в системе для этого товара
        $isKnownQr = QrCode::whereIn('variant_id', function ($q) use ($product) {
            $q->select('id')->from('product_variants')->where('product_id', $product->id);
        })->exists();

        // Если QR не найден в базе, но артикул верный - всё равно показываем товар
        // Это позволяет сканировать даже QR без is_printed

        // Считаем общие остатки
        $totalStock = $variants->sum('total_quantity');
        $totalShowcase = $variants->sum('showcase_quantity');
        $totalWarehouse = $variants->sum('warehouse_quantity');
        $totalSold = $this->getTotalSold($product);

        return response()->json([
            'success' => true,
            'is_known_qr' => $isKnownQr,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'article' => $product->article,
                'brand' => $product->brand,
                'category' => $product->category?->name ?? '—',
                'photo' => $product->photo ? asset('storage/' . $product->photo) : null,
                'retail_price' => $product->retail_price ?? 0,
                'supplier_price' => $product->supplier_price ?? 0,
                'wholesale_price' => $product->wholesale_price ?? 0,
                'variants' => $variants,
            ],
            'summary' => [
                'total_stock' => $totalStock,
                'shop_stock' => $totalShowcase,
                'warehouse_stock' => $totalWarehouse,
                'sold' => $totalSold,
                'variants_count' => $variants->count(),
            ],
            'variants' => $variants,
        ]);
    }

    /**
     * Получение всех вариантов товара с остатками по магазинам и складам
     */
    private function getVariantsWithStock($product)
    {
        $variants = ProductVariant::where('product_id', $product->id)
            ->with(['stock.shop', 'stock.warehouse'])
            ->get()
            ->map(function ($variant) {
                $stockData = $variant->stock->groupBy(function ($item) {
                    return $item->shop_id ? 'shop' : 'warehouse';
                });

                $shops = [];
                $showcaseQty = 0;
                $warehouseQty = 0;

                // Остатки по магазинам (витрина)
                if ($stockData->has('shop')) {
                    foreach ($stockData->get('shop') as $stock) {
                        $shops[] = [
                            'id' => $stock->shop->id ?? null,
                            'name' => $stock->shop->name ?? '—',
                            'quantity' => $stock->quantity,
                            'reserved' => $stock->reserved,
                            'available' => $stock->quantity - $stock->reserved,
                        ];
                        $showcaseQty += $stock->quantity;
                    }
                }

                // Остатки на складах
                if ($stockData->has('warehouse')) {
                    foreach ($stockData->get('warehouse') as $stock) {
                        $warehouseQty += $stock->quantity;
                    }
                }

                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'barcode' => $variant->barcode,
                    'wholesale_price' => number_format($variant->wholesale_price ?? 0, 2, ',', ' '),
                    'supplier_price' => number_format($variant->supplier_price ?? 0, 2, ',', ' '),
                    'total_quantity' => $showcaseQty + $warehouseQty,
                    'showcase_quantity' => $showcaseQty,
                    'warehouse_quantity' => $warehouseQty,
                    'shops' => $shops,
                ];
            });

        return $variants;
    }

    /**
     * Получение общего количества проданных товаров
     */
    private function getTotalSold($product)
    {
        return TransactionItem::whereHas('variant', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->sum('quantity');
    }

    /**
     * Перемещение товара между складом и витриной
     */
    public function moveStock(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'from' => 'required|in:warehouse,showcase',
            'to' => 'required|in:warehouse,showcase',
            'shop_id' => 'required_if:from,showcase|nullable|exists:shops,id',
            'warehouse_id' => 'required_if:from,warehouse|nullable|exists:warehouses,id',
            'target_shop_id' => 'required_if:to,showcase|nullable|exists:shops,id',
            'target_warehouse_id' => 'required_if:to,warehouse|nullable|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $variant = ProductVariant::findOrFail($request->variant_id);
            $quantity = (int) $request->quantity;

            if ($request->from === 'showcase') {
                // С витрины на склад
                $sourceStock = Stock::where('variant_id', $variant->id)
                    ->where('shop_id', $request->shop_id)
                    ->first();

                if (!$sourceStock || $sourceStock->quantity < $quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Недостаточно товара на витрине',
                    ]);
                }

                // Уменьшаем на витрине
                $sourceStock->quantity -= $quantity;
                $sourceStock->save();

                // Увеличиваем на складе
                $targetStock = Stock::firstOrCreate(
                    [
                        'variant_id' => $variant->id,
                        'warehouse_id' => $request->target_warehouse_id,
                        'shop_id' => null,
                    ],
                    ['quantity' => 0, 'reserved' => 0]
                );
                $targetStock->quantity += $quantity;
                $targetStock->save();

            } else {
                // Со склада на витрину
                $sourceStock = Stock::where('variant_id', $variant->id)
                    ->whereNull('shop_id')
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                if (!$sourceStock || $sourceStock->quantity < $quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Недостаточно товара на складе',
                    ]);
                }

                // Уменьшаем на складе
                $sourceStock->quantity -= $quantity;
                $sourceStock->save();

                // Увеличиваем на витрине
                $targetStock = Stock::firstOrCreate(
                    [
                        'variant_id' => $variant->id,
                        'shop_id' => $request->target_shop_id,
                        'warehouse_id' => null,
                    ],
                    ['quantity' => 0, 'reserved' => 0]
                );
                $targetStock->quantity += $quantity;
                $targetStock->save();
            }

            DB::commit();

            // Логируем перемещение
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'QR: Перемещение товара',
                'description' => "Товар {$variant->product->name} ({$variant->size}) перемещен: {$request->from} → {$request->to}, кол-во: {$quantity}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Товар успешно перемещен',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при перемещении: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Получение складов и магазинов для модального окна
     */
    public function getLocations()
    {
        return response()->json([
            'shops' => Shop::where('is_active', true)
                ->select('id', 'name')
                ->get(),
            'warehouses' => Warehouse::where('is_active', true)
                ->select('id', 'name')
                ->get(),
        ]);
    }

    /**
     * Обработка сканирования купонного QR
     */
    private function processCouponScan($parsed)
    {
        $coupon = Coupon::find($parsed['coupon_id']);
        
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Купон не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'type' => 'coupon',
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'is_active' => $coupon->is_active,
                'customer' => $coupon->customer ? [
                    'name' => $coupon->customer->full_name,
                    'phone' => $coupon->customer->phone,
                ] : null,
            ],
        ]);
    }
}
