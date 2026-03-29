@extends('layouts.owner')

@section('page-title', 'Прибыль и Маржа')
@section('page-subtitle', 'Анализ доходности и эффективности бизнеса')

@section('page-actions')
    <a href="{{ route('owner.reports.index') }}" class="btn glass bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-xl flex items-center justify-center gap-2 transition-all border border-white/10">
        <i class="bi bi-arrow-left"></i>
        <span>Назад</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Key Financial Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue -->
        <div class="glass p-6 rounded-2xl border-t-4 border-blue-500">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <i class="bi bi-currency-dollar text-xl"></i>
                </div>
            </div>
            <p class="text-xs font-bold text-muted uppercase tracking-widest">Выручка</p>
            <h3 class="text-2xl font-black text-white mt-1">{{ number_format($revenue, 0, '.', ' ') }} сом</h3>
            <p class="text-[10px] text-muted mt-2">Все поступления от продаж</p>
        </div>

        <!-- Cost -->
        <div class="glass p-6 rounded-2xl border-t-4 border-rose-500">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-500">
                    <i class="bi bi-box-seam text-xl"></i>
                </div>
            </div>
            <p class="text-xs font-bold text-muted uppercase tracking-widest">Себестоимость</p>
            <h3 class="text-2xl font-black text-white mt-1">{{ number_format($costOfGoods, 0, '.', ' ') }} сом</h3>
            <p class="text-[10px] text-muted mt-2">Затраты на закупку товаров</p>
        </div>

        <!-- Gross Profit -->
        <div class="glass p-6 rounded-2xl border-t-4 border-emerald-500">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i class="bi bi-graph-up-arrow text-xl"></i>
                </div>
                <span class="text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full">{{ number_format($grossMargin, 1) }}%</span>
            </div>
            <p class="text-xs font-bold text-muted uppercase tracking-widest">Валовая прибыль</p>
            <h3 class="text-2xl font-black text-white mt-1">{{ number_format($grossProfit, 0, '.', ' ') }} сом</h3>
            <p class="text-[10px] text-muted mt-2">Маржинальный доход</p>
        </div>

        <!-- Net Profit -->
        <div class="glass p-6 rounded-2xl border-t-4 border-cyan-500">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-500">
                    <i class="bi bi-piggy-bank text-xl"></i>
                </div>
                <span class="text-xs font-bold text-cyan-500 bg-cyan-500/10 px-2 py-0.5 rounded-full">{{ number_format($netMargin, 1) }}%</span>
            </div>
            <p class="text-xs font-bold text-muted uppercase tracking-widest">Чистая прибыль</p>
            <h3 class="text-2xl font-black text-white mt-1">{{ number_format($netProfit, 0, '.', ' ') }} сом</h3>
            <p class="text-[10px] text-muted mt-2">После вычета всех расходов</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expenses Breakdown -->
        <div class="glass rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                <h3 class="font-bold text-white flex items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-rose-500"></i>
                    Расходы по категориям
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left responsive-table">
                    <thead>
                        <tr class="bg-white/[0.02] text-[10px] uppercase tracking-widest text-muted">
                            <th class="px-6 py-3">Категория</th>
                            <th class="px-6 py-3 text-right">Сумма</th>
                            <th class="px-6 py-3 text-center">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @php $totalExp = $expensesByCategory->sum('total') ?: 1; @endphp
                        @foreach($expensesByCategory as $cat)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4" data-label="Категория">
                                    <div class="flex items-center gap-2 text-sm text-white/90">
                                        @if($cat->category === 'rent') <i class="bi bi-house-door text-amber-500"></i> Аренда
                                        @elseif($cat->category === 'utilities') <i class="bi bi-lightning text-cyan-500"></i> Коммуналка
                                        @elseif($cat->category === 'salary') <i class="bi bi-people text-emerald-500"></i> Зарплата
                                        @elseif($cat->category === 'taxes') <i class="bi bi-bank text-rose-500"></i> Налоги
                                        @elseif($cat->category === 'marketing') <i class="bi bi-megaphone text-blue-500"></i> Маркетинг
                                        @else <i class="bi bi-three-dots text-muted"></i> {{ ucfirst($cat->category) }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-white" data-label="Сумма">
                                    {{ number_format($cat->total, 0, '.', ' ') }} сом
                                </td>
                                <td class="px-6 py-4 text-center" data-label="%">
                                    <span class="text-[10px] font-mono text-muted">{{ number_format(($cat->total / $totalExp) * 100, 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Profitable Products -->
        <div class="glass rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                <h3 class="font-bold text-white flex items-center gap-2">
                    <i class="bi bi-trophy-fill text-amber-500"></i>
                    Топ товаров по прибыли
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left responsive-table">
                    <thead>
                        <tr class="bg-white/[0.02] text-[10px] uppercase tracking-widest text-muted">
                            <th class="px-6 py-3">Товар</th>
                            <th class="px-6 py-3 text-center">Продано</th>
                            <th class="px-6 py-3 text-right">Прибыль</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($productsProfit as $p)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4" data-label="Товар">
                                    <div class="font-bold text-white text-sm">{{ Str::limit($p->name, 30) }}</div>
                                    <div class="text-[10px] text-muted font-mono">{{ $p->article }}</div>
                                </td>
                                <td class="px-6 py-4 text-center" data-label="Продано">
                                    <span class="text-xs font-bold text-white/70">{{ $p->sold_qty }} шт</span>
                                </td>
                                <td class="px-6 py-4 text-right" data-label="Прибыль">
                                    <span class="font-black text-emerald-400">+{{ number_format($p->profit, 0, '.', ' ') }} сом</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
