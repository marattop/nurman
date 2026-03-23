@extends('layouts.app')

@section('title', 'Чек №' . $transaction->transaction_number)

@section('content')
<style>
    .receipt-container {
        max-width: 600px;
        margin: 2rem auto;
        background: var(--bg-card);
        border-radius: 12px;
        padding: 2rem;
        border: 1px solid var(--border-color);
    }

    .receipt-header {
        text-align: center;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .receipt-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .receipt-number {
        color: var(--primary);
        font-weight: 600;
    }

    .receipt-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        padding: 0.75rem;
        background: var(--bg-secondary);
        border-radius: 8px;
    }

    .info-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
    }

    .items-table th {
        text-align: left;
        padding: 0.75rem;
        background: var(--bg-secondary);
        color: var(--text-primary);
        font-weight: 600;
        border-bottom: 2px solid var(--border-color);
    }

    .items-table td {
        padding: 0.75rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .totals-section {
        background: var(--bg-secondary);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }

    .total-row.final {
        border-top: 2px solid var(--border-color);
        margin-top: 0.5rem;
        padding-top: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }

    .btn-print,
    .btn-back {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        margin-right: 0.5rem;
    }

    .btn-print {
        background: var(--gradient);
        color: white;
    }

    .btn-back {
        background: var(--bg-card);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    @media print {
        .no-print {
            display: none;
        }
        .receipt-container {
            border: none;
            box-shadow: none;
        }
    }
</style>

<div class="receipt-container">
    <div class="receipt-header">
        <div class="receipt-title">NURMAN</div>
        <div class="receipt-number">Чек № {{ $transaction->transaction_number }}</div>
        <div style="color: var(--text-secondary); margin-top: 0.5rem;">
            {{ $transaction->created_at->format('d.m.Y H:i') }}
        </div>
    </div>

    <div class="receipt-info">
        <div class="info-item">
            <div class="info-label">Магазин</div>
            <div class="info-value">{{ $transaction->shop?->name ?? '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Кассир</div>
            <div class="info-value">{{ $transaction->user?->full_name ?? '-' }}</div>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Товар</th>
                <th>Кол-во</th>
                <th>Цена</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $item)
                <tr>
                    <td>
                        {{ $item->variant?->product?->name ?? 'Товар' }}
                        @if($item->variant?->size || $item->variant?->color)
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                @if($item->variant?->size)Размер: {{ $item->variant->size }}@endif
                                @if($item->variant?->color)Цвет: {{ $item->variant->color }}@endif
                            </div>
                        @endif
                    </td>
                    <td>{{ $item->quantity }} шт.</td>
                    <td>{{ number_format($item->price, 0, ',', ' ') }} с</td>
                    <td>{{ number_format($item->subtotal, 0, ',', ' ') }} с</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row">
            <span>Подытог:</span>
            <span>{{ number_format($transaction->total_amount + $transaction->discount_amount, 0, ',', ' ') }} с</span>
        </div>
        @if($transaction->discount_amount > 0)
            <div class="total-row" style="color: var(--success);">
                <span>Скидка:</span>
                <span>-{{ number_format($transaction->discount_amount, 0, ',', ' ') }} с</span>
            </div>
        @endif
        <div class="total-row final">
            <span>ИТОГО:</span>
            <span>{{ number_format($transaction->total_amount, 0, ',', ' ') }} с</span>
        </div>
        <div class="total-row">
            <span>Оплачено:</span>
            <span>{{ number_format($transaction->meta['amount_paid'] ?? 0, 0, ',', ' ') }} с</span>
        </div>
        @if(($transaction->meta['change'] ?? 0) > 0)
            <div class="total-row" style="color: var(--success);">
                <span>Сдача:</span>
                <span>{{ number_format($transaction->meta['change'], 0, ',', ' ') }} с</span>
            </div>
        @endif
        <div class="total-row">
            <span>Способ оплаты:</span>
            <span>
                @if($transaction->payment_method === 'cash')
                    Наличные
                @elseif($transaction->payment_method === 'card')
                    Карта
                @elseif($transaction->payment_method === 'transfer')
                    Перевод
                @endif
            </span>
        </div>
    </div>

    @if($transaction->comment)
        <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px; margin-bottom: 1.5rem;">
            <strong>Комментарий:</strong><br>
            {{ $transaction->comment }}
        </div>
    @endif

    <div class="no-print" style="text-align: center;">
        <button class="btn-print" onclick="window.print()">
            Печать
        </button>
        <button class="btn-back" onclick="window.history.back()">
            Назад
        </button>
    </div>
</div>
@endsection
