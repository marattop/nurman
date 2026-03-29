@extends('seller::layouts.seller')
@section('page-title', 'Детали чека')
@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('seller.receipts') }}" style="color: #94a3b8; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-arrow-left"></i> Назад к истории
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 24px;">
    <!-- Список товаров в чеке -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="card-header" style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between;">
            <h3 class="card-header-title">Товары в чеке #{{ $transaction->transaction_number ?? $transaction->id }}</h3>
            <button onclick="window.open('{{ route('seller.receipts.print', $transaction->id) }}', '_blank', 'width=400,height=600')" class="btn btn-primary" style="padding: 6px 12px; font-size: 13px;">
                <i class="bi bi-printer"></i> Печать чека
            </button>
        </div>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 12px 20px;">Товар</th>
                        <th style="text-align: center; padding: 12px 20px;">Цена</th>
                        <th style="text-align: center; padding: 12px 20px;">Кол-во</th>
                        <th style="text-align: right; padding: 12px 20px;">Итого</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $item)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                        <td style="padding: 12px 20px;">
                            <div style="font-weight: 500; color: #f1f5f9;">{{ $item->variant->product->name ?? 'Товар удален' }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ $item->variant->size }} / {{ $item->variant->color }}</div>
                        </td>
                        <td style="padding: 12px 20px; text-align: center; color: #94a3b8;">
                            {{ number_format($item->price, 0, '.', ' ') }} с
                        </td>
                        <td style="padding: 12px 20px; text-align: center; color: #f1f5f9; font-weight: 600;">
                            {{ $item->quantity }}
                        </td>
                        <td style="padding: 12px 20px; text-align: right; font-weight: 700; color: #10b981;">
                            {{ number_format($item->price * $item->quantity, 0, '.', ' ') }} с
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="padding: 24px; background: rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
            <div style="display: flex; gap: 40px; color: #94a3b8; font-size: 14px;">
                <span>Сумма без скидки:</span>
                <span style="color: #f1f5f9;">{{ number_format($transaction->subtotal, 0, '.', ' ') }} с</span>
            </div>
            @if($transaction->discount_amount > 0)
            <div style="display: flex; gap: 40px; color: #ef4444; font-size: 14px;">
                <span>Скидка:</span>
                <span>-{{ number_format($transaction->discount_amount, 0, '.', ' ') }} с</span>
            </div>
            @endif
            <div style="display: flex; gap: 40px; font-size: 24px; font-weight: 800; color: #10b981; margin-top: 8px;">
                <span>Итого к оплате:</span>
                <span>{{ number_format($transaction->total_amount, 0, '.', ' ') }} с</span>
            </div>
        </div>
    </div>

    <!-- Инфо о транзакции -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <div class="card">
            <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 600;">Информация</h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; margin-bottom: 4px;">Способ оплаты</label>
                    <div style="display: flex; align-items: center; gap: 8px; color: #f1f5f9;">
                        <i class="bi {{ $transaction->payment_icon }}"></i>
                        <span>{{ ucfirst($transaction->payment_method) }}</span>
                    </div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; margin-bottom: 4px;">Магазин</label>
                    <div style="color: #f1f5f9;">{{ $transaction->shop->name ?? '—' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; margin-bottom: 4px;">Клиент</label>
                    @if($transaction->customer)
                        <a href="{{ route('seller.customers.show', $transaction->customer_id) }}" style="color: #38bdf8; text-decoration: none;">
                            {{ $transaction->customer->full_name }}
                        </a>
                    @else
                        <span style="color: #94a3b8;">Гость</span>
                    @endif
                </div>
                @if($transaction->coupon)
                <div>
                    <label style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; margin-bottom: 4px;">Купон</label>
                    <span class="badge-success">{{ $transaction->coupon->code }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
