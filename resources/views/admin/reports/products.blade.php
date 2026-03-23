@extends('layouts.app')

@section('title', 'Отчёт по товарам')

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
    .revenue-badge {
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
            <i class="bi bi-box-seam"></i>
        </div>
        <h1 class="h3 mb-0" style="color: var(--primary);">Отчёт по товарам</h1>
        <div class="ms-auto">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
        </div>
    </div>

    {{-- Фильтры --}}
    <div class="data-card mb-4">
        <div class="filter-section">
            <form action="{{ route('admin.reports.products') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">С даты</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">По дату</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
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

    {{-- Таблица --}}
    <div class="data-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Артикул</th>
                        <th style="width: 120px;">Продано шт</th>
                        <th style="width: 150px;">Выручка</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $product->name }}</div>
                        </td>
                        <td>
                            <code style="background: var(--primary-light); padding: 4px 8px; border-radius: 4px; color: var(--primary);">{{ $product->article }}</code>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--primary-light); color: var(--primary-dark);">
                                {{ number_format($product->sold_qty, 0) }} шт
                            </span>
                        </td>
                        <td>
                            <span class="revenue-badge">{{ number_format($product->revenue, 2, ',', ' ') }} с</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
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

        @if($products->hasPages())
        <div class="p-3">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
