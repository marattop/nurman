@extends('layouts.admin')
@section('page-title', 'Дашборд')
@section('content')
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- Товаров -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Товаров</div>
                <div style="font-size: 32px; font-weight: 700; color: #10b981;">{{ $stats['products_count'] ?? 0 }}</div>
                <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                    <i class="bi bi-box-seam"></i> Активных: {{ $stats['products_active'] ?? 0 }}
                </div>
            </div>
            <div class="stats-icon" style="width: 56px; height: 56px; background: rgba(16,185,129,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
                <i class="bi bi-box"></i>
            </div>
        </div>
    </div>

    <!-- Продаж сегодня -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Продаж сегодня</div>
                <div style="font-size: 32px; font-weight: 700; color: #38bdf8;">{{ $stats['today_sales'] ?? 0 }}</div>
                <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                    <i class="bi bi-cash"></i> {{ number_format($stats['today_revenue'] ?? 0, 0, '.', ' ') }} сом
                </div>
            </div>
            <div class="stats-icon" style="width: 56px; height: 56px; background: rgba(56,189,248,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 24px;">
                <i class="bi bi-cart"></i>
            </div>
        </div>
    </div>

    <!-- Сотрудников -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Сотрудников</div>
                <div style="font-size: 32px; font-weight: 700; color: #818cf8;">{{ $stats['active_users'] ?? 0 }}</div>
                <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                    <i class="bi bi-people"></i> Всего: {{ $stats['users_count'] ?? 0 }}
                </div>
            </div>
            <div class="stats-icon" style="width: 56px; height: 56px; background: rgba(129,140,248,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 24px;">
                <i class="bi bi-person-badge"></i>
            </div>
        </div>
    </div>

    <!-- Магазинов -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Магазинов</div>
                <div style="font-size: 32px; font-weight: 700; color: #f59e0b;">{{ $stats['shops_count'] ?? 0 }}</div>
                <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                    <i class="bi bi-shop"></i> Работает
                </div>
            </div>
            <div class="stats-icon" style="width: 56px; height: 56px; background: rgba(245,158,11,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 24px;">
                <i class="bi bi-shop"></i>
            </div>
        </div>
    </div>
</div>

<div class="main-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
    <!-- Последние товары -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(56,189,248,0.05); font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-clock-history" style="color: #38bdf8;"></i> Последние товары
        </div>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; min-width: 500px;">
                <thead>
                    <tr>
                        <th style="text-align: left;">Товар</th>
                        <th>Артикул</th>
                        <th>Категория</th>
                        <th>Дата</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentProducts as $product)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                                <span style="font-weight: 500;">{{ Str::limit($product->name, 35) }}</span>
                            </div>
                        </td>
                        <td style="color: #94a3b8;">{{ $product->article }}</td>
                        <td style="color: #94a3b8;">{{ $product->category?->name ?? '—' }}</td>
                        <td style="color: #94a3b8;">{{ $product->created_at->format('d.m H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="bi bi-box" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                            Нет новых товаров
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Заканчиваются -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(239,68,68,0.05); font-weight: 600; display: flex; align-items: center; gap: 8px; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-exclamation-triangle" style="color: #ef4444;"></i> Заканчиваются
            </div>
            @if($deficitCount > 0)
                <a href="{{ route('admin.purchases.deficit') }}" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; font-size: 12px; padding: 6px 12px;">
                    {{ $deficitCount }} шт <i class="bi bi-arrow-right"></i>
                </a>
            @endif
        </div>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; min-width: 250px;">
                <thead>
                    <tr>
                        <th style="text-align: left;">Товар</th>
                        <th>Остаток</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockProducts as $product)
                    <tr>
                        <td>
                            <div style="font-weight: 500;">{{ Str::limit($product->name, 30) }}</div>
                            <div style="color: #94a3b8; font-size: 13px;">{{ $product->article }}</div>
                        </td>
                        <td>
                            @if($product->quantity <= 2)
                                <span class="badge badge-danger">{{ $product->quantity }} шт</span>
                            @elseif($product->quantity <= 5)
                                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">{{ $product->quantity }} шт</span>
                            @else
                                <span class="badge badge-success">{{ $product->quantity }} шт</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="bi bi-check-circle" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                            Всё в наличии
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .main-grid {
        grid-template-columns: 1fr !important;
    }
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
    .stats-icon {
        width: 48px !important;
        height: 48px !important;
        font-size: 20px !important;
    }
    .card {
        padding: 16px !important;
    }
    .page-title {
        font-size: 24px !important;
    }
}
</style>
@endsection
