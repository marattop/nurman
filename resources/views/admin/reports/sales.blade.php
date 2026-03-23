@extends('layouts.app')

@section('title', 'Отчёт по продажам')

@section('content')
<style>
    .data-card {
        background: var(--bg-card);
        border-radius: 12px;
        box-shadow: var(--card-shadow);
    }
    .filter-section {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    .stat-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: var(--card-shadow);
        border-left: 4px solid var(--primary);
    }
    .stat-label {
        color: var(--text-secondary);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0;
    }
    .table {
        --bs-table-hover-bg: var(--table-hover);
    }
    .table th {
        font-weight: 600;
        color: var(--primary);
        border-top: none;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .page-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    .page-title-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gradient);
        color: white;
    }
    .amount-badge {
        background: var(--primary-light);
        color: var(--primary-dark);
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
    }
    .form-control, .form-select {
        background: var(--bg-card);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    .form-control:focus, .form-select:focus {
        background: var(--bg-card);
        border-color: var(--primary);
        color: var(--text-primary);
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
    .form-label {
        color: var(--text-primary);
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="page-title-icon">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <h1 class="h3 mb-0" style="color: var(--primary);">Отчёт по продажам</h1>
        <div class="ms-auto">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
        </div>
    </div>

    {{-- Фильтры --}}
    <div class="data-card mb-4">
        <div class="filter-section">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">С даты</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">По дату</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Магазин</label>
                    <select name="shop_id" class="form-select">
                        <option value="">Все магазины</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search"></i> Сформировать
                    </button>
                    <button type="button" class="btn" style="background: var(--gradient); color: white;">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Статистика --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Всего продаж</div>
                <h3 class="stat-value">{{ $totalTransactions }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Общая сумма</div>
                <h3 class="stat-value">{{ number_format($totalAmount, 2, ',', ' ') }} с</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Средний чек</div>
                <h3 class="stat-value">{{ $totalTransactions > 0 ? number_format($totalAmount / $totalTransactions, 2, ',', ' ') : 0 }} с</h3>
            </div>
        </div>
    </div>

    {{-- Таблица --}}
    <div class="data-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>№</th>
                        <th>Кассир</th>
                        <th>Магазин</th>
                        <th>Сумма</th>
                        <th>Оплата</th>
                        <th>Товаров</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $sale->created_at->format('d.m.Y') }}</div>
                            <small class="text-muted">{{ $sale->created_at->format('H:i') }}</small>
                        </td>
                        <td><code>{{ $sale->id }}</code></td>
                        <td>{{ $sale->seller?->username ?? '—' }}</td>
                        <td>{{ $sale->shop?->name ?? '—' }}</td>
                        <td><span class="amount-badge">{{ number_format($sale->amount, 2) }} с</span></td>
                        <td>
                            <span class="badge" style="background: var(--primary-light); color: var(--primary-dark);">
                                {{ ucfirst($sale->payment_method ?? 'cash') }}
                            </span>
                        </td>
                        <td>{{ $sale->items_count ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div style="font-size: 3rem; color: var(--primary-light);">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <p class="text-muted mt-3">Нет данных за выбранный период</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
        <div class="p-3">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
