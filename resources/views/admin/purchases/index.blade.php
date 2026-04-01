@extends('layouts.admin')
@section('page-title', 'Закупки товаров')
@section('content')

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="page-header">
    <div class="page-icon"><i class="bi bi-cart-plus"></i></div>
    <div>
        <h1 class="page-title">Закупки товаров</h1>
        <p class="page-subtitle">Управление закупками товаров</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0 sm:ml-auto w-full sm:w-auto">
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary justify-center">
            <i class="bi bi-plus-lg"></i> Создать закупку
        </a>
    </div>
</div>

{{-- Фильтры --}}
<div class="card" style="padding: 16px 20px; margin-bottom: 24px;">
    <form action="{{ route('admin.purchases.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:flex-1">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">С даты</label>
            <input type="date" name="date_from" class="input" value="{{ request('date_from') }}">
        </div>
        <div class="w-full sm:flex-1">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">По дату</label>
            <input type="date" name="date_to" class="input" value="{{ request('date_to') }}">
        </div>
        <button type="submit" class="btn btn-primary w-full sm:w-auto justify-center">
            <i class="bi bi-search"></i> Фильтр
        </button>
    </form>
</div>

{{-- Таблица --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список закупок</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Дата</th>
                    <th style="padding: 12px 16px;">№</th>
                    <th style="text-align: left; padding: 12px 16px;">Товар</th>
                    <th style="text-align: center; padding: 12px 16px;">Размер</th>
                    <th style="text-align: center; padding: 12px 16px;">Кол-во</th>
                    <th style="text-align: right; padding: 12px 16px;">Цена закупки</th>
                    <th style="text-align: right; padding: 12px 16px;">Сумма</th>
                    <th style="text-align: left; padding: 12px 16px;">Место</th>
                    <th style="text-align: left; padding: 12px 16px;">Принял</th>
                    <th style="text-align: right; padding: 12px 16px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td data-label="Дата" style="padding: 12px 16px;">
                        <div style="font-weight: 500;">{{ $purchase->purchase_date->format('d.m.Y') }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">{{ $purchase->purchase_date->format('H:i') }}</div>
                    </td>
                    <td data-label="№" style="padding: 12px 16px; text-align: center; color: #94a3b8;">
                        <span class="font-mono">{{ $purchase->invoice_number ?? '#' . $purchase->id }}</span>
                    </td>
                    <td data-label="Товар" style="padding: 12px 16px;">
                        <div style="font-weight: 500;">{{ $purchase->variant->product->name ?? '—' }}</div>
                    </td>
                    <td data-label="Размер" style="padding: 12px 16px; text-align: center;">
                        <span class="badge badge-blue">{{ $purchase->variant->size ?? '—' }}</span>
                    </td>
                    <td data-label="Кол-во" style="padding: 12px 16px; text-align: center;">
                        <span style="font-weight: 600;">{{ $purchase->quantity }} шт</span>
                    </td>
                    <td data-label="Цена закупки" style="padding: 12px 16px; text-align: right; color: #94a3b8;">
                        {{ number_format($purchase->supplier_price, 2, '.', ' ') }} сом
                    </td>
                    <td data-label="Сумма" style="padding: 12px 16px; text-align: right; color: #10b981; font-weight: 600;">
                        {{ number_format($purchase->total_amount, 2, '.', ' ') }} сом
                    </td>
                    <td data-label="Место" style="padding: 12px 16px;">
                        @if($purchase->shop_id)
                            <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                <i class="bi bi-shop"></i> {{ $purchase->shop->name ?? 'Магазин' }}
                            </span>
                        @elseif($purchase->warehouse_id)
                            <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                                <i class="bi bi-building"></i> {{ $purchase->warehouse->name ?? 'Склад' }}
                            </span>
                        @endif
                    </td>
                    <td data-label="Принял" style="padding: 12px 16px; color: #94a3b8;">
                        {{ $purchase->receivedBy->username ?? '—' }}
                    </td>
                    <td data-label="Действия" style="padding: 12px 16px; text-align: right;">
                        <form action="{{ route('admin.purchases.delete', $purchase) }}" method="POST" 
                            onsubmit="return confirm('Удалить эту закупку? Это действие нельзя отменить.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                        Нет закупок
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px; border-top: 1px solid rgba(148, 163, 184, 0.1);">
        {{ $purchases->links() }}
    </div>
</div>
@endsection
