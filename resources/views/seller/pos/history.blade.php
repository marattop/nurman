@extends('seller::layouts.seller')
@section('page-title', 'Мои продажи')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-receipt"></i></div>
    <div>
        <h1 class="page-title">Мои продажи</h1>
        <p class="page-subtitle">История продаж</p>
    </div>
</div>

{{-- Статистика --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Сегодня</div>
        <div style="font-size: 28px; font-weight: 700; color: #10b981;">{{ $stats['today_count'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Сумма сегодня</div>
        <div style="font-size: 28px; font-weight: 700; color: #38bdf8;">{{ number_format($stats['today'] ?? 0, 0, '.', ' ') }} сом</div>
    </div>
    <div class="stat-card">
        <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Всего</div>
        <div style="font-size: 28px; font-weight: 700; color: #f59e0b;">{{ number_format($stats['total'] ?? 0, 0, '.', ' ') }} сом</div>
    </div>
</div>

{{-- Таблица --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #10b981;"></i> Список продаж</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Дата</th>
                    <th>№ заказа</th>
                    <th>Сумма</th>
                    <th>Оплата</th>
                    <th>Товаров</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $sale->created_at->format('d.m.Y') }}</div>
                        <div style="color: #94a3b8; font-size: 13px;">{{ $sale->created_at->format('H:i') }}</div>
                    </td>
                    <td style="color: #94a3b8;">{{ $sale->transaction_number }}</td>
                    <td style="color: #10b981; font-weight: 600;">{{ number_format($sale->total_amount, 0, '.', ' ') }} сом</td>
                    <td>
                        <span class="badge badge-blue">{{ ucfirst($sale->payment_method ?? 'cash') }}</span>
                    </td>
                    <td style="color: #94a3b8;">{{ $sale->items->count() }}</td>
                    <td>
                        <a href="/seller/pos/receipt/{{ $sale->id }}" target="_blank" class="btn btn-outline">
                            <i class="bi bi-printer"></i> Чек
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                        Продаж ещё не было
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sales->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
        <div style="color: #94a3b8; font-size: 13px;">Показано {{ $sales->firstItem() ?? 0 }} - {{ $sales->lastItem() ?? 0 }} из {{ $sales->total() }}</div>
        <div style="display: flex; gap: 8px;">
            @if($sales->onFirstPage())
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">← Назад</span>
            @else
                <a href="{{ $sales->previousPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #10b981; text-decoration: none; font-size: 14px;">← Назад</a>
            @endif
            @if($sales->hasMorePages())
                <a href="{{ $sales->nextPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #10b981; text-decoration: none; font-size: 14px;">Вперёд →</a>
            @else
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">Вперёд →</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
