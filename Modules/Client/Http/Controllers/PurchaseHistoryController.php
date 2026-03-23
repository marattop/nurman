<?php

namespace Modules\Client\Http\Controllers;

use Modules\Client\Models\CustomerPurchaseHistory;

class PurchaseHistoryController extends Controller
{
    public function index()
    {
        $purchases = CustomerPurchaseHistory::where('customer_id', auth()->id())
            ->with('transaction')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('client::purchases.index', compact('purchases'));
    }

    public function show($transactionId)
    {
        $purchase = CustomerPurchaseHistory::where('customer_id', auth()->id())
            ->where('transaction_id', $transactionId)
            ->with('transaction.items.variant.product')
            ->firstOrFail();
        
        return view('client::purchases.show', compact('purchase'));
    }
}
