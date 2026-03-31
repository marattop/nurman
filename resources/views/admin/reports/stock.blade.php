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
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Товар</th>
                    <th style="text-align: center; padding: 12px 16px;">Размер</th>
                    <th style="text-align: center; padding: 12px 16px;">В магазине</th>
                    <th style="text-align: center; padding: 12px 16px;">На складе</th>
                    <th style="text-align: center; padding: 12px 16px;">Всего</th>
                    <th style="text-align: right; padding: 12px 16px;">Цена</th>
                    <th style="text-align: right; padding: 12px 16px;">Стоимость</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stock as $item)
                @php
                    $totalQty = ($item->shop_quantity ?? 0) + ($item->warehouse_quantity ?? 0);
                    $isOutOfStock = $totalQty <= 0;
                @endphp
                <tr style="{{ $isOutOfStock ? 'opacity: 0.5;' : '' }}">
                    <td data-label="Товар" style="padding: 12px 16px; vertical-align: middle; text-align: left;">
                        <div style="font-weight: 500;">{{ $item->name }}</div>
                    </td>
                    <td data-label="Размер" style="padding: 12px 16px; vertical-align: middle; text-align: center;">
                        <span class="badge badge-blue">{{ $item->size }}</span>
                    </td>
                    <td data-label="В магазине" style="padding: 12px 16px; vertical-align: middle; text-align: center;">
                        @if(($item->shop_quantity ?? 0) > 0)
                            <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                <i class="bi bi-shop"></i> {{ $item->shop_quantity }} шт
                            </span>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td data-label="На складе" style="padding: 12px 16px; vertical-align: middle; text-align: center;">
                        @if(($item->warehouse_quantity ?? 0) > 0)
                            <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                                <i class="bi bi-building"></i> {{ $item->warehouse_quantity }} шт
                            </span>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td data-label="Всего" style="padding: 12px 16px; vertical-align: middle; text-align: center;">
                        @if($isOutOfStock)
                            <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                                <i class="bi bi-x-circle"></i> Нет
                            </span>
                        @else
                            <span style="font-weight: 600; color: #f1f5f9;">{{ $totalQty }} шт</span>
                        @endif
                    </td>
                    <td data-label="Цена" style="padding: 12px 16px; vertical-align: middle; text-align: right; color: #94a3b8;">{{ number_format($item->retail_price, 0, '.', ' ') }} сом</td>
                    <td data-label="Стоимость" style="padding: 12px 16px; vertical-align: middle; text-align: right;">
                        @if($isOutOfStock)
                            <span style="color: #94a3b8;">Нет</span>
                        @else
                            <span style="color: #10b981; font-weight: 600;">{{ number_format($item->total_value ?? 0, 0, '.', ' ') }} сом</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">
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
