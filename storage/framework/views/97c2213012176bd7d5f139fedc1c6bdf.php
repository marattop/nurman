<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чек №<?php echo e($transaction->transaction_number); ?></title>
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
        <p><?php echo e($transaction->shop?->name ?? 'Магазин'); ?></p>
        <p><?php echo e($transaction->shop?->address ?? ''); ?></p>
        <p><?php echo e($transaction->shop?->phone ?? ''); ?></p>
    </div>
    
    <div class="transaction-info">
        <div>
            <span>Чек №:</span>
            <span><?php echo e($transaction->transaction_number); ?></span>
        </div>
        <div>
            <span>Дата:</span>
            <span><?php echo e($transaction->created_at->format('d.m.Y H:i')); ?></span>
        </div>
        <div>
            <span>Кассир:</span>
            <span><?php echo e($transaction->user?->full_name ?? 'Продавец'); ?></span>
        </div>
        <?php if($transaction->customer): ?>
        <div>
            <span>Клиент:</span>
            <span><?php echo e($transaction->customer?->full_name ?? ''); ?></span>
        </div>
        <div>
            <span>Телефон:</span>
            <span><?php echo e($transaction->customer?->phone ?? ''); ?></span>
        </div>
        <?php endif; ?>
        <?php if($transaction->coupon): ?>
        <div>
            <span>Купон:</span>
            <span><?php echo e($transaction->coupon?->code); ?></span>
        </div>
        <div>
            <span>Тип скидки:</span>
            <span>
                <?php if($transaction->coupon->discount_type === 'percent'): ?>
                    <?php echo e($transaction->coupon->discount_value); ?>%
                <?php elseif($transaction->coupon->discount_type === 'wholesale'): ?>
                    Оптовая цена
                <?php endif; ?>
            </span>
        </div>
        <?php elseif($transaction->customer && ($transaction->customer->customerProfile->loyalty_discount_percent ?? 0) > 0): ?>
        <div>
            <span>Скидка лояльности:</span>
            <span><?php echo e($transaction->customer->customerProfile->loyalty_discount_percent); ?>%</span>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="items">
        <?php $__currentLoopData = $transaction->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="item">
            <div class="item-name"><?php echo e($item->variant?->product?->name ?? 'Товар'); ?></div>
            <div class="item-details">
                <span><?php echo e($item->quantity); ?> шт x <?php echo e(number_format($item->price, 0, ',', ' ')); ?> с</span>
                <span><?php echo e(number_format($item->subtotal, 0, ',', ' ')); ?> с</span>
            </div>
            <?php if($item->variant?->size || $item->variant?->color): ?>
            <div class="item-details" style="color: #666;">
                <span>Размер: <?php echo e($item->variant?->size ?? '-'); ?></span>
                <span>Цвет: <?php echo e($item->variant?->color ?? '-'); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <div class="totals">
        <div>
            <span>Подытог:</span>
            <span><?php echo e(number_format($transaction->total_amount + $transaction->discount_amount, 0, ',', ' ')); ?> с</span>
        </div>
        <?php if($transaction->discount_amount > 0): ?>
        <div style="color: var(--accent-primary);">
            <span>Скидка:</span>
            <span>-<?php echo e(number_format($transaction->discount_amount, 0, ',', ' ')); ?> с</span>
        </div>
        <?php endif; ?>
        <div class="final">
            <span>ИТОГО:</span>
            <span><?php echo e(number_format($transaction->total_amount, 0, ',', ' ')); ?> с</span>
        </div>
        <div style="margin-top: 5px;">
            <span>Оплачено:</span>
            <span><?php echo e(number_format($transaction->meta['amount_paid'] ?? 0, 0, ',', ' ')); ?> с</span>
        </div>
        <?php if(($transaction->meta['change'] ?? 0) > 0): ?>
        <div style="color: var(--accent-primary);">
            <span>Сдача:</span>
            <span><?php echo e(number_format($transaction->meta['change'] ?? 0, 0, ',', ' ')); ?> с</span>
        </div>
        <?php endif; ?>
        <div style="margin-top: 5px;">
            <span>Способ оплаты:</span>
            <span>
                <?php if($transaction->payment_method === 'cash'): ?>
                    Наличные
                <?php elseif($transaction->payment_method === 'card'): ?>
                    Карта
                <?php elseif($transaction->payment_method === 'transfer'): ?>
                    Перевод
                <?php endif; ?>
            </span>
        </div>
    </div>
    
    <?php if($transaction->comment): ?>
    <div style="margin: 10px 0; padding: 5px; background: #f5f5f5;">
        <strong>Комментарий:</strong><br>
        <?php echo e($transaction->comment); ?>

    </div>
    <?php endif; ?>

    <div class="barcode">
        <?php echo e(str_replace('-', '', $transaction->transaction_number)); ?>

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
<?php /**PATH C:\laragon\www\nurman\resources\views/seller/pos/receipt.blade.php ENDPATH**/ ?>