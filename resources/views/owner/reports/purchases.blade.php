@extends('layouts.owner')

@section('page-title', 'Отчёт по закупкам')
@section('page-subtitle', 'История поступлений товаров и затрат')

@section('page-actions')
    <div class="flex flex-col md:flex-row gap-3">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <select name="period" onchange="this.form.submit()" 
                class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Сегодня</option>
                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Неделя</option>
                <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Месяц</option>
                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Год</option>
            </select>
            
            <select name="shop_id" onchange="this.form.submit()" 
                class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                <option value="">Все магазины</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('owner.reports.index') }}" class="btn glass bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-xl flex items-center justify-center gap-2 transition-all border border-white/10">
            <i class="bi bi-arrow-left"></i>
            <span>Назад</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass p-6 rounded-2xl flex items-center justify-between group hover:bg-white/[0.02] transition-colors">
            <div>
                <p class="text-xs font-semibold text-muted uppercase tracking-widest">Общая сумма закупок</p>
                <h3 class="text-3xl font-extrabold text-blue-400 mt-2">{{ number_format($totalAmount, 0, '.', ' ') }} <span class="text-lg font-medium opacity-60">сом</span></h3>
                <p class="text-xs text-muted mt-1 italic">за выбранный период</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-blue-400/10 flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform shadow-lg shadow-blue-400/5">
                <i class="bi bi-receipt text-3xl"></i>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl flex items-center justify-between group hover:bg-white/[0.02] transition-colors">
            <div>
                <p class="text-xs font-semibold text-muted uppercase tracking-widest">Всего закуплено единиц</p>
                <h3 class="text-3xl font-extrabold text-emerald-400 mt-2">{{ number_format($totalQuantity, 0, '.', ' ') }}</h3>
                <p class="text-xs text-muted mt-1 italic">единиц товаров</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-400/10 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform shadow-lg shadow-emerald-400/5">
                <i class="bi bi-box-seam text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Purchases Details Table -->
    <div class="glass rounded-2xl overflow-hidden shadow-2xl shadow-black/20">
        <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h3 class="font-bold text-white flex items-center gap-2 uppercase tracking-tighter text-sm">
                <i class="bi bi-list-ul text-accent"></i>
                Детализация закупок
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.05]">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-muted">№</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-muted">Дата</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-muted">Товар</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-muted">Магазин</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-muted text-center">Кол-во</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-muted text-right">Цена</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-muted text-right">Сумма</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-muted">Принял</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-4" data-label="№">
                                <span class="text-[10px] font-mono text-muted">#{{ $purchase->id }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-white/90" data-label="Дата">
                                {{ $purchase->purchase_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4" data-label="Товар">
                                <div class="font-bold text-white text-sm">{{ $purchase->variant?->product?->name ?? '-' }}</div>
                                <div class="text-[10px] text-muted">{{ $purchase->variant?->size ?? '' }} {{ $purchase->variant?->color ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4" data-label="Магазин">
                                <span class="px-2 py-0.5 rounded-md bg-white/5 text-[10px] text-white/80 border border-white/10 uppercase tracking-tighter">
                                    {{ $purchase->shop?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center" data-label="Кол-во">
                                <span class="px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">
                                    {{ $purchase->quantity }} шт
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-xs text-muted" data-label="Цена">
                                {{ number_format($purchase->supplier_price, 0, '.', ' ') }} сом
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Сумма">
                                <span class="font-extrabold text-blue-400 text-sm tracking-tight">{{ number_format($purchase->total_amount, 0, '.', ' ') }} сом</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-muted italic" data-label="Принял">
                                {{ $purchase->receivedBy?->full_name ?? $purchase->receivedBy?->username ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center text-muted">
                                <div class="flex flex-col items-center">
                                    <i class="bi bi-inbox text-5xl mb-4 opacity-10"></i>
                                    <p class="text-sm font-medium">История закупок пуста за этот период</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($purchases->hasPages())
            <div class="px-6 py-4 border-t border-white/5 bg-white/[0.01]">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
