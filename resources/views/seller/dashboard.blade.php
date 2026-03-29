@extends('seller::layouts.seller')
@section('page-title', 'Дашборд')
@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Продаж сегодня</div>
                <div style="font-size: 32px; font-weight: 700; color: #10b981;">{{ $todayCount ?? 0 }}</div>
            </div>
            <div style="width: 56px; height: 56px; background: rgba(16,185,129,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
                <i class="bi bi-cart"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Выручка сегодня</div>
                <div style="font-size: 32px; font-weight: 700; color: #38bdf8;">{{ number_format($todaySales ?? 0, 0, '.', ' ') }} сом</div>
            </div>
            <div style="width: 56px; height: 56px; background: rgba(56,189,248,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 24px;">
                <i class="bi bi-cash"></i>
            </div>
        </div>
    </div>

    <div class="stat-card" style="cursor: pointer;" onclick="location.href='{{ route('seller.pos.index') }}'">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Новая продажа</div>
                <div style="font-size: 20px; font-weight: 600; color: #f59e0b;">Начать →</div>
            </div>
            <div style="width: 56px; height: 56px; background: rgba(245,158,11,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 24px;">
                <i class="bi bi-plus-circle"></i>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <a href="{{ route('seller.pos.index') }}" style="text-decoration: none;">
        <div class="card" style="text-align: center; transition: 0.2s;">
            <div style="width: 56px; height: 56px; margin: 0 auto 12px; background: rgba(16,185,129,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 28px;">
                <i class="bi bi-cart3"></i>
            </div>
            <div style="font-weight: 600; margin-bottom: 4px;">Продажа</div>
            <div style="font-size: 13px; color: #94a3b8;">Оформить заказ</div>
        </div>
    </a>

    <a href="{{ route('seller.my-sales.index') }}" style="text-decoration: none;">
        <div class="card" style="text-align: center; transition: 0.2s;">
            <div style="width: 56px; height: 56px; margin: 0 auto 12px; background: rgba(56,189,248,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 28px;">
                <i class="bi bi-receipt"></i>
            </div>
            <div style="font-weight: 600; margin-bottom: 4px;">Мои продажи</div>
            <div style="font-size: 13px; color: #94a3b8;">История</div>
        </div>
    </a>

    <a href="{{ route('seller.customers.index') }}" style="text-decoration: none;">
        <div class="card" style="text-align: center; transition: 0.2s;">
            <div style="width: 56px; height: 56px; margin: 0 auto 12px; background: rgba(245,158,11,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 28px;">
                <i class="bi bi-people"></i>
            </div>
            <div style="font-weight: 600; margin-bottom: 4px;">Клиенты</div>
            <div style="font-size: 13px; color: #94a3b8;">База клиентов</div>
        </div>
    </a>

    <a href="{{ route('seller.pos.index') }}" style="text-decoration: none;">
        <div class="card" style="text-align: center; transition: 0.2s;">
            <div style="width: 56px; height: 56px; margin: 0 auto 12px; background: rgba(129,140,248,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 28px;">
                <i class="bi bi-qr-code-scan"></i>
            </div>
            <div style="font-weight: 600; margin-bottom: 4px;">Сканер товаров</div>
            <div style="font-size: 13px; color: #94a3b8;">Сканировать</div>
        </div>
    </a>
</div>
@endsection
