<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Купон {{ $coupon->code }} - NURMAN</title>
    <style>
        @page { size: A6; margin: 10mm; }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-primary-dark));
            color: white;
            min-height: 100vh;
        }
        .coupon {
            max-width: 400px;
            margin: 0 auto;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-primary-dark));
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .coupon-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }
        .coupon-logo {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .coupon-chip {
            width: 50px;
            height: 35px;
            background: linear-gradient(135deg, #fbbf24, #d97706);
            border-radius: 6px;
        }
        .coupon-code {
            font-family: monospace;
            font-size: 1.2rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .coupon-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .coupon-qr {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            display: inline-block;
            margin: 1rem 0;
        }
        .coupon-qr img {
            width: 150px;
            height: 150px;
        }
        .coupon-info {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.3);
            font-size: 0.9rem;
        }
        .coupon-customer {
            margin-bottom: 1rem;
        }
        .coupon-customer-name {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .coupon-customer-phone {
            opacity: 0.9;
        }
        @media print {
            body { background: white; color: black; }
            .coupon { background: linear-gradient(135deg, var(--accent-primary), var(--accent-primary-dark)); color: white; }
        }
    </style>
</head>
<body>
    <div class="coupon">
        <div class="coupon-header">
            <div class="coupon-logo">NURMAN</div>
            <div class="coupon-chip"></div>
        </div>

        <div class="coupon-code">{{ $coupon->code }}</div>
        
        <div class="coupon-value">
            @if($coupon->discount_type === 'percent')
                Скидка {{ $coupon->discount_value }}%
            @elseif($coupon->discount_type === 'wholesale')
                Оптовая цена
            @endif
        </div>

        @php
            $qrData = json_encode(['coupon_id' => $coupon->id, 'code' => $coupon->code]);
            $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrData);
        @endphp

        <div class="coupon-qr">
            <img src="{{ $qrCodeUrl }}" alt="QR код">
        </div>

        <div class="coupon-info">
            @if($coupon->customer)
            <div class="coupon-customer">
                <div class="coupon-customer-name">{{ $coupon->customer->full_name }}</div>
                <div class="coupon-customer-phone">{{ $coupon->customer->phone }}</div>
            </div>
            @endif

            @if($coupon->valid_until)
            <div>
                <strong>Действует до:</strong> {{ $coupon->valid_until->format('d.m.Y') }}
            </div>
            @else
            <div>
                <strong>Бессрочный купон</strong>
            </div>
            @endif

            <div style="margin-top: 1rem; font-size: 0.8rem; opacity: 0.8;">
                Покажите этот купон продавцу перед оплатой
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
