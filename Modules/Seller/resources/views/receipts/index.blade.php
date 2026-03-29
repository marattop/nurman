@extends('seller::layouts.seller')
@section('page-title', 'История продаж')
@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div class="page-icon"><i class="bi bi-receipt"></i></div>
        <div>
            <h1 class="page-title">История продаж</h1>
            <p class="page-subtitle">Список всех оформленных чеков</p>
        </div>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 20px;">Дата</th>
                    <th style="text-align: left; padding: 12px 20px;">ID / Номер</th>
                    <th style="text-align: left; padding: 12px 20px;">Клиент</th>
                    <th style="text-align: center; padding: 12px 20px;">Оплата</th>
                    <th style="text-align: right; padding: 12px 20px;">Сумма</th>
                    <th style="text-align: right; padding: 12px 20px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                    <td style="padding: 12px 20px; color: #94a3b8;">{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                    <td style="padding: 12px 20px; font-weight: 500;">#{{ $transaction->transaction_number ?? $transaction->id }}</td>
                    <td style="padding: 12px 20px; color: #f1f5f9;">{{ $transaction->customer->full_name ?? 'Гость' }}</td>
                    <td style="padding: 12px 20px; text-align: center;">
                        <i class="bi {{ $transaction->payment_icon }}" title="{{ $transaction->payment_method }}"></i>
                    </td>
                    <td style="padding: 12px 20px; text-align: right; font-weight: 700; color: #10b981;">
                        {{ number_format($transaction->total_amount, 0, '.', ' ') }} с
                    </td>
                    <td style="padding: 12px 20px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="{{ route('seller.receipts.show', $transaction->id) }}" class="btn btn-outline" style="padding: 6px 10px;" title="Детали">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button onclick="window.open('{{ route('seller.receipts.print', $transaction->id) }}', '_blank', 'width=400,height=600')" class="btn btn-outline" style="padding: 6px 10px;" title="Печать">
                                <i class="bi bi-printer"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 48px; color: #64748b;">
                        <i class="bi bi-receipt" style="font-size: 48px; opacity: 0.1; display: block; margin-bottom: 16px;"></i>
                        История пуста
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($transactions->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05);">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
