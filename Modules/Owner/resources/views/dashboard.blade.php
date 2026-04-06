@extends('layouts.owner')

@section('page-title', 'Дашборд')
@section('page-subtitle', 'Обзор ключевых показателей вашего бизнеса')

@section('content')

<!-- ФИЛЬТРЫ И ПЕРИОД -->
<div class="glass p-5 md:p-6 rounded-3xl mb-8 border border-white/5">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <!-- Календарь -->
        <div class="flex flex-wrap items-end gap-3 md:gap-4">
            <div class="flex-1 min-w-[140px] md:min-w-[180px]">
                <label class="block text-[10px] font-extrabold text-muted mb-2 uppercase tracking-widest ml-1">От</label>
                <div class="relative">
                    <i class="bi bi-calendar-event absolute left-4 top-1/2 -translate-y-1/2 text-accent/50 text-sm"></i>
                    <input type="date" id="date_from" value="{{ $dateFrom }}"
                        class="w-full bg-bg/50 border border-white/10 rounded-2xl py-3 pl-11 pr-4 text-sm font-semibold text-white focus:border-accent/50 focus:ring-0 transition-all outline-none">
                </div>
            </div>
            <div class="flex-1 min-w-[140px] md:min-w-[180px]">
                <label class="block text-[10px] font-extrabold text-muted mb-2 uppercase tracking-widest ml-1">До</label>
                <div class="relative">
                    <i class="bi bi-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-accent/50 text-sm"></i>
                    <input type="date" id="date_to" value="{{ $dateTo }}"
                        class="w-full bg-bg/50 border border-white/10 rounded-2xl py-3 pl-11 pr-4 text-sm font-semibold text-white focus:border-accent/50 focus:ring-0 transition-all outline-none">
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="applyDateFilter()" 
                    class="bg-accent hover:bg-accent-hover text-white h-[46px] px-6 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-accent/20 active:scale-95 flex items-center gap-2">
                    <i class="bi bi-funnel-fill"></i> <span class="hidden sm:inline">Применить</span>
                </button>
                <button onclick="resetDates()" 
                    class="bg-white/5 hover:bg-white/10 text-white h-[46px] px-4 rounded-2xl font-bold text-sm transition-all border border-white/10 active:scale-95 flex items-center justify-center">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>

        <!-- Быстрые кнопки -->
        <div class="flex flex-wrap gap-2 lg:justify-end">
            @php
                $periods = [
                    'today' => 'Сегодня',
                    'yesterday' => 'Вчера',
                    'week' => 'Неделя',
                    'month' => 'Месяц',
                    'last_month' => 'Прошлый месяц',
                    'year' => 'Год'
                ];
            @endphp
            @foreach($periods as $key => $label)
                <button onclick="setPeriod('{{ $key }}')" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all border {{ $period == $key ? 'bg-accent border-accent text-white shadow-lg shadow-accent/20' : 'bg-white/5 border-white/5 text-muted hover:bg-white/10 hover:text-white' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
</div>

<!-- Main Profit Banner -->
<div class="glass p-6 md:p-10 rounded-[2.5rem] mb-8 relative overflow-hidden border-white/5">
    <!-- Gradient background -->
    <div class="absolute inset-0 bg-gradient-to-br from-accent/10 via-transparent to-emerald-500/5 opacity-50"></div>
    
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10 relative z-10">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 rounded-full bg-accent/10 border border-accent/20 text-accent text-[10px] font-bold uppercase tracking-widest">
                    {{ $periodLabel }}
                </span>
            </div>
            <p class="text-muted text-xs font-bold uppercase tracking-widest mb-2">Чистая прибыль</p>
            <div class="flex items-baseline gap-3">
                <h1 class="text-5xl md:text-7xl font-black {{ $netProfit >= 0 ? 'text-white' : 'text-rose-500' }} tracking-tighter">
                    {{ number_format($netProfit, 0, '.', ' ') }}
                </h1>
                <span class="text-2xl font-bold text-muted">сом</span>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 mt-6">
                @if($profitGrowth > 0)
                    <div class="flex items-center text-success font-bold bg-success/10 px-4 py-2 rounded-2xl text-sm border border-success/20">
                        <i class="bi bi-graph-up-arrow mr-2 text-lg"></i> +{{ number_format(abs($profitGrowth), 1) }}%
                    </div>
                @elseif($profitGrowth < 0)
                    <div class="flex items-center text-danger font-bold bg-danger/10 px-4 py-2 rounded-2xl text-sm border border-danger/20">
                        <i class="bi bi-graph-down-arrow mr-2 text-lg"></i> -{{ number_format(abs($profitGrowth), 1) }}%
                    </div>
                @endif
                <div class="bg-white/5 px-4 py-2 rounded-2xl border border-white/5">
                    <span class="text-muted text-[11px] font-medium block">Рентабельность (Net)</span>
                    <span class="text-white font-bold">{{ number_format($netMargin, 1) }}%</span>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-x-8 gap-y-6 lg:min-w-[400px] p-6 md:p-8 bg-white/5 rounded-3xl border border-white/5 backdrop-blur-md">
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1 flex items-center gap-1.5">
                    <i class="bi bi-lightning-charge-fill text-yellow-400"></i> Выручка
                </p>
                <p class="text-2xl font-black">{{ number_format($revenue->total, 0, '.', ' ') }} <span class="text-xs font-medium opacity-40">сом</span></p>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1 flex items-center gap-1.5">
                    <i class="bi bi-credit-card-2-front-fill text-rose-400"></i> Расходы
                </p>
                <p class="text-2xl font-black text-rose-400">{{ number_format($expenses->total, 0, '.', ' ') }} <span class="text-xs font-medium opacity-40">сом</span></p>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1 flex items-center gap-1.5">
                    <i class="bi bi-cart-check-fill text-emerald-400"></i> Продажи
                </p>
                <p class="text-2xl font-black">{{ $revenue->count }} <span class="text-xs font-medium opacity-40">чеков</span></p>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1 flex items-center gap-1.5">
                    <i class="bi bi-bag-check-fill text-orange-400"></i> Ср. чек
                </p>
                <p class="text-2xl font-black text-orange-400">{{ number_format($avgCheck, 0, '.', ' ') }} <span class="text-xs font-medium opacity-40">сом</span></p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 md:gap-6 mb-8">
    <!-- Revenue -->
    <div class="glass p-5 rounded-3xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center text-accent text-xl">
                <i class="bi bi-cash-stack"></i>
            </div>
            @if($revenueGrowth != 0)
                <span class="flex items-center {{ $revenueGrowth > 0 ? 'text-success bg-success/10' : 'text-danger bg-danger/10' }} text-[10px] font-bold px-2 py-1 rounded-lg">
                    <i class="bi bi-arrow-{{ $revenueGrowth > 0 ? 'up' : 'down' }}-short text-sm"></i> {{ number_format(abs($revenueGrowth), 1) }}%
                </span>
            @endif
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Выручка</p>
            <h3 class="text-xl font-extrabold mt-1 truncate tracking-tight">{{ number_format($revenue->total, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
        </div>
    </div>

    <!-- COGS -->
    <div class="glass p-5 rounded-3xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 text-xl">
                <i class="bi bi-box-seam-fill"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Себестоимость</p>
            <h3 class="text-xl font-extrabold mt-1 truncate tracking-tight">{{ number_format($costOfGoods, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
        </div>
    </div>

    <!-- Gross Profit -->
    <div class="glass p-5 rounded-3xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-xl">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <span class="text-emerald-500 text-[10px] font-bold bg-emerald-500/10 px-2 py-1 rounded-lg">{{ number_format($grossMargin, 1) }}%</span>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Вал. прибыль</p>
            <h3 class="text-xl font-extrabold mt-1 truncate tracking-tight text-emerald-500">{{ number_format($grossProfit, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
        </div>
    </div>

    <!-- Expenses -->
    <div class="glass p-5 rounded-3xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 text-xl">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Расходы</p>
            <h3 class="text-xl font-extrabold mt-1 truncate tracking-tight text-rose-400">{{ number_format($expenses->total, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
        </div>
    </div>

    <!-- Inventory -->
    <div class="glass p-5 rounded-3xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 flex items-center justify-center text-sky-500 text-xl">
                <i class="bi bi-archive-fill"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Товарный запас</p>
            <h3 class="text-xl font-extrabold mt-1 truncate tracking-tight text-sky-400">{{ number_format($inventoryValue, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
        </div>
    </div>

    <!-- Purchases -->
    <div class="glass p-5 rounded-3xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 text-xl">
                <i class="bi bi-truck"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Закупки</p>
            <h3 class="text-xl font-extrabold mt-1 truncate tracking-tight text-amber-500">{{ number_format($purchases->total, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
        </div>
    </div>
</div>

<!-- Dynamics Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="glass p-6 rounded-[2rem] border border-white/5">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
                <i class="bi bi-graph-up text-accent"></i> Динамика выручки
            </h3>
        </div>
        <div class="h-[300px] w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <div class="glass p-6 rounded-[2rem] border border-white/5">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
                <i class="bi bi-graph-up-arrow text-emerald-500"></i> Динамика прибыли
            </h3>
        </div>
        <div class="h-[300px] w-full">
            <canvas id="profitChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue By Shop -->
    <div class="glass rounded-[2rem] overflow-hidden border border-white/5">
        <div class="px-6 py-5 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
                <i class="bi bi-shop text-accent"></i> Выручка по магазинам
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-[10px] uppercase tracking-widest text-muted border-b border-white/5">
                        <th class="px-6 py-4 font-extrabold">Магазин</th>
                        <th class="px-6 py-4 font-extrabold text-center">Продаж</th>
                        <th class="px-6 py-4 font-extrabold text-right">Сумма</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($revenueByShop as $shop)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4" data-label="Магазин">
                            <span class="text-sm font-bold text-white group-hover:text-accent transition-colors">{{ $shop->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-center" data-label="Продаж">
                            <span class="px-3 py-1 rounded-lg bg-accent/10 text-accent text-xs font-black">{{ $shop->count }}</span>
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Сумма">
                            <span class="text-sm font-black text-white">{{ number_format($shop->total, 0, '.', ' ') }} <span class="text-[10px] font-normal opacity-40">сом</span></span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-muted text-sm italic font-medium">Нет данных за период</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expenses By Category -->
    <div class="glass rounded-[2rem] overflow-hidden border border-white/5">
        <div class="px-6 py-5 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
                <i class="bi bi-pie-chart-fill text-rose-500"></i> Расходы по категориям
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-[10px] uppercase tracking-widest text-muted border-b border-white/5">
                        <th class="px-6 py-4 font-extrabold">Категория</th>
                        <th class="px-6 py-4 font-extrabold text-right">Сумма</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($expensesByCategory as $expense)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4" data-label="Категория">
                            <span class="text-sm font-bold text-white group-hover:text-rose-400 transition-colors capitalize">{{ $expense->category }}</span>
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Сумма">
                            <span class="text-sm font-black text-rose-400">{{ number_format($expense->total, 0, '.', ' ') }} <span class="text-[10px] font-normal opacity-40 text-muted">сом</span></span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-10 text-center text-muted text-sm italic font-medium">Нет расходов за период</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Sales -->
<div class="glass rounded-[2rem] overflow-hidden border border-white/5 mb-8">
    <div class="px-6 py-5 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
        <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
            <i class="bi bi-clock-history text-accent"></i> Последние продажи
        </h3>
        <a href="{{ route('owner.reports.sales') }}" class="text-[10px] font-black uppercase tracking-widest text-accent hover:text-white transition-colors">Смотреть все <i class="bi bi-arrow-right ml-1"></i></a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-widest text-muted border-b border-white/5">
                    <th class="px-6 py-4 font-extrabold">ID</th>
                    <th class="px-6 py-4 font-extrabold">Магазин</th>
                    <th class="px-6 py-4 font-extrabold text-right">Сумма</th>
                    <th class="px-6 py-4 font-extrabold text-right">Дата</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($recentSales as $sale)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="ID">
                        <span class="px-2 py-1 rounded bg-white/5 text-muted font-mono text-[10px] border border-white/5 group-hover:border-accent/30 group-hover:text-accent transition-all">#{{ $sale->id }}</span>
                    </td>
                    <td class="px-6 py-4" data-label="Магазин">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-white">{{ $sale->shop?->name ?? 'N/A' }}</span>
                            <span class="text-[10px] text-muted">{{ $sale->user?->username ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right" data-label="Сумма">
                        <span class="text-sm font-black text-emerald-500">{{ number_format($sale->total_amount, 0, '.', ' ') }} <span class="text-[10px] font-normal opacity-40">сом</span></span>
                    </td>
                    <td class="px-6 py-4 text-right" data-label="Дата">
                        <span class="text-xs font-medium text-muted">{{ $sale->created_at->format('d.m.Y') }}</span>
                        <span class="text-[10px] text-muted/40 block">{{ $sale->created_at->format('H:i') }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-muted text-sm italic font-medium">Нет продаж за период</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Top Products -->
<div class="glass rounded-[2rem] overflow-hidden border border-white/5">
    <div class="px-6 py-5 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
        <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
            <i class="bi bi-trophy-fill text-amber-500"></i> Топ товаров по прибыли
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-widest text-muted border-b border-white/5">
                    <th class="px-6 py-4 font-extrabold">Товар</th>
                    <th class="px-6 py-4 font-extrabold text-center">Продано</th>
                    <th class="px-6 py-4 font-extrabold text-right">Выручка</th>
                    <th class="px-6 py-4 font-extrabold text-right">Прибыль</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($topProducts as $product)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="Товар">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 bg-white/5 border border-white/10 group-hover:border-accent/30 transition-all">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-muted">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-white leading-tight group-hover:text-accent transition-colors">{{ Str::limit($product->name, 40) }}</span>
                                <span class="text-[10px] text-muted/60 tracking-widest uppercase font-black">{{ $product->article }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center" data-label="Продано">
                        <span class="px-3 py-1 rounded-lg bg-white/5 text-white text-xs font-black">{{ $product->sold_qty }} <span class="text-[9px] font-normal opacity-40 uppercase">шт</span></span>
                    </td>
                    <td class="px-6 py-4 text-right" data-label="Выручка">
                        <span class="text-sm font-bold text-white/80">{{ number_format($product->revenue, 0, '.', ' ') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right" data-label="Прибыль">
                        <span class="text-sm font-black text-emerald-500">{{ number_format($product->profit, 0, '.', ' ') }} <span class="text-[10px] font-normal opacity-40">сом</span></span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-muted text-sm italic font-medium">Нет данных за период</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Данные для графиков
const revenueData = @json($revenueDynamics);
const profitData = @json($profitDynamics);

// Общие настройки для графиков
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
            ticks: { color: '#94a3b8', font: { size: 10, weight: 'bold' } }
        },
        x: {
            grid: { display: false },
            ticks: { color: '#94a3b8', font: { size: 10, weight: 'bold' } }
        }
    },
    elements: {
        line: { tension: 0.4 },
        point: { radius: 0, hoverRadius: 6 }
    }
};

// Инициализация графика выручки
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' });
        }),
        datasets: [{
            label: 'Выручка',
            data: revenueData.map(d => d.revenue),
            borderColor: '#a855f7',
            backgroundColor: 'rgba(168, 85, 247, 0.1)',
            fill: true,
            borderWidth: 3
        }]
    },
    options: chartOptions
});

// Инициализация графика прибыли
const profitCtx = document.getElementById('profitChart').getContext('2d');
new Chart(profitCtx, {
    type: 'line',
    data: {
        labels: profitData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' });
        }),
        datasets: [{
            label: 'Прибыль',
            data: profitData.map(d => d.profit),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            borderWidth: 3
        }]
    },
    options: chartOptions
});

// Применить фильтр по датам
function applyDateFilter() {
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    
    if (!dateFrom || !dateTo) {
        alert('Укажите обе даты!');
        return;
    }
    
    // Перенаправляем с параметрами
    window.location.href = `?date_from=${dateFrom}&date_to=${dateTo}`;
}

// Сбросить даты (показать этот месяц)
function resetDates() {
    window.location.href = '?period=month';
}

// Быстрый выбор периода
function setPeriod(period) {
    // Перенаправляем с параметрами и сбрасываем кеш
    window.location.href = `?period=${period}&_t=${Date.now()}`;
}
</script>
@endpush
