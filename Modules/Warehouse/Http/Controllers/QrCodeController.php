<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Warehouse\Models\QrCode as QrCodeModel;
use Modules\Warehouse\Models\ProductVariant;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function index(Request $request)
    {
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
