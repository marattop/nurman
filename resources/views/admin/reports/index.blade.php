@extends('layouts.app')
@section('page-title', 'Отчёты')
@section('content')
<style>
:root { --accent-green: #10b981; --accent-blue: #3b82f6; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); --card-hover: rgba(255, 255, 255, 0.85); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.1); --glass-bg: rgba(30, 30, 30, 0.8); --glass-border: rgba(255, 255, 255, 0.08); --card-hover: rgba(40, 40, 40, 0.9); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.report-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); padding: 2rem; text-align: center; border: 1px solid var(--glass-border); transition: all 0.3s; }
.report-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1); }
.report-icon { width: 64px; height: 64px; margin: 0 auto 1rem; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 32px; }
.report-icon.blue { background: rgba(59, 130, 246, 0.15); color: var(--accent-blue); }
.report-icon.green { background: rgba(16, 185, 129, 0.15); color: var(--accent-green); }
.report-title { font-size: 18px; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; }
.report-desc { font-size: 14px; color: var(--text-muted); margin-bottom: 1.5rem; }
.btn-report { display: inline-block; padding: 10px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; background: var(--gradient); color: white; }
.summary-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); border: 1px solid var(--glass-border); overflow: hidden; }
.summary-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); background: rgba(16, 185, 129, 0.08); font-weight: 700; color: var(--text-main); }
.summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; padding: 1.5rem; }
.summary-item { text-align: center; }
.summary-value { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
.summary-label { font-size: 12px; color: var(--text-muted); text-transform: uppercase; }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;"><div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--gradient); color: white; font-size: 1.25rem;"><i class="bi bi-file-earmark-bar"></i></div><div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">Отчёты</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Детальная аналитика</p></div></div>
    <div class="report-grid">
        <div class="report-card"><div class="report-icon blue"><i class="bi bi-graph-up"></i></div><h3 class="report-title">Продажи</h3><p class="report-desc">Динамика и структура продаж</p><a href="{{ route('admin.reports.sales') }}" class="btn-report">Открыть</a></div>
        <div class="report-card"><div class="report-icon green"><i class="bi bi-box-seam"></i></div><h3 class="report-title">Товары</h3><p class="report-desc">Популярность товаров</p><a href="{{ route('admin.reports.products') }}" class="btn-report">Открыть</a></div>
        <div class="report-card"><div class="report-icon blue"><i class="bi bi-layers"></i></div><h3 class="report-title">Остатки</h3><p class="report-desc">Анализ складских остатков</p><a href="{{ route('admin.reports.stock') }}" class="btn-report">Открыть</a></div>
    </div>
    <div class="summary-card">
        <div class="summary-header"><i class="bi bi-info-circle me-2"></i>Краткая сводка за период</div>
        <div class="summary-grid">
            <div class="summary-item"><div class="summary-value" style="color: var(--accent-blue);">{{ number_format($revenue ?? 0, 0, '.', ' ') }} сом</div><div class="summary-label">Выручка</div></div>
            <div class="summary-item"><div class="summary-value" style="color: var(--accent-red);">{{ number_format($costOfGoods ?? 0, 0, '.', ' ') }} сом</div><div class="summary-label">Себестоимость</div></div>
            <div class="summary-item"><div class="summary-value" style="color: var(--accent-green);">{{ number_format($grossProfit ?? 0, 0, '.', ' ') }} сом</div><div class="summary-label">Валовая прибыль</div></div>
            <div class="summary-item"><div class="summary-value" style="color: {{ ($netProfit ?? 0) >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }};">{{ number_format(abs($netProfit ?? 0), 0, '.', ' ') }} сом</div><div class="summary-label">Чистая прибыль</div></div>
        </div>
    </div>
</div>
@endsection
