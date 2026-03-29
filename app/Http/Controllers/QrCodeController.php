<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\QrCode;
use App\Models\ActivityLog;

/**
 * Контроллер управления QR-кодами
 * Генерирует QR-коды содержащие только артикул товара
 */
class QrCodeController extends Controller
{
    /**
     * Страница управления QR-кодами
     */
    public function index(Request $request)
    {
        $query = Product::with(['variants.qrCodes'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('article', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20)->withQueryString();

        return view('admin.qr.index', compact('products'));
    }

    /**
     * Страница генерации QR-кодов для товара
     */
    public function generatePage(Product $product)
    {
        $product->load('variants.qrCodes');
        return view('admin.qr.generate', compact('product'));
    }

    /**
     * Генерация QR-кода для товара (только артикул)
     * QR-код содержит JSON: {"article": "ART123"}
     */
    public function generate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->product_id);

            // Если указан вариант, генерируем для него
            if ($request->variant_id) {
                $variants = ProductVariant::where('id', $request->variant_id)->get();
            } else {
                // Иначе для всех вариантов товара
                $variants = ProductVariant::where('product_id', $product->id)->get();
            }

            $generated = [];

            foreach ($variants as $variant) {
                // QR-код содержит только артикул товара в JSON формате
                $qrData = ['article' => $product->article];
                $qrDataJson = json_encode($qrData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                // Проверяем, есть ли уже QR-код для этого варианта
                $existingQr = QrCode::where('variant_id', $variant->id)->first();

                if ($existingQr) {
                    // Обновляем существующий
                    $existingQr->update([
                        'qr_data' => $qrData, // кастится в array благодаря $casts
                    ]);
                    $generated[] = $existingQr;
                } else {
                    // Создаем новый
                    $qrCode = QrCode::create([
                        'variant_id' => $variant->id,
                        'qr_data' => $qrData,
                        'is_printed' => false,
                    ]);
                    $generated[] = $qrCode;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'QR-коды успешно сгенерированы',
                'qr_codes' => $generated,
                'article' => $product->article,
                'qr_data_example' => $qrDataJson, // Для отладки
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Генерация QR-кода для всех товаров
     */
    public function generateAll()
    {
        DB::beginTransaction();
        try {
            $products = Product::with('variants')->get();
            $count = 0;

            foreach ($products as $product) {
                foreach ($product->variants as $variant) {
                    $qrData = ['article' => $product->article];

                    QrCode::updateOrCreate(
                        ['variant_id' => $variant->id],
                        [
                            'qr_data' => $qrData,
                            'is_printed' => false,
                        ]
                    );
                    $count++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Сгенерировано {$count} QR-кодов",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Печать QR-кода
     */
    public function print(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
        ]);

        $variant = ProductVariant::with(['product', 'qrCodes'])->findOrFail($request->variant_id);

        $qrCode = QrCode::where('variant_id', $variant->id)->first();

        if (!$qrCode) {
            // Генерируем если нет
            $product = $variant->product;
            $qrData = ['article' => $product->article];

            $qrCode = QrCode::create([
                'variant_id' => $variant->id,
                'qr_data' => $qrData,
                'is_printed' => false,
            ]);
        }

        $qrCode->markAsPrinted(auth()->id());

        // Возвращаем данные для печати
        return response()->json([
            'success' => true,
            'qr_data' => json_encode(['article' => $variant->product->article], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'product_name' => $variant->product->name,
            'size' => $variant->size,
            'color' => $variant->color,
            'sku' => $variant->sku,
        ]);
    }

    /**
     * Страница управления QR-кодами конкретного товара
     */
    public function manageProduct(Product $product)
    {
        $product->load(['variants.qrCodes', 'variants.stock']);
        return view('admin.qr.manage-product', compact('product'));
    }

    /**
     * Удаление QR-кода
     */
    public function delete($id)
    {
        $qrCode = QrCode::findOrFail($id);
        $qrCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'QR-код удален',
        ]);
    }
}
