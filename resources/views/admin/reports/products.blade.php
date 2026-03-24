@extends('layouts.admin')
@section('page-title', 'Товары')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-box-seam"></i></div>
    <div>
        <h1 class="page-title">Товары</h1>
        <p class="page-subtitle">Отчёт по товарам</p>
    </div>
    <div style="margin-left: auto;">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

{{-- Фильтры --}}
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <form action="{{ route('admin.reports.products') }}" method="GET" style="padding: 16px 20px; display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">С даты</label>
            <input type="date" name="date_from" class="input" value="{{ $dateFrom }}">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">По дату</label>
            <input type="date" name="date_to" class="input" value="{{ $dateTo }}">
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Сформировать
            </button>
        </div>
    </form>
</div>

{{-- Таблица --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список товаров</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Товар</th>
                    <th>Артикул</th>
                    <th>Продано шт</th>
                    <th>Выручка</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $product->name }}</div>
                    </td>
                    <td>
                        <code style="background: rgba(56,189,248,0.15); padding: 4px 8px; border-radius: 6px; color: #38bdf8; font-size: 13px;">{{ $product->article }}</code>
                    </td>
                    <td>
                        <span class="badge badge-blue">{{ number_format($product->sold_qty, 0) }} шт</span>
                    </td>
                    <td style="color: #10b981; font-weight: 600;">{{ number_format($product->revenue, 0, '.', ' ') }} сом</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem; color: #64748b;">
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
