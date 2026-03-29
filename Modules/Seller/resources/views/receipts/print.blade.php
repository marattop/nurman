<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Чек #{{ $transaction->transaction_number ?? $transaction->id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 14px; line-height: 1.2; margin: 0; padding: 10px; width: 80mm; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        .right { text-align: right; }
        @media print {
            body { width: 80mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <div class="bold" style="font-size: 18px;">NURMAN</div>
        <div>{{ $transaction->shop->name ?? 'Магазин одежды' }}</div>
        <div>{{ $transaction->shop->address ?? '' }}</div>
        <div>{{ $transaction->shop->phone ?? '' }}</div>
    </div>

    <div class="divider"></div>

    <div>Чек: #{{ $transaction->transaction_number ?? $transaction->id }}</div>
    <div>Кассир: {{ $transaction->user->full_name ?? '—' }}</div>
    <div>Дата: {{ $transaction->created_at->format('d.m.Y H:i') }}</div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Товар</th>
                <th class="right">Кол</th>
                <th class="right">Сумма</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $item)
            <tr>
                <td>{{ $item->variant->product->name ?? 'Товар' }} ({{ $item->variant->size }}/{{ $item->variant->color }})</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->price * $item->quantity, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div style="display: flex; justify-content: space-between;">
        <span>Сумма:</span>
        <span>{{ number_format($transaction->subtotal, 0) }} с</span>
    </div>
    @if($transaction->discount_amount > 0)
    <div style="display: flex; justify-content: space-between;">
        <span>Скидка:</span>
        <span>-{{ number_format($transaction->discount_amount, 0) }} с</span>
    </div>
    @endif
    <div class="bold" style="display: flex; justify-content: space-between; font-size: 16px; margin-top: 5px;">
        <span>ИТОГО:</span>
        <span>{{ number_format($transaction->total_amount, 0) }} с</span>
    </div>

    <div class="divider"></div>

    <div class="center" style="font-size: 12px;">
        <div>Способ оплаты: {{ $transaction->payment_method }}</div>
        <div style="margin-top: 10px;">Спасибо за покупку!</div>
        <div>Ждем вас снова!</div>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.close()" style="padding: 10px 20px;">Закрыть</button>
    </div>
</body>
</html>
