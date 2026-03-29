<?php

namespace Modules\Seller\Http\Controllers;

use Modules\Seller\Models\Transaction;

class ReceiptsController extends Controller
{
    public function index()
    {
        try {
            $transactions = Transaction::sales()
                ->where('user_id', auth()->id())
                ->with(['customer', 'items'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('seller::receipts.index', compact('transactions'));
        } catch (\Exception $e) {
            \Log::error('ReceiptsController error: ' . $e->getMessage());
            return redirect()->route('seller.dashboard')->with('error', 'Ошибка загрузки истории продаж: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        // Проверяем, что транзакция принадлежит текущему продавцу
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещён');
        }

        $transaction->load(['items', 'shop', 'user', 'customer', 'coupon']);
        return view('seller::receipts.show', compact('transaction'));
    }

    public function print(Transaction $transaction)
    {
        // Проверяем, что транзакция принадлежит текущему продавцу
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещён');
        }

        $transaction->load(['items', 'shop', 'user', 'customer']);
        return view('seller::receipts.print', compact('transaction'));
    }
}
