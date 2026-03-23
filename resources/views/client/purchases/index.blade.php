@extends('layouts.app')

@section('title', 'История покупок - NURMAN')

@section('content')
<style>
    .purchases-page { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .filters-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }
    .purchases-table { width: 100%; border-collapse: collapse; }
    .purchases-table th, .purchases-table td {
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
    .purchases-table tr:hover { background: var(--bg-secondary); }
    .btn-view { color: var(--primary); text-decoration: none; }
    .btn-view:hover { text-decoration: underline; }
</style>

<div class="purchases-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🛒 История покупок</h1>
        <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> На главную
        </a>
    </div>

    <!-- Фильтры -->
    <div class="filters-card">
        <form method="GET" action="{{ route('client.purchases.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">С даты</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">По дату</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Магазин</label>
                    <select name="shop_id" class="form-select">
                        <option value="">Все магазины</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Фильтр
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Таблица -->
    @if($purchases->count() > 0)
        <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="table-responsive">
                <table class="purchases-table">
                    <thead>
                        <tr>
                            <th>№ заказа</th>
                            <th>Дата</th>
                            <th>Магазин</th>
                            <th>Товаров</th>
                            <th>Сумма</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                        <tr>
                            <td>
                                <a href="{{ route('client.purchases.show', $purchase->id) }}" class="btn-view">
                                    {{ $purchase->transaction_number }}
                                </a>
                            </td>
                            <td>{{ $purchase->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $purchase->shop?->name ?? '—' }}</td>
                            <td>{{ $purchase->items->count() }}</td>
                            <td style="font-weight: 600; color: var(--primary);">
                                {{ number_format($purchase->total_amount, 0, ',', ' ') }} с
                            </td>
                            <td>
                                <a href="{{ route('client.purchases.show', $purchase->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Подробнее
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $purchases->links() }}
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Покупок не найдено
        </div>
    @endif
</div>
@endsection
