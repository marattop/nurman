@extends('layouts.app')
@section('page-title', 'Отчёт по продажам')
@section('content')

<style>
    :root {
        --accent-blue: #3b82f6;
        --accent-purple: #8b5cf6;
        --accent-green: #10b981;
        --accent-orange: #f59e0b;
        --accent-red: #ef4444;
        --accent-cyan: #06b6d4;
    }
    
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
        border-radius: 24px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 20px 60px rgba(30, 58, 95, 0.4);
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    .filter-card {
        background: var(--bg-card);
        border-radius: 20px;
        padding: 24px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        border: 1px solid var(--border-color);
    }
    
    .metric-card {
        background: var(--bg-card);
        border-radius: 20px;
        padding: 28px;
        box-shadow: var(--card-shadow);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }
    
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--card-accent) 0%, transparent 100%);
    }
    
    .metric-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border-color: var(--card-accent);
    }
    
    .metric-card.blue { --card-accent: var(--accent-blue); }
    .metric-card.cyan { --card-accent: var(--accent-cyan); }
    .metric-card.green { --card-accent: var(--accent-green); }
    
    .metric-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 18px;
    }
    
    .metric-icon.blue { 
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.15) 100%);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    .metric-icon.cyan { 
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.15) 0%, rgba(8, 145, 178, 0.15) 100%);
        color: #06b6d4;
        border: 1px solid rgba(6, 182, 212, 0.2);
    }
    .metric-icon.green { 
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    
    .metric-label {
        font-size: 14px;
        color: var(--text-secondary);
        margin-bottom: 10px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .metric-value {
        font-size: 30px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    
    .metric-sub {
        font-size: 13px;
        color: var(--text-muted);
    }
    
    .data-card {
        background: var(--bg-card);
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        border: 1px solid var(--border-color);
    }
    
    .data-header {
        padding: 24px 28px;
        border-bottom: 1px solid var(--border-color);
        font-weight: 700;
        color: var(--text-primary);
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.08) 0%, transparent 100%);
        font-size: 16px;
    }
    
    .table-custom {
        width: 100%;
        margin: 0;
    }
    
    .table-custom th {
        padding: 14px 20px;
        font-weight: 700;
        font-size: 12px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.08) 0%, transparent 100%);
        border-bottom: 2px solid rgba(59, 130, 246, 0.15);
    }
    
    .table-custom td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        color: var(--text-primary);
    }
    
    .table-custom tr:hover {
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, rgba(59, 130, 246, 0.02) 100%);
    }
    
    .badge-custom {
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 20px 15px;
            border-radius: 16px;
        }
        
        .page-header h2 {
            font-size: 20px !important;
        }
        
        .page-header p {
            font-size: 13px !important;
        }
        
        .metric-card {
            padding: 20px 18px;
        }
        
        .metric-icon {
            width: 52px;
            height: 52px;
            font-size: 24px;
        }
        
        .metric-value {
            font-size: 24px;
        }
        
        .metric-label {
            font-size: 12px !important;
        }
        
        .metric-sub {
            font-size: 11px !important;
        }
        
        .filter-card {
            padding: 20px 15px;
        }
        
        .data-header {
            padding: 18px 15px;
            font-size: 14px !important;
        }
        
        .table-custom {
            font-size: 12px;
        }
        
        .table-custom th {
            padding: 12px 12px;
            font-size: 10px;
        }
        
        .table-custom td {
            padding: 14px 12px;
            font-size: 13px;
        }
        
        .badge-custom {
            padding: 4px 10px;
            font-size: 11px;
        }
        
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }
    }
    
    @media (max-width: 576px) {
        .page-header {
            padding: 18px 12px;
        }
        
        .page-header h2 {
            font-size: 18px !important;
        }
        
        .metric-card {
            padding: 18px 15px;
        }
        
        .metric-icon {
            width: 48px;
            height: 48px;
            font-size: 22px;
        }
        
        .metric-value {
            font-size: 22px;
        }
        
        .col-12.col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    
    @media (max-width: 375px) {
        .page-header h2 {
            font-size: 16px !important;
        }
        
        .metric-value {
            font-size: 20px;
        }
        
        .metric-icon {
            width: 44px;
            height: 44px;
            font-size: 20px;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Заголовок -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2">
                    <i class="bi bi-cart3 me-2"></i>Отчёт по продажам
                </h2>
                <p class="mb-0" style="opacity: 0.9; font-size: 15px;">Детальная информация по всем продажам</p>
            </div>
            <a href="{{ route('owner.reports.index') }}" class="btn" style="
                background: rgba(255,255,255,0.1);
                color: white;
                border: 1px solid rgba(255,255,255,0.2);
                padding: 10px 20px;
                border-radius: 12px;
                transition: all 0.3s ease;
            "
            onmouseover="this.style.background='rgba(255,255,255,0.2)'"
            onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="bi bi-arrow-left me-1"></i>Назад
            </a>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="filter-card">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Период</label>
                <select name="period" class="form-select">
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Сегодня</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Неделя</option>
                    <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Месяц</option>
                    <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Год</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Магазин</label>
                <select name="shop_id" class="form-select">
                    <option value="">Все магазины</option>
                    @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Продавец</label>
                <select name="seller_id" class="form-select">
                    <option value="">Все продавцы</option>
                    @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}" {{ $sellerId == $seller->id ? 'selected' : '' }}>{{ $seller->full_name ?? $seller->username }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">&nbsp;</label>
                <button type="submit" class="btn w-100" style="
                    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                    color: white;
                    border: none;
                    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
                    padding: 10px 20px;
                    font-weight: 600;
                    border-radius: 12px;
                "
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 28px rgba(59, 130, 246, 0.5)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(59, 130, 246, 0.4)'">
                    <i class="bi bi-funnel me-1"></i>Фильтр
                </button>
            </div>
        </form>
    </div>

    <!-- Сводка -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="metric-card blue">
                <div class="metric-icon blue">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="metric-label">Всего продаж</div>
                <div class="metric-value">{{ $totalTransactions }}</div>
                <div class="metric-sub"><i class="bi bi-receipt"></i> транзакций</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="metric-card cyan">
                <div class="metric-icon cyan">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="metric-label">Общая выручка</div>
                <div class="metric-value">{{ number_format($totalSales, 0, '.', ' ') }} сом</div>
                <div class="metric-sub"><i class="bi bi-wallet2"></i> сумма</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="metric-card green">
                <div class="metric-icon green">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="metric-label">Продано товаров</div>
                <div class="metric-value">{{ number_format($totalItems, 0, '.', ' ') }}</div>
                <div class="metric-sub"><i class="bi bi-box"></i> единиц</div>
            </div>
        </div>
    </div>

    <!-- Таблица -->
    <div class="data-card">
        <div class="data-header">
            <i class="bi bi-list-ul me-2"></i>Детализация
        </div>
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Дата</th>
                        <th>Магазин</th>
                        <th>Продавец</th>
                        <th class="text-center">Товаров</th>
                        <th class="text-end">Сумма</th>
                        <th class="text-center">Оплата</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td><small class="text-muted">#{{ $sale->id }}</small></td>
                        <td>{{ $sale->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <span class="badge-custom" style="background: rgba(100, 116, 139, 0.15); color: var(--text-primary); border: 1px solid rgba(100, 116, 139, 0.3);">
                                {{ $sale->shop?->name ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $sale->user?->full_name ?? $sale->user?->username ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge-custom" style="background: rgba(6, 182, 212, 0.15); color: #06b6d4; border: 1px solid rgba(6, 182, 212, 0.3);">
                                {{ $sale->items->sum('quantity') }} шт
                            </span>
                        </td>
                        <td class="text-end" style="color: #3b82f6; font-weight: 700;">{{ number_format($sale->total_amount, 0, '.', ' ') }} сом</td>
                        <td class="text-center">
                            @if($sale->payment_method === 'cash')
                                <span class="badge-custom" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                    <i class="bi bi-cash"></i> Наличные
                                </span>
                            @elseif($sale->payment_method === 'card')
                                <span class="badge-custom" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3);">
                                    <i class="bi bi-credit-card"></i> Карта
                                </span>
                            @else
                                <span class="badge-custom" style="background: rgba(6, 182, 212, 0.15); color: #06b6d4; border: 1px solid rgba(6, 182, 212, 0.3);">
                                    <i class="bi bi-arrow-left-right"></i> Перевод
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i>
                            <p class="mt-3">Нет данных за выбранный период</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
