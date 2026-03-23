@extends('layouts.app')

@section('title', 'Личный кабинет - NURMAN')

@section('content')
<style>
    :root {
        --card-gradient: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #CD853F 100%);
        --card-gold: #DAA520;
        --card-gold-light: #FFD700;
        --card-brown: #5D4037;
        --card-shadow: 0 10px 40px rgba(139, 69, 19, 0.4);
    }

    .client-dashboard {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #8B4513, #A0522D);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow);
    }

    .welcome-banner h1 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--card-gold);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    /* Карта клиента - КРАСИВЫЙ ДИЗАЙН */
    .client-card-section {
        margin-bottom: 2rem;
    }

    .client-card {
        background: var(--card-gradient);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        box-shadow: var(--card-shadow);
        position: relative;
        overflow: hidden;
        min-height: 280px;
        border: 2px solid var(--card-gold);
    }

    .client-card::before {
        content: 'NURMAN';
        position: absolute;
        top: -20px;
        right: -40px;
        font-size: 8rem;
        font-weight: 900;
        color: rgba(255,255,255,0.05);
        letter-spacing: 10px;
    }

    .client-card::after {
        content: '';
        position: absolute;
        bottom: -100px;
        left: -100px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(218,165,32,0.2) 0%, transparent 70%);
        border-radius: 50%;
    }

    .client-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .client-card-logo {
        font-size: 1.8rem;
        font-weight: 900;
        letter-spacing: 3px;
        color: var(--card-gold-light);
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .client-card-chip {
        width: 60px;
        height: 45px;
        background: linear-gradient(135deg, #FFD700, #B8860B);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        position: relative;
    }

    .client-card-chip::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: rgba(0,0,0,0.3);
        transform: translateY(-50%);
    }

    .client-card-chip::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 1px;
        background: rgba(0,0,0,0.3);
        transform: translateX(-50%);
    }

    .client-card-body {
        position: relative;
        z-index: 1;
        margin-bottom: 2rem;
    }

    .client-card-name {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .client-card-phone {
        font-size: 1.2rem;
        color: var(--card-gold-light);
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .client-card-qr-wrapper {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        display: inline-block;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .client-card-qr {
        width: 140px;
        height: 140px;
    }

    .client-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .client-card-bonus {
        background: rgba(0,0,0,0.3);
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        backdrop-filter: blur(10px);
        border: 1px solid var(--card-gold);
    }

    .client-card-bonus-label {
        font-size: 0.75rem;
        color: var(--card-gold-light);
        display: block;
        margin-bottom: 0.25rem;
    }

    .client-card-bonus-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: white;
    }

    .card-actions {
        display: flex;
        gap: 1rem;
    }

    .btn-print-card {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
        font-weight: 600;
    }

    .btn-print-card:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    /* Сообщение если карта не создана */
    .card-not-created {
        background: var(--bg-card);
        border: 2px dashed var(--card-gold);
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: var(--card-shadow);
    }

    .card-not-created-icon {
        font-size: 5rem;
        color: var(--card-gold);
        margin-bottom: 1.5rem;
    }

    .card-not-created h3 {
        color: var(--text-primary);
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .card-not-created p {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    .btn-contact {
        background: var(--card-gradient);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-contact:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 69, 19, 0.4);
    }

    /* Секции */
    .dashboard-section {
        background: var(--bg-card);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .section-link {
        color: var(--card-gold);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .section-link:hover {
        color: var(--card-gold-light);
        text-decoration: underline;
    }

    /* Таблица покупок */
    .purchases-table {
        width: 100%;
        border-collapse: collapse;
    }

    .purchases-table th,
    .purchases-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .purchases-table th {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .purchases-table tr:hover {
        background: var(--bg-secondary);
    }

    .purchases-table a {
        color: var(--card-gold);
        text-decoration: none;
        font-weight: 600;
    }

    .purchases-table a:hover {
        text-decoration: underline;
    }

    /* Купоны */
    .coupons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }

    .coupon-item {
        background: var(--bg-secondary);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid var(--border-color);
        transition: 0.2s;
    }

    .coupon-item:hover {
        border-color: var(--card-gold);
        transform: translateY(-2px);
    }

    .coupon-code {
        font-family: monospace;
        font-size: 1.1rem;
        color: var(--card-gold);
        margin-bottom: 0.5rem;
    }

    .coupon-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .coupon-meta {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .client-card, .client-card * {
            visibility: visible;
        }
        .client-card {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
        }
    }

    @media (max-width: 768px) {
        .client-card-container {
            grid-template-columns: 1fr;
        }
        .client-card-footer {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

<div class="client-dashboard">
    <!-- Приветствие -->
    <div class="welcome-banner">
        <h1><i class="bi bi-hand-thumbs-up me-2"></i>Здравствуйте, {{ auth()->user()->full_name }}!</h1>
        <p>Добро пожаловать в личный кабинет покупателя NURMAN</p>
    </div>

    <!-- Статистика -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><i class="bi bi-gift"></i> {{ $profile?->bonus_points ?? 0 }}</div>
            <div class="stat-label">Бонусных баллов</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><i class="bi bi-bag"></i> {{ $stats['total_purchases'] }}</div>
            <div class="stat-label">Покупок</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><i class="bi bi-wallet2"></i> {{ number_format($stats['total_spent'], 0, ',', ' ') }}</div>
            <div class="stat-label">Потрачено сом</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><i class="bi bi-ticket"></i> {{ $stats['active_coupons'] }}</div>
            <div class="stat-label">Активных купонов</div>
        </div>
    </div>

    <!-- Карта клиента -->
    <div class="client-card-section">
        @if(auth()->user()->myCoupons && auth()->user()->myCoupons->count() > 0)
            <!-- Карта отображается ТОЛЬКО если есть активный купон -->
            <div class="client-card" id="clientCard">
                <div class="client-card-header">
                    <div class="client-card-logo">NURMAN</div>
                    <div class="client-card-chip"></div>
                </div>
                
                <div class="client-card-body">
                    <div class="client-card-name">{{ auth()->user()->full_name }}</div>
                    <div class="client-card-phone">{{ auth()->user()->phone }}</div>
                    
                    @php
                        $qrData = json_encode([
                            'type' => 'customer',
                            'customer_id' => auth()->user()->id,
                            'phone' => auth()->user()->phone
                        ]);
                        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrData);
                    @endphp
                    
                    <div class="client-card-qr-wrapper">
                        <img src="{{ $qrCodeUrl }}" alt="QR код" class="client-card-qr">
                    </div>
                </div>
                
                <div class="client-card-footer">
                    <div class="client-card-bonus">
                        <span class="client-card-bonus-label">Бонусы</span>
                        <span class="client-card-bonus-value">{{ $profile?->bonus_points ?? 0 }} <i class="bi bi-coin"></i></span>
                    </div>
                    <div class="card-actions">
                        <button class="btn-print-card" onclick="printClientCard()">
                            <i class="bi bi-printer"></i> Печать
                        </button>
                    </div>
                </div>
            </div>
        @else
            <!-- Красивое сообщение если карта не создана -->
            <div class="card-not-created">
                <div class="card-not-created-icon">
                    <i class="bi bi-card-heading"></i>
                </div>
                <h3><i class="bi bi-ticket me-2"></i>Ваша скидочная карта ещё не оформлена</h3>
                <p>
                    Для получения персональной скидочной карты NURMAN обратитесь к администратору или продавцу.<br>
                    После регистрации вам будет выдана карта с индивидуальным QR-кодом.
                </p>
                <button class="btn-contact" onclick="alert('Обратитесь к администратору для оформления карты')">
                    <i class="bi bi-person-badge me-2"></i>Связаться с администратором
                </button>
            </div>
        @endif
    </div>

    <!-- Быстрые действия -->
    <div class="dashboard-section">
        <h3 class="section-title" style="margin-bottom: 1.5rem;"><i class="bi bi-phone me-2"></i>Быстрые действия</h3>
        
        <div style="display: grid; gap: 0.75rem;">
            <a href="{{ route('client.profile.index') }}" class="btn btn-outline-primary w-100 text-start">
                <i class="bi bi-person me-2"></i>Редактировать профиль
            </a>
            <a href="{{ route('client.purchases.index') }}" class="btn btn-outline-primary w-100 text-start">
                <i class="bi bi-bag me-2"></i>История покупок
            </a>
            <a href="{{ route('client.coupons.index') }}" class="btn btn-outline-primary w-100 text-start">
                <i class="bi bi-ticket me-2"></i>Мои купоны
            </a>
        </div>
    </div>

    <!-- Последние покупки -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3 class="section-title"><i class="bi bi-bag me-2"></i>Последние покупки</h3>
            <a href="{{ route('client.purchases.index') }}" class="section-link">Все покупки <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        @if($recentPurchases->count() > 0)
            <div class="table-responsive">
                <table class="purchases-table">
                    <thead>
                        <tr>
                            <th>№ заказа</th>
                            <th>Дата</th>
                            <th>Магазин</th>
                            <th>Товаров</th>
                            <th>Сумма</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPurchases as $purchase)
                        <tr>
                            <td>
                                <a href="{{ route('client.purchases.show', $purchase->id) }}">
                                    {{ $purchase->transaction_number }}
                                </a>
                            </td>
                            <td>{{ $purchase->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $purchase->shop?->name ?? '—' }}</td>
                            <td>{{ $purchase->items->count() }}</td>
                            <td style="font-weight: 600; color: var(--card-gold);">
                                {{ number_format($purchase->total_amount, 0, ',', ' ') }} с
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                У вас пока нет покупок. Совершите первую покупку в нашем магазине!
            </div>
        @endif
    </div>
    <!-- Активные купоны -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3 class="section-title"><i class="bi bi-ticket me-2"></i>Активные купоны</h3>
            <a href="{{ route('client.coupons.index') }}" class="section-link">Все купоны <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        @if($activeCoupons->count() > 0)
            <div class="coupons-grid">
                @foreach($activeCoupons->take(4) as $coupon)
                <div class="coupon-item">
                    <div class="coupon-code">{{ $coupon->code }}</div>
                    <div class="coupon-value">
                        @if($coupon->discount_type === 'percent')
                            {{ $coupon->discount_value }}%
                        @elseif($coupon->discount_type === 'wholesale')
                            Опт. цена
                        @endif
                    </div>
                    <div class="coupon-meta">
                        @if($coupon->valid_until)
                            <i class="bi bi-calendar me-1"></i>Действует до {{ $coupon->valid_until->format('d.m.Y') }}
                        @else
                            <i class="bi bi-infinity me-1"></i>Бессрочно
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                У вас пока нет активных купонов
            </div>
        @endif
    </div>
</div>

<script>
function printClientCard() {
    if (!document.getElementById('clientCard')) {
        alert('Сначала оформите скидочную карту у администратора');
        return;
    }
    window.print();
}
</script>
@endsection
