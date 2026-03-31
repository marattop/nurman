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

<div class="main-grid grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Последние товары -->
    <div class="lg:col-span-2 card !p-0 overflow-hidden border-white/5 shadow-lg">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5 font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-clock-history text-accent"></i> 
                <span>Последние товары</span>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs text-accent hover:underline">Все товары →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="table responsive-table w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-left px-5">Товар</th>
                        <th>Артикул</th>
                        <th>Категория</th>
                        <th class="text-right px-5">Дата</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentProducts as $product)
                    <tr class="hover:bg-white/[0.02] transition-colors border-b border-white/5 last:border-0">
                        <td data-label="Товар" class="px-5">
                            <div class="flex items-center gap-3 py-1">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" class="w-10 h-10 rounded-lg object-cover border border-white/10 shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-success/15 flex items-center justify-center text-success border border-success/10">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="font-bold text-text truncate max-w-[150px] sm:max-w-none">{{ $product->name }}</span>
                                    <span class="text-[10px] text-muted md:hidden">{{ $product->category?->name ?? '—' }}</span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Артикул" class="text-muted font-mono text-xs">{{ $product->article }}</td>
                        <td data-label="Категория" class="text-muted text-xs hidden md:table-cell">{{ $product->category?->name ?? '—' }}</td>
                        <td data-label="Дата" class="text-muted text-xs text-right px-5">{{ $product->created_at->format('d.m H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-12 text-muted italic">Нет новых товаров</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Заканчиваются -->
    <div class="card !p-0 overflow-hidden border-white/5 shadow-lg border-danger/10">
        <div class="px-5 py-4 border-b border-white/5 bg-danger/5 font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-exclamation-triangle text-danger"></i> 
                <span>Дефицит</span>
            </div>
            @if($deficitCount > 0)
                <span class="badge badge-danger">{{ $deficitCount }} поз.</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="table responsive-table w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-left px-5">Товар</th>
                        <th class="text-right px-5">Остаток</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockProducts as $product)
                    <tr class="hover:bg-white/[0.02] transition-colors border-b border-white/5 last:border-0">
                        <td data-label="Товар" class="px-5 py-3">
                            <div class="font-bold text-text text-sm truncate max-w-[120px] sm:max-w-none">{{ $product->name }}</div>
                            <div class="text-[10px] text-muted font-mono">{{ $product->article }}</div>
                        </td>
                        <td data-label="Остаток" class="text-right px-5">
                            @if($product->quantity <= 2)
                                <span class="text-danger font-bold">{{ $product->quantity }} шт</span>
                            @elseif($product->quantity <= 5)
                                <span class="text-warning font-bold">{{ $product->quantity }} шт</span>
                            @else
                                <span class="text-success font-bold">{{ $product->quantity }} шт</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center py-12 text-muted italic">Всё в наличии</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deficitCount > 5)
        <div class="p-3 bg-white/5 border-t border-white/5 text-center">
            <a href="{{ route('admin.purchases.deficit') }}" class="text-xs text-accent hover:underline">Смотреть весь дефицит</a>
        </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
@media (max-width: 768px) {
    .stats-icon {
        width: 48px !important;
        height: 48px !important;
        font-size: 20px !important;
    }
}
</style>
@endpush
