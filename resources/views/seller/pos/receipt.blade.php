<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чек №{{ $transaction->transaction_number }}</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-family: 'Courier New', monospace;
                font-size: 12px;
                width: 58mm;
            }
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 10px;
            max-width: 58mm;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .transaction-info {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
            margin: 10px 0;
        }
        
        .transaction-info div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .items {
            margin: 10px 0;
        }
        
        .item {
            margin: 8px 0;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .totals {
            border-top: 1px dashed #000;
            margin: 10px 0;
            padding-top: 10px;
        }
        
        .totals div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .totals .final {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
        }
        
        .barcode {
            text-align: center;
            margin: 10px 0;
            font-family: 'Libre Barcode 39 Text', cursive;
            font-size: 24px;
        }
        
        .customer-info {
            margin: 10px 0;
            padding: 5px;
            background: #f5f5f5;
        }
        
        .print-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: var(--accent-primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        
        .print-btn:hover {
            background: var(--accent-primary-dark);
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        Печать чека
    </button>
    
    <div class="header">
        <h2>NURMAN</h2>
        <p>Торговая сеть</p>
        <p>{{ $transaction->shop?->name ?? 'Магазин' }}</p>
        <p>{{ $transaction->shop?->address ?? '' }}</p>
        <p>{{ $transaction->shop?->phone ?? '' }}</p>
    </div>
    
    <div class="transaction-info">
        <div>
            <span>Чек №:</span>
            <span>{{ $transaction->transaction_number }}</span>
        </div>
        <div>
            <span>Дата:</span>
            <span>{{ $transaction->created_at->format('d.m.Y H:i') }}</span>
        </div>
        <div>
            <span>Кассир:</span>
            <span>{{ $transaction->user?->full_name ?? 'Продавец' }}</span>
        </div>
        @if($transaction->customer)
        <div>
            <span>Клиент:</span>
            <span>{{ $transaction->customer?->full_name ?? '' }}</span>
        </div>
        <div>
            <span>Телефон:</span>
            <span>{{ $transaction->customer?->phone ?? '' }}</span>
        </div>
        @endif
        @if($transaction->coupon)
        <div>
            <span>Купон:</span>
            <span>{{ $transaction->coupon?->code }}</span>
        </div>
        <div>
            <span>Тип скидки:</span>
            <span>
                @if($transaction->coupon->discount_type === 'percent')
                    {{ $transaction->coupon->discount_value }}%
                @elseif($transaction->coupon->discount_type === 'wholesale')
                    Оптовая цена
                @endif
            </span>
        </div>
        @elseif($transaction->customer && ($transaction->customer->customerProfile->loyalty_discount_percent ?? 0) > 0)
        <div>
            <span>Скидка лояльности:</span>
            <span>{{ $transaction->customer->customerProfile->loyalty_discount_percent }}%</span>
        </div>
        @endif
    </div>
    
    <div class="items">
        @foreach($transaction->items as $item)
        <div class="item">
            <div class="item-name">{{ $item->variant?->product?->name ?? 'Товар' }}</div>
            <div class="item-details">
                <span>{{ $item->quantity }} шт x {{ number_format($item->price, 0, ',', ' ') }} с</span>
                <span>{{ number_format($item->subtotal, 0, ',', ' ') }} с</span>
            </div>
            @if($item->variant?->size || $item->variant?->color)
            <div class="item-details" style="color: #666;">
                <span>Размер: {{ $item->variant?->size ?? '-' }}</span>
                <span>Цвет: {{ $item->variant?->color ?? '-' }}</span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    
    <div class="totals">
        <div>
            <span>Подытог:</span>
            <span>{{ number_format($transaction->total_amount + $transaction->discount_amount, 0, ',', ' ') }} с</span>
        </div>
        @if($transaction->discount_amount > 0)
        <div style="color: var(--accent-primary);">
            <span>Скидка:</span>
            <span>-{{ number_format($transaction->discount_amount, 0, ',', ' ') }} с</span>
        </div>
        @endif
        <div class="final">
            <span>ИТОГО:</span>
            <span>{{ number_format($transaction->total_amount, 0, ',', ' ') }} с</span>
        </div>
        <div style="margin-top: 5px;">
            <span>Оплачено:</span>
            <span>{{ number_format($transaction->meta['amount_paid'] ?? 0, 0, ',', ' ') }} с</span>
        </div>
        @if(($transaction->meta['change'] ?? 0) > 0)
        <div style="color: var(--accent-primary);">
            <span>Сдача:</span>
            <span>{{ number_format($transaction->meta['change'] ?? 0, 0, ',', ' ') }} с</span>
        </div>
        @endif
        <div style="margin-top: 5px;">
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
    <div style="margin: 10px 0; padding: 5px; background: #f5f5f5;">
        <strong>Комментарий:</strong><br>
        {{ $transaction->comment }}
    </div>
    @endif

    <div class="barcode">
        {{ str_replace('-', '', $transaction->transaction_number) }}
    </div>

    <div class="footer">
        <p>Спасибо за покупку!</p>
        <p>Возврат товара в течение 14 дней</p>
        <p>www.nurman.kg</p>
    </div>
    
    <script>
        // Автоматическая печать при загрузке
        window.addEventListener('load', function() {
            if (window.location.search.includes('print=1')) {
                window.print();
            }
        });
    </script>
</body>
</html>
