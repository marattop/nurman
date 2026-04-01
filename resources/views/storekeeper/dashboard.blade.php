@extends('layouts.storekeeper')

@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-speedometer2"></i></div>
    <div>
        <h1 class="page-title">Дашборд</h1>
        <p class="page-subtitle">Обзор склада</p>
    </div>
</div>

{{-- Статистика --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="margin: 0 0 8px 0; color: #94a3b8; font-size: 14px;">Всего товаров</p>
                <p style="margin: 0; font-size: 32px; font-weight: 700; color: #f1f5f9;">{{ $totalProducts }}</p>
            </div>
            <div style="width: 56px; height: 56px; border-radius: 12px; background: rgba(34,197,94,0.15); display: flex; align-items: center; justify-content: center; color: #22c55e; font-size: 24px;">
                <i class="bi bi-box"></i>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="margin: 0 0 8px 0; color: #94a3b8; font-size: 14px;">Вариантов</p>
                <p style="margin: 0; font-size: 32px; font-weight: 700; color: #f1f5f9;">{{ $totalVariants }}</p>
            </div>
            <div style="width: 56px; height: 56px; border-radius: 12px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 24px;">
                <i class="bi bi-layers"></i>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="margin: 0 0 8px 0; color: #94a3b8; font-size: 14px;">На складе</p>
                <p style="margin: 0; font-size: 32px; font-weight: 700; color: #f1f5f9;">{{ $totalStock }} <span style="font-size: 16px; color: #94a3b8;">шт</span></p>
            </div>
            <div style="width: 56px; height: 56px; border-radius: 12px; background: rgba(139,92,246,0.15); display: flex; align-items: center; justify-content: center; color: #8b5cf6; font-size: 24px;">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="margin: 0 0 8px 0; color: #94a3b8; font-size: 14px;">Мало на складе</p>
                <p style="margin: 0; font-size: 32px; font-weight: 700; color: #ef4444;">{{ $lowStock }}</p>
            </div>
            <div style="width: 56px; height: 56px; border-radius: 12px; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 24px;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

{{-- Последние товары --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-tags" style="color: #22c55e;"></i> Последние товары</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Товар</th>
                    <th style="text-align: left; padding: 12px 16px;">Артикул</th>
                    <th style="text-align: left; padding: 12px 16px;">Категория</th>
                    <th style="text-align: right; padding: 12px 16px;">Вариантов</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentProducts as $product)
                <tr>
                    <td data-label="Товар" style="padding: 12px 16px;">
                        <div style="font-weight: 500;">{{ $product->name }}</div>
                    </td>
                    <td data-label="Артикул" style="padding: 12px 16px; font-family: monospace; color: #94a3b8;">
                        {{ $product->article }}
                    </td>
                    <td data-label="Категория" style="padding: 12px 16px;">
                        <span class="badge badge-blue">{{ $product->category?->name ?? '—' }}</span>
                    </td>
                    <td data-label="Вариантов" style="padding: 12px 16px; text-align: right;">
                        <span style="font-weight: 600; color: #22c55e;">{{ $product->variants->count() }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                        Товары не найдены
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
