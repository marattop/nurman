<?php

namespace Modules\Seller\Http\Controllers;

use Modules\Seller\Models\Transaction;

class ReceiptsController extends Controller
{
    public function index()
    {
        $transactions = Transaction::sales()
            ->where('user_id', auth()->id())
            ->with(['items.variant.product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('seller::receipts.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['items.variant.product', 'shop', 'user', 'customer', 'coupon']);
        return view('seller::receipts.show', compact('transaction'));
    }

    public function print(Transaction $transaction)
    {
        $transaction->load(['items.variant.product', 'shop', 'user', 'customer']);
        return view('seller::receipts.print', compact('transaction'));
    }
}
