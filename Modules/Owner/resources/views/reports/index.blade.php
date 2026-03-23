@extends('layouts.app')
@section('page-title', 'Отчёты')
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

    /* Светлая тема */
    :root, [data-theme="light"] {
        --bg-color: #F2F2F7;
        --text-main: #1C1C1E;
        --text-muted: #8E8E93;
        --border-color: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(0, 0, 0, 0.05);
        --card-hover: rgba(255, 255, 255, 0.85);
    }

    /* Тёмная тема */
    [data-theme="dark"] {
        --bg-color: #000000;
        --text-main: #FFFFFF;
        --text-muted: rgba(255, 255, 255, 0.85);
        --border-color: rgba(255, 255, 255, 0.08);
        --glass-bg: rgba(28, 28, 30, 0.6);
        --glass-border: rgba(255, 255, 255, 0.06);
        --card-hover: rgba(28, 28, 30, 0.75);
    }

    /* === БАЗОВЫЕ СТИЛИ === */
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

    .report-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: var(--radius-lg);
        padding: 32px 24px;
        box-shadow: var(--shadow-md);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid var(--glass-border);
        text-align: center;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .report-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--card-accent) 0%, transparent 100%);
    }

    .report-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-lg);
        background: var(--card-hover);
    }

    .report-card.blue { --card-accent: var(--accent-blue); }
    .report-card.cyan { --card-accent: var(--accent-cyan); }
    .report-card.red { --card-accent: var(--accent-red); }
    .report-card.green { --card-accent: var(--accent-green); }

    .report-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
    }

    .report-icon.blue { background: rgba(0, 122, 255, 0.15); color: var(--accent-blue); border-color: rgba(0, 122, 255, 0.3); }
    .report-icon.cyan { background: rgba(90, 200, 250, 0.15); color: var(--accent-cyan); border-color: rgba(90, 200, 250, 0.3); }
    .report-icon.red { background: rgba(255, 59, 48, 0.15); color: var(--accent-red); border-color: rgba(255, 59, 48, 0.3); }
    .report-icon.green { background: rgba(52, 199, 89, 0.15); color: var(--accent-green); border-color: rgba(52, 199, 89, 0.3); }

    .report-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 10px;
    }

    .report-desc {
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 20px;
    }

    .btn-report {
        display: inline-block;
        padding: 10px 24px;
        background: linear-gradient(135deg, var(--accent-blue) 0%, #0056CC 100%);
        color: #FFFFFF;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 20px rgba(0, 122, 255, 0.3);
    }

    .btn-report:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 122, 255, 0.4);
        color: #FFFFFF;
    }

    .summary-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: var(--radius-lg);
        border: 1px solid var(--glass-border);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .summary-card .card-header {
        background: rgba(0, 122, 255, 0.08);
        padding: 24px 28px;
        border-bottom: 1px solid var(--glass-border);
    }

    .summary-card .card-header h5 {
        color: var(--text-main);
        font-weight: 700;
        margin: 0;
    }

    .summary-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 4px;
    }

    .summary-label {
        font-size: 13px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    small, .small {
        color: var(--text-muted) !important;
    }

    strong {
        color: var(--text-main);
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 24px 20px;
        }
        .page-header h2 {
            font-size: 24px;
        }
        .report-card {
            padding: 24px 20px;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Заголовок -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2"><i class="bi bi-file-earmark-bar me-2"></i>Отчёты</h2>
                <p class="mb-0">Детальная аналитика вашего бизнеса</p>
            </div>
        </div>
    </div>

    <!-- Карточки отчётов -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="report-card blue">
                <div class="report-icon blue">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h5 class="report-title">Прибыль</h5>
                <p class="report-desc">Анализ прибыли и маржинальности</p>
                <a href="{{ route('owner.module.reports.profit') }}" class="btn-report">Открыть</a>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="report-card cyan">
                <div class="report-icon cyan">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h5 class="report-title">Продажи</h5>
                <p class="report-desc">Динамика и структура продаж</p>
                <a href="{{ route('owner.module.reports.sales') }}" class="btn-report">Открыть</a>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="report-card green">
                <div class="report-icon green">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h5 class="report-title">Закупки</h5>
                <p class="report-desc">История закупок товаров</p>
                <a href="{{ route('owner.module.reports.purchases') }}" class="btn-report">Открыть</a>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="report-card red">
                <div class="report-icon red">
                    <i class="bi bi-receipt"></i>
                </div>
                <h5 class="report-title">Расходы</h5>
                <p class="report-desc">Все расходы по категориям</p>
                <a href="{{ route('owner.module.reports.expenses') }}" class="btn-report">Открыть</a>
            </div>
        </div>
    </div>

    <!-- Краткая сводка -->
    <div class="summary-card">
        <div class="card-header">
            <h5><i class="bi bi-info-circle me-2" style="color: var(--accent-blue);"></i>Краткая сводка за период</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="text-center">
                        <div class="summary-value" style="color: var(--accent-blue);">{{ number_format($revenue, 0, '.', ' ') }} сом</div>
                        <div class="summary-label">Выручка</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-center">
                        <div class="summary-value" style="color: var(--accent-red);">{{ number_format($costOfGoods, 0, '.', ' ') }} сом</div>
                        <div class="summary-label">Себестоимость</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-center">
                        <div class="summary-value" style="color: var(--accent-green);">{{ number_format($grossProfit, 0, '.', ' ') }} сом</div>
                        <div class="summary-label">Валовая прибыль</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-center">
                        <div class="summary-value" style="color: {{ $netProfit >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }};">
                            {{ number_format(abs($netProfit), 0, '.', ' ') }} сом
                        </div>
                        <div class="summary-label">Чистая прибыль</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
