@extends('layouts.app')
@section('page-title', 'Отчёт по закупкам')
@section('content')

<style>
    :root { --accent-blue: #3b82f6; --accent-green: #10b981; --accent-purple: #8b5cf6; }
    .page-header { background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); border-radius: 24px; padding: 30px; color: white; margin-bottom: 30px; box-shadow: 0 20px 60px rgba(30, 58, 95, 0.4); border: 1px solid rgba(59, 130, 246, 0.2); }
    .filter-card { background: var(--bg-card); border-radius: 20px; padding: 24px; box-shadow: var(--card-shadow); margin-bottom: 30px; border: 1px solid var(--border-color); }
    .metric-card { background: var(--bg-card); border-radius: 20px; padding: 28px; box-shadow: var(--card-shadow); transition: all 0.4s; height: 100%; border: 1px solid var(--border-color); }
    .metric-card:hover { transform: translateY(-8px); box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .metric-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 30px; margin-bottom: 18px; }
    .metric-icon.blue { background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(37,99,235,0.15)); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
    .metric-icon.green { background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(5,150,105,0.15)); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
    .metric-label { font-size: 14px; color: var(--text-secondary); margin-bottom: 10px; font-weight: 500; text-transform: uppercase; }
    .metric-value { font-size: 30px; font-weight: 800; color: var(--text-primary); }
    .metric-sub { font-size: 13px; color: var(--text-muted); }
    .data-card { background: var(--bg-card); border-radius: 20px; box-shadow: var(--card-shadow); overflow: hidden; border: 1px solid var(--border-color); }
    .data-header { padding: 24px 28px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-primary); background: linear-gradient(180deg, rgba(59,130,246,0.08), transparent); }
    .table-custom th { padding: 14px 20px; font-weight: 700; font-size: 12px; color: var(--text-secondary); text-transform: uppercase; background: linear-gradient(180deg, rgba(59,130,246,0.08), transparent); border-bottom: 2px solid rgba(59,130,246,0.15); }
    .table-custom td { padding: 16px 20px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-primary); }
    .table-custom tr:hover { background: linear-gradient(90deg, rgba(59,130,246,0.05), rgba(59,130,246,0.02)); }
    .badge-custom { padding: 6px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; }
    @media(max-width:768px) { 
        .page-header { padding: 20px 15px; border-radius: 16px; } 
        .page-header h2 { font-size: 20px !important; } 
        .page-header p { font-size: 13px !important; }
        .metric-card { padding: 20px 18px; } 
        .metric-icon { width: 52px; height: 52px; font-size: 24px; } 
        .metric-value { font-size: 24px; }
        .metric-label { font-size: 12px !important; }
        .metric-sub { font-size: 11px !important; }
        .filter-card { padding: 20px 15px; }
        .data-header { padding: 18px 15px; font-size: 14px !important; }
        .table-custom { font-size: 12px; }
        .table-custom th { padding: 12px 12px; font-size: 10px; }
        .table-custom td { padding: 14px 12px; font-size: 13px; }
        .badge-custom { padding: 4px 10px; font-size: 11px; }
        .table-responsive { -webkit-overflow-scrolling: touch; }
    }
    @media(max-width:576px) { 
        .page-header { padding: 18px 12px; } 
        .page-header h2 { font-size: 18px !important; }
        .metric-card { padding: 18px 15px; }
        .metric-icon { width: 48px; height: 48px; font-size: 22px; } 
        .metric-value { font-size: 22px; }
        .col-12.col-md-6 { flex: 0 0 100%; max-width: 100%; }
    }
    @media(max-width:375px) { 
        .page-header h2 { font-size: 16px !important; }
        .metric-value { font-size: 20px; }
        .metric-icon { width: 44px; height: 44px; font-size: 20px; }
    }
</style>

<div class="container-fluid py-4">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2"><i class="bi bi-truck me-2"></i>Отчёт по закупкам</h2>
                <p class="mb-0" style="opacity: 0.9; font-size: 15px;">История закупок товаров</p>
            </div>
            <a href="{{ route('owner.reports.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 12px;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="bi bi-arrow-left me-1"></i>Назад
            </a>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted">Период</label>
                <select name="period" class="form-select">
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Сегодня</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Неделя</option>
                    <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Месяц</option>
                    <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Год</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted">Магазин</label>
                <select name="shop_id" class="form-select">
                    <option value="">Все магазины</option>
                    @foreach($shops as $shop)<option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted">&nbsp;</label>
                <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; box-shadow: 0 4px 20px rgba(59,130,246,0.4); padding: 10px 20px; font-weight: 600; border-radius: 12px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 28px rgba(59,130,246,0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(59,130,246,0.4)'">
                    <i class="bi bi-funnel me-1"></i>Фильтр
                </button>
            </div>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <div class="metric-card">
                <div class="metric-icon blue"><i class="bi bi-receipt"></i></div>
                <div class="metric-label">Общая сумма закупок</div>
                <div class="metric-value">{{ number_format($totalAmount, 0, '.', ' ') }} сом</div>
                <div class="metric-sub"><i class="bi bi-calendar"></i> за период</div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="metric-card">
                <div class="metric-icon green"><i class="bi bi-box-seam"></i></div>
                <div class="metric-label">Всего закуплено единиц</div>
                <div class="metric-value">{{ number_format($totalQuantity, 0, '.', ' ') }}</div>
                <div class="metric-sub"><i class="bi bi-box"></i> товаров</div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="data-header"><i class="bi bi-list-ul me-2"></i>Детализация закупок</div>
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr><th>№</th><th>Дата</th><th>Товар</th><th>Магазин</th><th class="text-center">Кол-во</th><th class="text-end">Цена</th><th class="text-end">Сумма</th><th>Принял</th></tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td><small class="text-muted">#{{ $purchase->id }}</small></td>
                        <td>{{ $purchase->purchase_date->format('d.m.Y') }}</td>
                        <td><strong>{{ $purchase->variant?->product?->name ?? '-' }}</strong><br><small class="text-muted">{{ $purchase->variant?->size ?? '' }} {{ $purchase->variant?->color ?? '' }}</small></td>
                        <td><span class="badge-custom" style="background: rgba(100,116,139,0.15); border: 1px solid rgba(100,116,139,0.3);">{{ $purchase->shop?->name ?? '-' }}</span></td>
                        <td class="text-center"><span class="badge-custom" style="background: rgba(6,182,212,0.15); color: #06b6d4; border: 1px solid rgba(6,182,212,0.3);">{{ $purchase->quantity }} шт</span></td>
                        <td class="text-end">{{ number_format($purchase->supplier_price, 2, '.', ' ') }} сом</td>
                        <td class="text-end" style="color: #3b82f6; font-weight: 700;">{{ number_format($purchase->total_amount, 0, '.', ' ') }} сом</td>
                        <td><small>{{ $purchase->receivedBy?->full_name ?? $purchase->receivedBy?->username ?? '-' }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i><p class="mt-3">Нет данных</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())<div class="card-footer bg-white py-3">{{ $purchases->links() }}</div>@endif
    </div>
</div>
@endsection
