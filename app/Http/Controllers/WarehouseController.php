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
        return redirect()->route('warehouse.purchases.index')->with('success', 'Приёмка оформлена');
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
