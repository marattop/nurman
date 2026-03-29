@extends('layouts.owner')

@section('page-title', 'Аналитика')
@section('page-subtitle', 'Глубокий анализ показателей бизнеса в реальном времени')

@section('page-actions')
    <div class="flex items-center gap-3">
        <select id="periodSelect" class="bg-sidebar border border-border text-text text-sm rounded-xl focus:ring-accent focus:border-accent block w-full p-2.5 transition-all" onchange="loadAnalytics()">
            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Сегодня</option>
            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Неделя</option>
            <option value="month" {{ request('period', 'month') == 'month' ? 'selected' : '' }}>Месяц</option>
            <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Год</option>
        </select>
        <button onclick="loadAnalytics()" class="w-10 h-10 rounded-xl bg-accent text-bg flex items-center justify-center hover:scale-95 transition-transform shadow-glow">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
    </div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Main Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="glass p-6 rounded-[2rem] border-white/5 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-accent/5 rounded-full blur-2xl group-hover:bg-accent/10 transition-colors"></div>
            <div class="flex flex-col h-full">
                <div class="w-12 h-12 rounded-2xl bg-accent/10 flex items-center justify-center text-accent mb-4">
                    <i class="bi bi-currency-dollar text-2xl"></i>
                </div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Выручка</p>
                <h3 class="text-2xl font-black text-white">{{ number_format($revenue, 0, '.', ' ') }} <small class="text-xs font-bold text-muted">сом</small></h3>
                <div class="mt-4 flex items-center gap-2 text-[10px] text-muted">
                    <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5">{{ $periodLabel }}</span>
                </div>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="glass p-6 rounded-[2rem] border-white/5 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-success/5 rounded-full blur-2xl group-hover:bg-success/10 transition-colors"></div>
            <div class="flex flex-col h-full">
                <div class="w-12 h-12 rounded-2xl bg-success/10 flex items-center justify-center text-success mb-4">
                    <i class="bi bi-graph-up-arrow text-2xl"></i>
                </div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Прибыль</p>
                <h3 class="text-2xl font-black text-white">{{ number_format($grossProfit, 0, '.', ' ') }} <small class="text-xs font-bold text-muted">сом</small></h3>
                <div class="mt-4 flex items-center gap-2 text-[10px] text-muted">
                    <span class="px-2 py-0.5 rounded bg-success/5 text-success border border-success/10">Валовая</span>
                </div>
            </div>
        </div>

        <!-- Sales Card -->
        <div class="glass p-6 rounded-[2rem] border-white/5 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#5AC8FA]/5 rounded-full blur-2xl group-hover:bg-[#5AC8FA]/10 transition-colors"></div>
            <div class="flex flex-col h-full">
                <div class="w-12 h-12 rounded-2xl bg-[#5AC8FA]/10 flex items-center justify-center text-[#5AC8FA] mb-4">
                    <i class="bi bi-receipt text-2xl"></i>
                </div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Продаж</p>
                <h3 class="text-3xl font-black text-white">{{ $salesCount }}</h3>
                <div class="mt-4 flex items-center gap-2 text-[10px] text-muted">
                    <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5">Чеков закрыто</span>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="glass p-6 rounded-[2rem] border-white/5 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-danger/5 rounded-full blur-2xl group-hover:bg-danger/10 transition-colors"></div>
            <div class="flex flex-col h-full">
                <div class="w-12 h-12 rounded-2xl bg-danger/10 flex items-center justify-center text-danger mb-4">
                    <i class="bi bi-people text-2xl"></i>
                </div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Клиентов</p>
                <h3 class="text-3xl font-black text-white">{{ $customersCount }}</h3>
                <div class="mt-4 flex items-center gap-2 text-[10px] text-muted">
                    <span class="px-2 py-0.5 rounded bg-danger/5 text-danger border border-danger/10">Активных</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Insights Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Products -->
        <div class="glass rounded-[2.5rem] overflow-hidden border-white/5 flex flex-col">
            <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center text-accent">
                        <i class="bi bi-trophy text-xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-white">Топ товаров</h3>
                </div>
                <span class="text-[10px] font-bold text-muted uppercase tracking-widest">По выручке</span>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left responsive-table">
                    <thead>
                        <tr class="text-muted text-[10px] uppercase tracking-widest border-b border-white/5">
                            <th class="px-8 py-4 font-bold">Наименование</th>
                            <th class="px-8 py-4 font-bold text-center">Продано</th>
                            <th class="px-8 py-4 font-bold text-right">Выручка</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($topProducts as $p)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-8 py-5" data-label="Товар">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-white group-hover:text-accent transition-colors">{{ Str::limit($p->name, 35) }}</span>
                                    <span class="text-[10px] text-muted font-mono mt-0.5">{{ $p->article }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center" data-label="Продано">
                                <span class="px-3 py-1 rounded-full bg-white/5 text-white text-xs font-bold border border-white/10">
                                    {{ $p->sold_qty }} <small class="text-[10px] opacity-50">шт</small>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right font-black text-accent" data-label="Сумма">
                                {{ number_format($p->revenue, 0, '.', ' ') }} <small class="text-[10px]">сом</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Shop Performance -->
        <div class="glass rounded-[2.5rem] overflow-hidden border-white/5 flex flex-col">
            <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-success/10 flex items-center justify-center text-success">
                        <i class="bi bi-shop text-xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-white">Эффективность точек</h3>
                </div>
                <span class="text-[10px] font-bold text-muted uppercase tracking-widest">Продажи</span>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left responsive-table">
                    <thead>
                        <tr class="text-muted text-[10px] uppercase tracking-widest border-b border-white/5">
                            <th class="px-8 py-4 font-bold">Магазин</th>
                            <th class="px-8 py-4 font-bold text-center">Чеков</th>
                            <th class="px-8 py-4 font-bold text-right">Итого</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($revenueByShop as $shop)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-8 py-5" data-label="Магазин">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-success/10 flex items-center justify-center text-success text-xs font-bold">
                                        {{ substr($shop->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-white">{{ $shop->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center" data-label="Продаж">
                                <span class="text-white font-black">{{ $shop->count }}</span>
                            </td>
                            <td class="px-8 py-5 text-right font-black text-success" data-label="Сумма">
                                {{ number_format($shop->total, 0, '.', ' ') }} <small class="text-[10px]">сом</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function loadAnalytics() {
    const period = document.getElementById('periodSelect').value;
    window.location.href = '?period=' + period;
}
</script>
@endsection
