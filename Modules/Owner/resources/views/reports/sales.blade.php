@extends('layouts.owner')

@section('page-title', 'Отчёт по продажам')
@section('page-subtitle', 'Детальный анализ выручки и транзакций')

@section('page-actions')
<div class="flex items-center gap-2">
    <a href="{{ route('owner.reports.index') }}" class="glass px-4 py-2 rounded-xl text-muted hover:text-white transition-all text-sm flex items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Назад</span>
    </a>
</div>
@endsection

@section('content')

<!-- Filters -->
<div class="glass p-5 rounded-2xl mb-8">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2 ml-1">Период</label>
            <select name="period" class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-accent transition-all">
                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Сегодня</option>
                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Неделя</option>
                <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Месяц</option>
                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Год</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2 ml-1">Магазин</label>
            <select name="shop_id" class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-accent transition-all">
                <option value="">Все магазины</option>
                @foreach($shops as $shop)
                <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2 ml-1">Продавец</label>
            <select name="seller_id" class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-accent transition-all">
                <option value="">Все продавцы</option>
                @foreach($sellers as $seller)
                <option value="{{ $seller->id }}" {{ $sellerId == $seller->id ? 'selected' : '' }}>{{ $seller->full_name ?? $seller->username }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-accent text-bg font-bold py-2.5 px-6 rounded-xl text-sm hover:bg-accent-hover transition-all shadow-glow flex items-center justify-center gap-2">
            <i class="bi bi-funnel-fill"></i>
            Применить
        </button>
    </form>
</div>

<!-- Metrics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="glass p-6 rounded-2xl relative overflow-hidden group">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 text-2xl">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Всего продаж</p>
                <h3 class="text-2xl font-extrabold">{{ number_format($totalTransactions, 0, '.', ' ') }}</h3>
            </div>
        </div>
        <p class="text-[11px] text-muted"><i class="bi bi-check2-circle text-success"></i> Успешных транзакций</p>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group border-l-4 border-l-emerald-500">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-2xl">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Общая выручка</p>
                <h3 class="text-2xl font-extrabold text-emerald-500">{{ number_format($totalSales, 0, '.', ' ') }} <span class="text-sm font-normal opacity-60 text-white">сом</span></h3>
            </div>
        </div>
        <p class="text-[11px] text-muted"><i class="bi bi-cash-stack"></i> Сумма всех чеков</p>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-sky-500/10 flex items-center justify-center text-sky-500 text-2xl">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Продано товаров</p>
                <h3 class="text-2xl font-extrabold">{{ number_format($totalItems, 0, '.', ' ') }} <span class="text-sm font-normal opacity-60">ед.</span></h3>
            </div>
        </div>
        <p class="text-[11px] text-muted"><i class="bi bi-box"></i> Количество позиций</p>
    </div>
</div>

<!-- Detailed Table -->
<div class="glass rounded-2xl overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-white/5 bg-white/5">
        <h3 class="text-sm font-bold flex items-center gap-2">
            <i class="bi bi-list-ul text-accent"></i> Детализация транзакций
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                    <th class="px-6 py-4 font-bold">№</th>
                    <th class="px-6 py-4 font-bold">Дата и время</th>
                    <th class="px-6 py-4 font-bold">Магазин</th>
                    <th class="px-6 py-4 font-bold">Продавец</th>
                    <th class="px-6 py-4 font-bold text-center">Товаров</th>
                    <th class="px-6 py-4 font-bold text-right">Сумма</th>
                    <th class="px-6 py-4 font-bold text-center">Оплата</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($sales as $sale)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="ID">
                        <span class="px-2 py-0.5 rounded bg-accent/10 text-accent font-mono text-[10px]">#{{ $sale->id }}</span>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium" data-label="Дата">{{ $sale->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4" data-label="Магазин">
                        <span class="px-2 py-1 rounded bg-white/5 text-[11px] font-medium border border-white/10 italic">
                            {{ $sale->shop?->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm" data-label="Продавец">{{ $sale->user?->full_name ?? $sale->user?->username ?? '-' }}</td>
                    <td class="px-6 py-4 text-center" data-label="Товаров">
                        <span class="px-2 py-1 rounded-lg bg-sky-500/10 text-sky-500 text-[11px] font-bold">
                            {{ $sale->items->sum('quantity') }} шт
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right" data-label="Сумма">
                        <span class="text-sm font-bold text-emerald-500">{{ number_format($sale->total_amount, 0, '.', ' ') }} сом</span>
                    </td>
                    <td class="px-6 py-4 text-center" data-label="Оплата">
                        @if($sale->payment_method === 'cash')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase">
                                <i class="bi bi-cash"></i> Нал
                            </span>
                        @elseif($sale->payment_method === 'card')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-500/10 text-blue-500 text-[10px] font-bold uppercase">
                                <i class="bi bi-credit-card"></i> Карта
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-sky-500/10 text-sky-500 text-[10px] font-bold uppercase">
                                <i class="bi bi-arrow-left-right"></i> Перевод
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-muted text-3xl mb-4">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h4 class="text-lg font-bold">Нет данных</h4>
                            <p class="text-sm text-muted mt-1">За выбранный период продаж не найдено</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->hasPages())
    <div class="px-6 py-4 border-t border-white/5 bg-white/5">
        {{ $sales->links() }}
    </div>
    @endif
</div>

@endsection
