@extends('layouts.app')
@section('page-title', 'Отчёт по прибыли')
@section('content')

<style>
    /* === БАЗОВЫЕ ПЕРЕМЕННЫЕ === */
    :root {
        --accent-blue: #007AFF;
        --accent-green: #34C759;
        --accent-orange: #FF9500;
        --accent-red: #FF3B30;
        --accent-purple: #AF52DE;
        --accent-cyan: #5AC8FA;
        --radius-lg: 24px;
        --radius-xl: 32px;
        --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.12);
    }

    :root, [data-theme="light"] {
        --bg-color: #F2F2F7;
        --text-main: #1C1C1E;
        --text-muted: #8E8E93;
        --border-color: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(0, 0, 0, 0.05);
        --card-hover: rgba(255, 255, 255, 0.85);
    }

    [data-theme="dark"] {
        --bg-color: #000000;
        --text-main: #FFFFFF;
        --text-muted: rgba(255, 255, 255, 0.85);
        --border-color: rgba(255, 255, 255, 0.08);
        --glass-bg: rgba(28, 28, 30, 0.6);
        --glass-border: rgba(255, 255, 255, 0.06);
        --card-hover: rgba(28, 28, 30, 0.75);
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-main);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .page-header {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 40px;
        color: var(--text-main);
        margin-bottom: 32px;
        box-shadow: var(--shadow-lg);
    }

    .page-header h2 {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
    }

    .page-header p {
        font-size: 15px;
        color: var(--text-muted);
    }

    .metric-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: var(--radius-lg);
        padding: 28px;
        box-shadow: var(--shadow-md);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid var(--glass-border);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--card-accent) 0%, transparent 100%);
    }

    .metric-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-lg);
        background: var(--card-hover);
    }

    .metric-card.blue { --card-accent: var(--accent-blue); }
    .metric-card.red { --card-accent: var(--accent-red); }
    .metric-card.green { --card-accent: var(--accent-green); }
    .metric-card.cyan { --card-accent: var(--accent-cyan); }

    .metric-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 20px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
    }

    .metric-icon.blue { background: rgba(0, 122, 255, 0.15); color: var(--accent-blue); border-color: rgba(0, 122, 255, 0.3); }
    .metric-icon.red { background: rgba(255, 59, 48, 0.15); color: var(--accent-red); border-color: rgba(255, 59, 48, 0.3); }
    .metric-icon.green { background: rgba(52, 199, 89, 0.15); color: var(--accent-green); border-color: rgba(52, 199, 89, 0.3); }
    .metric-icon.cyan { background: rgba(90, 200, 250, 0.15); color: var(--accent-cyan); border-color: rgba(90, 200, 250, 0.3); }

    .metric-label {
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 10px;
    }

    .metric-value {
        font-size: 30px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
    }

    .metric-sub {
        font-size: 13px;
        color: var(--text-muted);
    }

    .data-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--glass-border);
        overflow: hidden;
    }

    .data-header {
        padding: 24px 28px;
        border-bottom: 1px solid var(--glass-border);
        font-weight: 700;
        color: var(--text-main);
        background: rgba(0, 122, 255, 0.08);
    }

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
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
    }

    .table-custom tr:hover {
        background: var(--glass-border);
    }

    small, .small {
        color: var(--text-muted) !important;
    }

    strong {
        color: var(--text-main);
    }

    @media (max-width: 768px) {
        .page-header { padding: 24px 20px; }
        .page-header h2 { font-size: 24px; }
        .metric-card { padding: 20px 18px; }
        .metric-icon { width: 52px; height: 52px; font-size: 24px; }
        .metric-value { font-size: 24px; }
    }
</style>

<div class="container-fluid py-4">
    <!-- Заголовок -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2"><i class="bi bi-graph-up me-2"></i>Отчёт по прибыли</h2>
                <p class="mb-0">Анализ прибыли и маржинальности</p>
            </div>
        </div>
    </div>

    <!-- Основные метрики -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="metric-card blue">
                <div class="metric-icon blue"><i class="bi bi-currency-dollar"></i></div>
                <div class="metric-label">Выручка</div>
                <div class="metric-value">{{ number_format($revenue, 0, '.', ' ') }} сом</div>
                <div class="metric-sub">За выбранный период</div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="metric-card red">
                <div class="metric-icon red"><i class="bi bi-box-seam"></i></div>
                <div class="metric-label">Себестоимость</div>
                <div class="metric-value">{{ number_format($costOfGoods, 0, '.', ' ') }} сом</div>
                <div class="metric-sub">Закупки товаров</div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="metric-card green">
                <div class="metric-icon green"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="metric-label">Валовая прибыль</div>
                <div class="metric-value">{{ number_format($grossProfit, 0, '.', ' ') }} сом</div>
                <div class="metric-sub">{{ number_format($grossMargin, 1) }}% маржинальность</div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="metric-card cyan">
                <div class="metric-icon cyan"><i class="bi bi-piggy-bank"></i></div>
                <div class="metric-label">Чистая прибыль</div>
                <div class="metric-value">{{ number_format($netProfit, 0, '.', ' ') }} сом</div>
                <div class="metric-sub">{{ number_format($netMargin, 1) }}% рентабельность</div>
            </div>
        </div>
    </div>

    <!-- Детализация по категориям -->
    <div class="row g-4">
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
                                <td class="text-center"><small>{{ number_format(($cat->total / $totalExp) * 100, 1) }}%</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                            @foreach($productsProfit as $p)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($p->name, 35) }}</strong>
                                    <br><small>{{ $p->article }}</small>
                                </td>
                                <td class="text-center"><small>{{ $p->sold_qty }} шт</small></td>
                                <td class="text-end"><strong style="color: var(--accent-green);">+{{ number_format($p->profit, 0, '.', ' ') }} сом</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
