@extends('layouts.admin')
@section('page-title', 'Продажи')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-graph-up-arrow"></i></div>
    <div>
        <h1 class="page-title">Продажи</h1>
        <p class="page-subtitle">Отчёт по продажам</p>
    </div>
    <div style="margin-left: auto;">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

{{-- Фильтры --}}
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <form action="{{ route('admin.reports.sales') }}" method="GET" style="padding: 16px 20px; display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">С даты</label>
            <input type="date" name="date_from" class="input" value="{{ $dateFrom }}">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">По дату</label>
            <input type="date" name="date_to" class="input" value="{{ $dateTo }}">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">Магазин</label>
            <select name="shop_id" class="input">
                <option value="">Все магазины</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Сформировать
            </button>
        </div>
    </form>
</div>

{{-- Статистика --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card">
        <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Всего продаж</div>
        <div style="font-size: 28px; font-weight: 700; color: #38bdf8;">{{ $totalTransactions }}</div>
    </div>
    <div class="card">
        <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Общая сумма</div>
        <div style="font-size: 28px; font-weight: 700; color: #10b981;">{{ number_format($totalAmount, 0, '.', ' ') }} сом</div>
    </div>
    <div class="card">
        <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Средний чек</div>
        <div style="font-size: 28px; font-weight: 700; color: #f59e0b;">{{ $totalTransactions > 0 ? number_format($totalAmount / $totalTransactions, 0, '.', ' ') : 0 }} сом</div>
    </div>
</div>

{{-- Таблица --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список продаж</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Дата</th>
                    <th style="text-align: center; padding: 12px 16px;">№</th>
                    <th style="text-align: left; padding: 12px 16px;">Кассир</th>
                    <th style="text-align: left; padding: 12px 16px;">Магазин</th>
                    <th style="text-align: right; padding: 12px 16px;">Сумма</th>
                    <th style="text-align: center; padding: 12px 16px;">Оплата</th>
                    <th style="text-align: center; padding: 12px 16px;">Товаров</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td style="padding: 12px 16px; vertical-align: middle;">
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <span style="font-weight: 500;">{{ $sale->created_at->format('d.m.Y') }}</span>
                            <span style="color: #94a3b8; font-size: 13px;">{{ $sale->created_at->format('H:i') }}</span>
                        </div>
                    </td>
                    <td style="padding: 12px 16px; vertical-align: middle; text-align: center; color: #94a3b8;">{{ $sale->transaction_number ?? $sale->id }}</td>
                    <td style="padding: 12px 16px; vertical-align: middle; text-align: left; color: #94a3b8;">{{ $sale->user?->username ?? '—' }}</td>
                    <td style="padding: 12px 16px; vertical-align: middle; text-align: left; color: #94a3b8;">{{ $sale->shop?->name ?? '—' }}</td>
                    <td style="padding: 12px 16px; vertical-align: middle; text-align: right; color: #10b981; font-weight: 600;">{{ number_format($sale->total_amount ?? 0, 0, '.', ' ') }} сом</td>
                    <td style="padding: 12px 16px; vertical-align: middle; text-align: center;">
                        <span class="badge badge-blue">{{ ucfirst($sale->payment_method ?? 'cash') }}</span>
                    </td>
                    <td style="padding: 12px 16px; vertical-align: middle; text-align: center; color: #94a3b8;">{{ $sale->items_count ?? ($sale->items ? $sale->items->sum('quantity') : 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                        Нет данных за выбранный период
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
