@extends('layouts.admin')
@section('page-title', 'Отчёты')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-file-earmark-bar"></i></div>
    <div>
        <h1 class="page-title">Отчёты</h1>
        <p class="page-subtitle">Детальная аналитика</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="text-align: center;">
        <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: rgba(56,189,248,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 32px;">
            <i class="bi bi-graph-up"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 8px 0;">Продажи</h3>
        <p style="font-size: 14px; color: #94a3b8; margin: 0 0 16px 0;">Динамика и структура продаж</p>
        <a href="{{ route('admin.reports.sales') }}" class="btn btn-primary">Открыть</a>
    </div>

    <div class="card" style="text-align: center;">
        <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: rgba(16,185,129,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 32px;">
            <i class="bi bi-box-seam"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 8px 0;">Товары</h3>
        <p style="font-size: 14px; color: #94a3b8; margin: 0 0 16px 0;">Популярность товаров</p>
        <a href="{{ route('admin.reports.products') }}" class="btn btn-primary">Открыть</a>
    </div>

    <div class="card" style="text-align: center;">
        <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: rgba(245,158,11,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 32px;">
            <i class="bi bi-layers"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 8px 0;">Остатки</h3>
        <p style="font-size: 14px; color: #94a3b8; margin: 0 0 16px 0;">Анализ складских остатков</p>
        <a href="{{ route('admin.reports.stock') }}" class="btn btn-primary">Открыть</a>
    </div>
</div>
@endsection
