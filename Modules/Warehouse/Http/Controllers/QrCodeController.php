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

        // Поиск по QR-коду (формат id:code)
        if (str_contains($qrData, ':')) {
            $parts = explode(':', $qrData);
            $qr = QrCodeModel::where('id', $parts[0])->where('qr_data', $parts[1])->first();
            if ($qr) {
                $variant = $qr->variant;
                $product = $variant->product;
                return $this->formatScanResponse($product, $variant, true);
            }
        }

        // Поиск по артикулу товара
        $product = Product::where('article', $qrData)->first();
        if ($product) {
            $variant = $product->variants->first();
            return $this->formatScanResponse($product, $variant);
        }

        // Поиск по штрих-коду варианта
        $variant = ProductVariant::where('barcode', $qrData)->first();
        if ($variant) {
            $product = $variant->product;
            return $this->formatScanResponse($product, $variant);
        }

        return response()->json(['success' => false, 'message' => 'Товар не найден']);
    }

    private function formatScanResponse($product, $variant, $isKnownQr = false)
    {
        $stock = Stock::where('variant_id', $variant->id)->get();
        $shopStock = $stock->whereNotNull('shop_id')->sum('quantity');
        $warehouseStock = $stock->whereNotNull('warehouse_id')->sum('quantity');

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

        // Сгенерировать новый QR-код
        $qrCode = QrCodeModel::generateForVariant($variant);

        return back()->with('success', 'QR-код сгенерирован')->with('qr_code', $qrCode);
    }

    public function print(QrCodeModel $qrCode)
    {
        $qrCode->markAsPrinted(auth()->id());
        
        return view('warehouse::qrcodes.print', compact('qrCode'));
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
