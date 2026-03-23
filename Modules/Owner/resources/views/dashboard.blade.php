@extends('layouts.app')
@section('page-title', 'Панель владельца')
@section('content')

<style>
    /* === 1. БАЗОВЫЕ ПЕРЕМЕННЫЕ === */
    :root {
        /* Акцентные цвета (общие для обеих тем) */
        --accent-blue: #007AFF;
        --accent-green: #34C759;
        --accent-orange: #FF9500;
        --accent-red: #FF3B30;
        --accent-purple: #AF52DE;
        --accent-cyan: #5AC8FA;
        
        /* Скругления */
        --radius-lg: 24px;
        --radius-xl: 32px;
        
        /* Тени */
        --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.06);
        --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.12);
        --shadow-xl: 0 30px 70px rgba(0, 0, 0, 0.15);
    }

    /* Светлая тема */
    :root, [data-theme="light"] {
        --bg-color: #F2F2F7;
        --text-main: #1C1C1E;
        --text-muted: #8E8E93;
        --text-secondary: #636366;
        --border-color: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(0, 0, 0, 0.05);
        --card-hover: rgba(255, 255, 255, 0.85);
        --badge-bg: rgba(0, 0, 0, 0.08);
        --badge-text: #1C1C1E;
        --trend-up-bg: rgba(52, 199, 89, 0.15);
        --trend-up-text: #34C759;
        --trend-down-bg: rgba(255, 59, 48, 0.15);
        --trend-down-text: #FF3B30;
    }

    /* Тёмная тема */
    [data-theme="dark"] {
        --bg-color: #000000;
        --text-main: #FFFFFF;
        --text-muted: rgba(255, 255, 255, 0.85);
        --text-secondary: rgba(255, 255, 255, 0.7);
        --border-color: rgba(255, 255, 255, 0.08);
        --glass-bg: rgba(28, 28, 30, 0.6);
        --glass-border: rgba(255, 255, 255, 0.06);
        --card-hover: rgba(28, 28, 30, 0.75);
        --badge-bg: rgba(255, 255, 255, 0.15);
        --badge-text: #FFFFFF;
        --trend-up-bg: rgba(52, 199, 89, 0.15);
        --trend-up-text: #5AC8FA;
        --trend-down-bg: rgba(255, 59, 48, 0.15);
        --trend-down-text: #FF3B30;
    }

    /* === 2. БАЗОВЫЕ СТИЛИ === */
    body {
        background-color: var(--bg-color);
        color: var(--text-main);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* === 3. КАРТОЧКИ (GLASSMORPHISM) === */
    .dashboard-header,
    .date-filter-card,
    .stat-card,
    .chart-card,
    .data-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .dashboard-header {
        padding: 40px;
        margin-bottom: 32px;
        box-shadow: var(--shadow-lg);
    }

    .dashboard-header h2 {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .dashboard-header p {
        font-size: 15px;
        color: var(--text-muted);
        font-weight: 400;
    }

    .date-filter-card {
        padding: 28px;
        margin-bottom: 32px;
        box-shadow: var(--shadow-md);
    }

    .stat-card {
        padding: 32px;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        background: var(--card-hover);
        box-shadow: var(--shadow-xl);
        transform: translateY(-8px) scale(1.02);
    }

    .chart-card, .data-card {
        padding: 32px;
        box-shadow: var(--shadow-md);
    }

    /* === 4. СТАТИСТИКА === */
    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 20px;
        background: var(--badge-bg);
        border: 1px solid var(--glass-border);
    }

    .stat-icon.revenue { background: rgba(0, 122, 255, 0.15); color: var(--accent-blue); border-color: rgba(0, 122, 255, 0.3); }
    .stat-icon.cost { background: rgba(255, 59, 48, 0.15); color: var(--accent-red); border-color: rgba(255, 59, 48, 0.3); }
    .stat-icon.gross { background: rgba(52, 199, 89, 0.15); color: var(--accent-green); border-color: rgba(52, 199, 89, 0.3); }
    .stat-icon.net { background: rgba(90, 200, 250, 0.15); color: var(--accent-cyan); border-color: rgba(90, 200, 250, 0.3); }
    .stat-icon.expenses { background: rgba(255, 149, 0, 0.15); color: var(--accent-orange); border-color: rgba(255, 149, 0, 0.3); }
    .stat-icon.purchases { background: rgba(175, 82, 222, 0.15); color: var(--accent-purple); border-color: rgba(175, 82, 222, 0.3); }

    .stat-label {
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
        letter-spacing: -1px;
    }

    .stat-trend {
        font-size: 13px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .stat-trend.up {
        background: var(--trend-up-bg);
        color: var(--trend-up-text);
    }

    .stat-trend.down {
        background: var(--trend-down-bg);
        color: var(--trend-down-text);
    }

    /* === 5. ТАБЛИЦЫ === */
    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-custom th {
        padding: 14px 20px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-custom td {
        padding: 18px 20px;
        font-size: 14px;
        color: var(--text-main);
        border-bottom: 1px solid var(--border-color);
    }

    .table-custom tr:last-child td {
        border-bottom: none;
    }

    .table-custom tr:hover {
        background: var(--badge-bg);
    }

    /* === 6. BADGES === */
    .badge-custom {
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        background: var(--badge-bg);
        color: var(--badge-text);
    }

    .badge-custom.bg-info {
        background: rgba(0, 122, 255, 0.15);
        color: var(--accent-blue);
    }

    /* === 7. MINI STAT === */
    .mini-stat {
        background: var(--badge-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 16px 24px;
        border-radius: 16px;
        text-align: center;
        border: 1px solid var(--glass-border);
    }

    .mini-stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-main);
    }

    .mini-stat-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* === 8. ЗАГОЛОВКИ СЕКЦИЙ === */
    .chart-title,
    .data-header {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-main);
        letter-spacing: -0.3px;
        margin-bottom: 24px;
    }

    /* === 9. ФОРМЫ === */
    .form-label {
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 500;
    }

    .form-control {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-main);
        border-radius: 12px;
        padding: 10px 14px;
    }

    .form-control:focus {
        background: var(--card-hover);
        border-color: var(--accent-blue);
        color: var(--text-main);
    }

    /* === 10. КНОПКИ === */
    .btn {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-blue) 0%, #0056CC 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 20px rgba(0, 122, 255, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 122, 255, 0.4);
    }

    .btn-filter {
        background: var(--badge-bg);
        color: var(--text-muted);
        border: 1px solid var(--glass-border);
    }

    .btn-filter.active,
    .btn-filter:hover {
        background: var(--accent-blue);
        color: #FFFFFF;
        border-color: var(--accent-blue);
    }

    /* === 11. ВТОРОСТЕПЕННЫЙ ТЕКСТ === */
    small, .small, .text-muted {
        color: var(--text-muted) !important;
    }

    strong {
        color: var(--text-main);
    }

    /* === 12. АНИМАЦИИ === */
    .stat-card, .chart-card, .data-card {
        animation: fadeInUp 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* === 13. МОБИЛЬНАЯ АДАПТАЦИЯ === */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 24px 20px;
            border-radius: var(--radius-lg);
        }

        .dashboard-header h2 {
            font-size: 24px;
        }

        .stat-card {
            padding: 24px;
        }

        .stat-value {
            font-size: 26px;
        }

        .chart-card, .data-card {
            padding: 24px;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Заголовок -->
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2">
                    <i class="bi bi-speedometer2 me-2"></i>Панель владельца
                </h2>
                <p class="mb-0">Полная аналитика вашего бизнеса</p>
            </div>
            <div class="d-flex gap-3">
                <div class="mini-stat">
                    <div class="mini-stat-value">{{ $totalShops }}</div>
                    <div class="mini-stat-label">Магазинов</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-value">{{ $totalEmployees }}</div>
                    <div class="mini-stat-label">Сотрудников</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтр дат -->
    <div class="date-filter-card">
        <form method="GET" action="{{ route('owner.module.dashboard') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small">Период</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="?period=today" class="btn btn-sm btn-filter {{ request('period') === 'today' ? 'active' : '' }}">Сегодня</a>
                        <a href="?period=week" class="btn btn-sm btn-filter {{ request('period') === 'week' ? 'active' : '' }}">Неделя</a>
                        <a href="?period=month" class="btn btn-sm btn-filter {{ request('period', 'month') === 'month' ? 'active' : '' }}">Месяц</a>
                        <a href="?period=year" class="btn btn-sm btn-filter {{ request('period') === 'year' ? 'active' : '' }}">Год</a>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small">Дата от</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from', $dateFrom) }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small">Дата до</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to', $dateTo) }}">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>Применить
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <small><i class="bi bi-calendar3 me-1"></i>{{ $periodLabel }}</small>
            </div>
        </form>
    </div>

    <!-- Основные метрики -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card revenue">
                <div class="stat-icon revenue">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-label">Выручка</div>
                <div class="stat-value">{{ number_format($revenue->total, 0, '.', ' ') }} сом</div>
                <div>
                    @if($revenueGrowth > 0)
                        <span class="stat-trend up"><i class="bi bi-arrow-up"></i> +{{ number_format(abs($revenueGrowth), 1) }}%</span>
                    @elseif($revenueGrowth < 0)
                        <span class="stat-trend down"><i class="bi bi-arrow-down"></i> {{ number_format(abs($revenueGrowth), 1) }}%</span>
                    @endif
                    <span class="small ms-2">{{ $revenue->count }} продаж</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card cost">
                <div class="stat-icon cost">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-label">Себестоимость</div>
                <div class="stat-value">{{ number_format($costOfGoods, 0, '.', ' ') }} сом</div>
                <div class="small">
                    <i class="bi bi-box"></i> Закупки: {{ number_format($purchases->total, 0, '.', ' ') }} сом
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card gross">
                <div class="stat-icon gross">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="stat-label">Валовая прибыль</div>
                <div class="stat-value">{{ number_format($grossProfit, 0, '.', ' ') }} сом</div>
                <div>
                    <span class="stat-trend up" style="background: var(--trend-up-bg); color: var(--trend-up-text);">
                        {{ number_format($grossMargin, 1) }}% маржинальность
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card net">
                <div class="stat-icon net">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div class="stat-label">Чистая прибыль</div>
                <div class="stat-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="color: {{ $netProfit >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }};">
                    {{ number_format(abs($netProfit), 0, '.', ' ') }} сом
                </div>
                <div>
                    @if($profitGrowth > 0)
                        <span class="stat-trend up"><i class="bi bi-arrow-up"></i> +{{ number_format(abs($profitGrowth), 1) }}%</span>
                    @elseif($profitGrowth < 0)
                        <span class="stat-trend down"><i class="bi bi-arrow-down"></i> {{ number_format(abs($profitGrowth), 1) }}%</span>
                    @endif
                    <span class="small ms-2">{{ number_format($netMargin, 1) }}%</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card expenses">
                <div class="stat-icon expenses">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <div class="stat-label">Расходы</div>
                <div class="stat-value">{{ number_format($expenses->total, 0, '.', ' ') }} сом</div>
                <div>
                    @if($expensesGrowth < 0)
                        <span class="stat-trend up"><i class="bi bi-arrow-down"></i> -{{ number_format(abs($expensesGrowth), 1) }}%</span>
                    @elseif($expensesGrowth > 0)
                        <span class="stat-trend down"><i class="bi bi-arrow-up"></i> +{{ number_format(abs($expensesGrowth), 1) }}%</span>
                    @endif
                    <span class="small ms-2">{{ $expenses->count }} операций</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card purchases">
                <div class="stat-icon purchases">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stat-label">Закупки товаров</div>
                <div class="stat-value">{{ number_format($purchases->total, 0, '.', ' ') }} сом</div>
                <div class="small">
                    <i class="bi bi-box-seam"></i> {{ number_format($purchases->quantity, 0, '.', ' ') }} ед.
                </div>
            </div>
        </div>
    </div>

    <!-- Графики -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title"><i class="bi bi-graph-up me-2"></i>Динамика выручки и прибыли</div>
                </div>
                <div style="height: 320px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title"><i class="bi bi-pie-chart me-2"></i>По способам оплаты</div>
                </div>
                <div style="height: 320px;">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Детализация -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="data-card">
                <div class="data-header"><i class="bi bi-shop me-2"></i>По магазинам</div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Магазин</th>
                                <th class="text-center">Продаж</th>
                                <th class="text-end">Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revenueByShop as $shop)
                            <tr>
                                <td><strong>{{ $shop->name }}</strong></td>
                                <td class="text-center"><span class="badge-custom">{{ $shop->count }}</span></td>
                                <td class="text-end"><strong style="color: var(--accent-blue);">{{ number_format($shop->total, 0, '.', ' ') }} сом</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="data-card">
                <div class="data-header"><i class="bi bi-receipt me-2"></i>Расходы по категориям</div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Категория</th>
                                <th class="text-end">Сумма</th>
                                <th class="text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalExp = $expensesByCategory->sum('total') ?? 1; @endphp
                            @foreach($expensesByCategory as $cat)
                            <tr>
                                <td>
                                    @if($cat->category === 'rent') <i class="bi bi-house-door" style="color: var(--accent-orange);"></i> Аренда
                                    @elseif($cat->category === 'utilities') <i class="bi bi-lightning" style="color: var(--accent-cyan);"></i> Коммуналка
                                    @elseif($cat->category === 'salary') <i class="bi bi-people" style="color: var(--accent-green);"></i> Зарплата
                                    @elseif($cat->category === 'taxes') <i class="bi bi-bank" style="color: var(--accent-red);"></i> Налоги
                                    @elseif($cat->category === 'marketing') <i class="bi bi-megaphone" style="color: var(--accent-blue);"></i> Маркетинг
                                    @else <i class="bi bi-three-dots" style="color: var(--text-muted);"></i> {{ ucfirst($cat->category) }}
                                    @endif
                                </td>
                                <td class="text-end"><strong>{{ number_format($cat->total, 0, '.', ' ') }} сом</strong></td>
                                <td class="text-center">
                                    <span class="badge-custom bg-info">{{ number_format(($cat->total / $totalExp) * 100, 1) }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Топ товаров и последние продажи -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="data-card">
                <div class="data-header"><i class="bi bi-trophy me-2"></i>Топ товаров по прибыли</div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Товар</th>
                                <th class="text-center">Продано</th>
                                <th class="text-end">Прибыль</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $p)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($p->name, 35) }}</strong>
                                    <br><small>{{ $p->article }}</small>
                                </td>
                                <td class="text-center"><span class="badge-custom bg-info">{{ $p->sold_qty }} шт</span></td>
                                <td class="text-end"><strong style="color: var(--accent-green);">+{{ number_format($p->profit, 0, '.', ' ') }} сом</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="data-card">
                <div class="data-header"><i class="bi bi-clock-history me-2"></i>Последние продажи</div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Сумма</th>
                                <th>Магазин</th>
                                <th>Продавец</th>
                                <th>Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                            <tr>
                                <td><small>#{{ $sale->id }}</small></td>
                                <td><strong style="color: var(--accent-blue);">{{ number_format($sale->total_amount, 0, '.', ' ') }} сом</strong></td>
                                <td><small>{{ $sale->shop?->name ?? '-' }}</small></td>
                                <td><small>{{ $sale->user?->full_name ?? $sale->user?->username ?? '-' }}</small></td>
                                <td><small>{{ $sale->created_at->format('d.m H:i') }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenueDataRaw = @json($revenueDynamics);
    const profitDataRaw = @json($profitDynamics);
    
    const revenueData = Array.isArray(revenueDataRaw) ? revenueDataRaw : [];
    const profitData = Array.isArray(profitDataRaw) ? profitDataRaw : [];

    const dates = revenueData.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' });
    });
    const revenues = revenueData.map(d => parseFloat(d.revenue || 0));
    const profits = profitData.map(d => parseFloat(d.profit || 0));

    const allValues = [...revenues, ...profits];
    const maxValue = Math.max(...allValues);
    const minValue = Math.min(...allValues);
    const padding = maxValue * 0.1;

    // Определение текущей темы
    const html = document.documentElement;
    const isLightTheme = html.getAttribute('data-theme') === 'light';
    
    const textColor = isLightTheme ? '#1C1C1E' : '#FFFFFF';
    const gridColor = isLightTheme ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)';
    const tooltipBg = isLightTheme ? 'rgba(255, 255, 255, 0.98)' : 'rgba(28, 28, 30, 0.95)';
    const tooltipText = isLightTheme ? '#1C1C1E' : '#FFFFFF';
    const tooltipBody = isLightTheme ? '#1C1C1E' : '#FFFFFF';
    const tooltipBorder = isLightTheme ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)';
    const pointBorderColor = isLightTheme ? '#FFFFFF' : '#1C1C1E';

    // График выручки и прибыли
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Выручка',
                    data: revenues,
                    borderColor: '#007AFF',
                    backgroundColor: 'rgba(0, 122, 255, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#007AFF',
                    pointBorderColor: pointBorderColor,
                    pointBorderWidth: 2
                },
                {
                    label: 'Прибыль',
                    data: profits,
                    borderColor: '#34C759',
                    backgroundColor: 'rgba(52, 199, 89, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#34C759',
                    pointBorderColor: pointBorderColor,
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: textColor,
                        font: {
                            family: '-apple-system, BlinkMacSystemFont, "Inter", sans-serif',
                            size: 13
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: tooltipBg,
                    backdropFilter: 'blur(20px)',
                    titleColor: tooltipText,
                    bodyColor: tooltipBody,
                    borderColor: tooltipBorder,
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    cornerRadius: 12,
                    titleFont: {
                        family: '-apple-system, BlinkMacSystemFont, "Inter", sans-serif',
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        family: '-apple-system, BlinkMacSystemFont, "Inter", sans-serif',
                        size: 13
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            family: '-apple-system, BlinkMacSystemFont, "Inter", sans-serif',
                            size: 12
                        }
                    }
                },
                y: {
                    grid: {
                        color: gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            family: '-apple-system, BlinkMacSystemFont, "Inter", sans-serif',
                            size: 12
                        },
                        callback: function(value) {
                            return value.toLocaleString('ru-RU');
                        }
                    },
                    min: Math.max(0, minValue - padding),
                    max: maxValue + padding
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // График по способам оплаты
    const paymentData = @json($revenueBySource);
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(d => {
                const map = {
                    'cash': 'Наличные',
                    'card': 'Карта',
                    'qr': 'QR-код'
                };
                return map[d.payment_method] || d.payment_method;
            }),
            datasets: [{
                data: paymentData.map(d => parseFloat(d.total)),
                backgroundColor: [
                    'rgba(0, 122, 255, 0.8)',
                    'rgba(52, 199, 89, 0.8)',
                    'rgba(255, 149, 0, 0.8)'
                ],
                borderColor: isLightTheme ? 'rgba(255, 255, 255, 1)' : 'rgba(28, 28, 30, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: textColor,
                        font: {
                            family: '-apple-system, BlinkMacSystemFont, "Inter", sans-serif',
                            size: 13
                        },
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: tooltipBg,
                    backdropFilter: 'blur(20px)',
                    titleColor: tooltipText,
                    bodyColor: tooltipBody,
                    borderColor: tooltipBorder,
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: {
                        family: '-apple-system, BlinkMacSystemFont, "Inter", sans-serif',
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        family: '-apple-system, BlinkMacSystemFont, "Inter", sans-serif',
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return value.toLocaleString('ru-RU') + ' сом (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
