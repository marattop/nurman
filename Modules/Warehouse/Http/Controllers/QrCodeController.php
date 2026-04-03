<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Warehouse\Models\QrCode as QrCodeModel;
use Modules\Warehouse\Models\ProductVariant;
use Modules\Warehouse\Models\Product;
use Modules\Warehouse\Models\Stock;

class QrCodeController extends Controller
{
    public function __construct()
    {
        // Проверка разрешения для всех методов кроме index
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->can('warehouse.qr')) {
                abort(403, 'Доступ ограничен. Обратитесь к администратору для получения разрешения на печать QR-кодов.');
            }
            return $next($request);
        })->except(['scan', 'processScan']);
    }

    public function scan()
    {
        return view('warehouse::scan-qr');
    }

    public function processScan(Request $request)
    {
        $qrData = $request->get('qr_data');

        if (!$qrData) {
            return response()->json(['success' => false, 'message' => 'Код не передан']);
        }

        // Логирование для отладки
        \Log::info('Warehouse QR scan - raw:', ['qr_data' => $qrData]);

        // Парсим JSON если это JSON
        $decoded = json_decode($qrData, true);
        if ($decoded && isset($decoded['article'])) {
            $qrData = $decoded['article'];
            \Log::info('Warehouse QR scan - извлечён article:', ['article' => $qrData]);
        }

        // Поиск по SKU варианта (QR-код содержит SKU)
        \Log::info('Warehouse - Поиск по SKU:', ['sku' => $qrData]);
        $variant = ProductVariant::where('sku', $qrData)->first();
        if ($variant) {
            $product = $variant->product;
            \Log::info('Warehouse - Найдено по SKU:', ['product' => $product->name]);
            return $this->formatScanResponse($product, $variant, true);
        }

        // Поиск по артикулу товара
        \Log::info('Warehouse - Поиск по article:', ['article' => $qrData]);
        $product = Product::where('article', $qrData)->first();
        if ($product) {
            $variant = $product->variants->first();
            if (!$variant) {
                \Log::warning('Warehouse - Нет вариантов:', ['product_id' => $product->id]);
                return response()->json(['success' => false, 'message' => 'У товара нет вариантов']);
            }
            \Log::info('Warehouse - Найдено по article:', ['product' => $product->name, 'variant_id' => $variant->id]);
            return $this->formatScanResponse($product, $variant);
        }

        // Поиск по штрих-коду варианта
        \Log::info('Warehouse - Поиск по barcode:', ['barcode' => $qrData]);
        $variant = ProductVariant::where('barcode', $qrData)->first();
        if ($variant) {
            $product = $variant->product;
            \Log::info('Warehouse - Найдено по barcode:', ['product' => $product->name]);
            return $this->formatScanResponse($product, $variant);
        }

        \Log::warning('Warehouse - Товар не найден:', ['qr_data' => $qrData]);
        return response()->json(['success' => false, 'message' => 'Товар не найден. QR: ' . $qrData]);
    }

    private function formatScanResponse($product, $variant, $isKnownQr = false)
    {
        $stock = Stock::where('variant_id', $variant->id)->get();
        $shopStock = $stock->whereNotNull('shop_id')->sum('quantity');
        $warehouseStock = $stock->whereNotNull('warehouse_id')->sum('quantity');

        \Log::info('Форматирование ответа: ' . json_encode([
            'product_name' => $product->name,
            'variant_id' => $variant->id,
            'total_stock' => $shopStock + $warehouseStock
        ]));

        return response()->json([
            'success' => true,
            'is_known_qr' => $isKnownQr,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'article' => $product->article,
                'photo' => $product->photo ? asset('storage/' . $product->photo) : null,
                'retail_price' => $product->retail_price,
                'supplier_price' => $variant->supplier_price,
                'variants' => $product->variants->map(function($v) {
                    $vStock = Stock::where('variant_id', $v->id)->get();
                    return [
                        'id' => $v->id,
                        'size' => $v->size,
                        'color' => $v->color,
                        'barcode' => $v->barcode,
                        'total_quantity' => $vStock->sum('quantity'),
                        'showcase_quantity' => $vStock->whereNotNull('shop_id')->sum('quantity'),
                        'warehouse_quantity' => $vStock->whereNotNull('warehouse_id')->sum('quantity'),
                    ];
                })
            ],
            'summary' => [
                'total_stock' => $shopStock + $warehouseStock,
                'shop_stock' => $shopStock,
                'warehouse_stock' => $warehouseStock,
            ]
        ]);
    }

    public function index(Request $request)
    {
        // Проверка разрешения для index
        if (!auth()->user()->can('warehouse.qr')) {
            abort(403, 'Доступ ограничен. Обратитесь к администратору для получения разрешения на печать QR-кодов.');
        }
        
        $query = QrCodeModel::with(['variant.product', 'printedBy']);

        if ($request->get('not_printed')) {
            $query->where('is_printed', false);
        }

        $qrCodes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('warehouse::qrcodes.index', compact('qrCodes'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);

        // Проверить, есть ли уже QR-код
        $existingQr = QrCodeModel::where('variant_id', $variant->id)->first();

        if ($existingQr) {
            return back()->with('info', 'QR-код уже существует')->with('qr_code', $existingQr);
        }

        // Удалить все старые QR-коды для этого варианта (на случай дублей)
        QrCodeModel::where('variant_id', $variant->id)->delete();

        // Сгенерировать новый QR-код
        $qrCode = QrCodeModel::generateForVariant($variant);

        return back()->with('success', 'QR-код сгенерирован')->with('qr_code', $qrCode);
    }

    public function print(QrCodeModel $qrCode)
    {
        // Проверить, существует ли файл QR-кода
        if (!$qrCode->qr_code_path || !file_exists(public_path($qrCode->qr_code_path))) {
            // Файл не найден - перегенерировать QR-код
            $variant = $qrCode->variant;
            if ($variant) {
                // Удалить старый файл если существует
                if ($qrCode->qr_code_path && file_exists(public_path($qrCode->qr_code_path))) {
                    unlink(public_path($qrCode->qr_code_path));
                }
                
                // Удалить все QR-коды для этого варианта (чтобы не было дублей)
                QrCodeModel::where('variant_id', $variant->id)->delete();
                
                // Сгенерировать новый QR-код
                $qrCode = QrCodeModel::generateForVariant($variant);
                $qrCode->markAsPrinted(auth()->id());
                
                return view('warehouse::qrcodes.print', compact('qrCode'));
            }
        }
        
        $qrCode->markAsPrinted(auth()->id());

        return view('warehouse::qrcodes.print', compact('qrCode'));
    }

    /**
     * Печать только QR-кода (без дизайна)
     */
    public function printLabel(QrCodeModel $qrCode)
    {
        $qrCode->markAsPrinted(auth()->id());

        return view('warehouse::qrcodes.print-label', compact('qrCode'));
    }

    public function batchPrint(Request $request)
    {
        $validated = $request->validate([
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'exists:product_variants,id',
        ]);

        $qrCodes = [];
        
        foreach ($validated['variant_ids'] as $variantId) {
            $variant = ProductVariant::find($variantId);
            
            $qrCode = QrCodeModel::where('variant_id', $variantId)->first();
            
            if (!$qrCode) {
                $qrCode = QrCodeModel::generateForVariant($variant);
            }
            
            $qrCodes[] = $qrCode;
        }

        return view('warehouse::qrcodes.batch-print', compact('qrCodes'));
    }
}
