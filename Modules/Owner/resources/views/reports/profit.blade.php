@extends('layouts.owner')

@section('page-title', 'Отчёт по прибыли')
@section('page-subtitle', 'Анализ прибыли и маржинальности за выбранный период')

@section('content')

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-sky-500/10 flex items-center justify-center text-sky-500 text-2xl mb-4">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Выручка</p>
        <h3 class="text-2xl font-extrabold mt-1 truncate">{{ number_format($revenue, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
        <p class="text-[10px] text-muted mt-1 italic">За период</p>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 text-2xl mb-4">
            <i class="bi bi-box-seam"></i>
        </div>
        <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Себестоимость</p>
        <h3 class="text-2xl font-extrabold mt-1 truncate">{{ number_format($costOfGoods, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
        <p class="text-[10px] text-muted mt-1 italic">Закупки товаров</p>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300 border-l-4 border-l-emerald-500/50">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-2xl mb-4">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Вал. прибыль</p>
        <h3 class="text-2xl font-extrabold mt-1 truncate text-emerald-500">{{ number_format($grossProfit, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
        <p class="text-[10px] text-muted mt-1 font-bold">{{ number_format($grossMargin, 1) }}% маржинальность</p>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300 border-l-4 border-l-accent/50">
        <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent text-2xl mb-4">
            <i class="bi bi-piggy-bank"></i>
        </div>
        <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Чистая прибыль</p>
        <h3 class="text-2xl font-extrabold mt-1 truncate text-accent">{{ number_format($netProfit, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
        <p class="text-[10px] text-muted mt-1 font-bold">{{ number_format($netMargin, 1) }}% рентабельность</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Expenses By Category -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-pie-chart text-rose-500"></i> Расходы по категориям
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                        <th class="px-6 py-3 font-bold">Категория</th>
                        <th class="px-6 py-3 font-bold text-right">Сумма</th>
                        <th class="px-6 py-3 font-bold text-center">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @php $totalExp = $expensesByCategory->sum('total') ?? 1; @endphp
                    @forelse($expensesByCategory as $cat)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4 text-sm font-semibold capitalize flex items-center gap-3" data-label="Категория">
                            @if($cat->category === 'rent') <i class="bi bi-house-door text-orange-400"></i> Аренда
                            @elseif($cat->category === 'utilities') <i class="bi bi-lightning text-sky-400"></i> Коммуналка
                            @elseif($cat->category === 'salary') <i class="bi bi-people text-emerald-400"></i> Зарплата
                            @elseif($cat->category === 'taxes') <i class="bi bi-bank text-rose-400"></i> Налоги
                            @elseif($cat->category === 'marketing') <i class="bi bi-megaphone text-accent"></i> Маркетинг
                            @else <i class="bi bi-three-dots text-muted"></i> {{ ucfirst($cat->category) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-rose-400" data-label="Сумма">{{ number_format($cat->total, 0, '.', ' ') }} сом</td>
                        <td class="px-6 py-4 text-center text-[10px] font-bold text-muted" data-label="%">{{ number_format(($cat->total / ($totalExp ?: 1)) * 100, 1) }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-muted italic">Нет расходов</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-trophy text-amber-500"></i> Топ товаров по прибыли
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                        <th class="px-6 py-3 font-bold">Товар</th>
                        <th class="px-6 py-3 font-bold text-center">Продано</th>
                        <th class="px-6 py-3 font-bold text-right">Прибыль</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($productsProfit as $p)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4" data-label="Товар">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold">{{ Str::limit($p->name, 40) }}</span>
                                <span class="text-[10px] text-muted">{{ $p->article }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm" data-label="Продано">
                            <span class="px-2 py-0.5 rounded-lg bg-white/5 text-muted font-bold text-xs">{{ $p->sold_qty }} шт</span>
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Прибыль">
                            <span class="text-sm font-bold text-emerald-500">+{{ number_format($p->profit, 0, '.', ' ') }} сом</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-muted italic">Нет данных</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
