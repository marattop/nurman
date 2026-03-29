@extends('layouts.owner')

@section('page-title', 'Отчёт по закупкам')
@section('page-subtitle', 'История закупок товаров')

@section('page-actions')
    <a href="{{ route('owner.reports.index') }}" class="glass px-4 py-2 rounded-xl text-sm font-bold hover:bg-white/10 transition-colors flex items-center gap-2">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
@endsection

@section('content')

<!-- Filter Card -->
<div class="glass p-6 rounded-2xl mb-8">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2">Период</label>
            <select name="period" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Сегодня</option>
                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Неделя</option>
                <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Месяц</option>
                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Год</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2">Магазин</label>
            <select name="shop_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
                <option value="">Все магазины</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-accent hover:bg-accent-hover text-white font-bold py-2.5 rounded-xl transition-all shadow-glow flex items-center justify-center gap-2">
            <i class="bi bi-funnel"></i> Фильтр
        </button>
    </form>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="glass p-6 rounded-2xl flex items-center gap-6">
        <div class="w-14 h-14 rounded-2xl bg-sky-500/10 flex items-center justify-center text-sky-500 text-2xl">
            <i class="bi bi-receipt"></i>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Общая сумма закупок</p>
            <h3 class="text-2xl font-extrabold mt-1">{{ number_format($totalAmount, 0, '.', ' ') }} <span class="text-sm font-normal opacity-60">сом</span></h3>
            <p class="text-[10px] text-muted mt-1"><i class="bi bi-calendar"></i> за период</p>
        </div>
    </div>
    <div class="glass p-6 rounded-2xl flex items-center gap-6">
        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-2xl">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Закуплено единиц</p>
            <h3 class="text-2xl font-extrabold mt-1">{{ number_format($totalQuantity, 0, '.', ' ') }} <span class="text-sm font-normal opacity-60">ед.</span></h3>
            <p class="text-[10px] text-muted mt-1"><i class="bi bi-box"></i> товаров</p>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="glass rounded-2xl overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-white/5 bg-white/5">
        <h3 class="text-sm font-bold flex items-center gap-2">
            <i class="bi bi-list-ul text-accent"></i> Детализация закупок
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                    <th class="px-6 py-3 font-bold">№</th>
                    <th class="px-6 py-3 font-bold">Дата</th>
                    <th class="px-6 py-3 font-bold">Товар</th>
                    <th class="px-6 py-3 font-bold">Магазин</th>
                    <th class="px-6 py-3 font-bold text-center">Кол-во</th>
                    <th class="px-6 py-3 font-bold text-right">Цена</th>
                    <th class="px-6 py-3 font-bold text-right">Сумма</th>
                    <th class="px-6 py-3 font-bold">Принял</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="№">
                        <span class="text-[10px] font-mono text-muted">#{{ $purchase->id }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm" data-label="Дата">{{ $purchase->purchase_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4" data-label="Товар">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold">{{ $purchase->variant?->product?->name ?? '-' }}</span>
                            <span class="text-[10px] text-muted">{{ $purchase->variant?->size ?? '' }} {{ $purchase->variant?->color ?? '' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4" data-label="Магазин">
                        <span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-[10px] font-bold text-muted">{{ $purchase->shop?->name ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center" data-label="Кол-во">
                        <span class="px-2 py-0.5 rounded-lg bg-sky-500/10 text-sky-500 text-[10px] font-bold border border-sky-500/20">{{ $purchase->quantity }} шт</span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-muted" data-label="Цена">{{ number_format($purchase->supplier_price, 2, '.', ' ') }} сом</td>
                    <td class="px-6 py-4 text-right" data-label="Сумма">
                        <span class="text-sm font-bold text-accent">{{ number_format($purchase->total_amount, 0, '.', ' ') }} сом</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-muted" data-label="Принял">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-person text-[10px]"></i>
                            <span>{{ $purchase->receivedBy?->full_name ?? $purchase->receivedBy?->username ?? '-' }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-muted italic">
                        <div class="flex flex-col items-center gap-3">
                            <i class="bi bi-inbox text-4xl opacity-20"></i>
                            <p>Нет данных о закупках</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
        <div class="px-6 py-4 border-t border-white/5 bg-white/[0.02]">
            {{ $purchases->links() }}
        </div>
    @endif
</div>

@endsection
