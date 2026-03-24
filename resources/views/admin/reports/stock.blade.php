@extends('layouts.admin')
@section('page-title', 'Остатки')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-layers"></i></div>
    <div>
        <h1 class="page-title">Остатки</h1>
        <p class="page-subtitle">Отчёт по складу</p>
    </div>
    <div style="margin-left: auto;">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

{{-- Статистика --}}
<div class="card" style="margin-bottom: 24px;">
    <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Общая стоимость запасов</div>
    <div style="font-size: 32px; font-weight: 700; color: #10b981;">{{ number_format($totalValue, 0, '.', ' ') }} сом</div>
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
                    <th>Размер</th>
                    <th>Магазин</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Стоимость</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stock as $item)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $item->name }}</div>
                    </td>
                    <td>
                        <span class="badge badge-blue">{{ $item->size }}</span>
                    </td>
                    <td style="color: #94a3b8;">{{ $item->shop_name ?? 'Склад' }}</td>
                    <td>
                        <span class="badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">{{ $item->quantity }} шт</span>
                    </td>
                    <td style="color: #94a3b8;">{{ number_format($item->retail_price, 0, '.', ' ') }} сом</td>
                    <td style="color: #10b981; font-weight: 600;">{{ number_format($item->total_value, 0, '.', ' ') }} сом</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                        Нет данных
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
