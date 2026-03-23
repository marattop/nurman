@extends('layouts.app')

@section('title', 'Отчёт по складу')

@section('content')
<style>
    .data-card {
        background: var(--bg-card);
        border-radius: 12px;
        box-shadow: var(--card-shadow);
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
    .value-badge {
        background: var(--primary-light);
        color: var(--primary-dark);
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="page-title-icon">
            <i class="bi bi-layers"></i>
        </div>
        <h1 class="h3 mb-0" style="color: var(--primary);">Отчёт по складу</h1>
        <div class="ms-auto">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
        </div>
    </div>

    {{-- Статистика --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Общая стоимость запасов</div>
                <h3 class="stat-value">{{ number_format($totalValue, 2, ',', ' ') }} с</h3>
            </div>
        </div>
    </div>

    {{-- Таблица --}}
    <div class="data-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Размер</th>
                        <th>Магазин</th>
                        <th style="width: 100px;">Количество</th>
                        <th style="width: 120px;">Цена</th>
                        <th style="width: 150px;">Стоимость</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stock as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->name }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--primary-light); color: var(--primary-dark);">{{ $item->size }}</span>
                        </td>
                        <td>{{ $item->shop_name ?? 'Склад' }}</td>
                        <td>
                            <span class="badge" style="background: var(--border-color); color: var(--text-primary);">{{ $item->quantity }} шт</span>
                        </td>
                        <td>{{ number_format($item->retail_price, 2) }} с</td>
                        <td>
                            <span class="value-badge">{{ number_format($item->total_value, 2, ',', ' ') }} с</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div style="font-size: 3rem; color: var(--primary-light);">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <p class="text-muted mt-3">Нет данных</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stock->hasPages())
        <div class="p-3">
            {{ $stock->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
