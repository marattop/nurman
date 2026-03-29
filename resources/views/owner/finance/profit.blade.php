@extends('layouts.owner')

@section('page-title', 'Прибыль')
@section('page-subtitle', 'Анализ прибыльности за период ' . $periodLabel)

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Revenue -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-xs uppercase tracking-wider font-bold mb-1">Выручка</p>
                <h3 class="text-2xl font-bold text-[#38bdf8]">{{ number_format($revenue, 0, '.', ' ') }} сом</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-[#38bdf8]/10 flex items-center justify-center text-[#38bdf8]">
                <i class="bi bi-currency-dollar text-2xl"></i>
            </div>
        </div>

        <!-- Cost -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-xs uppercase tracking-wider font-bold mb-1">Себестоимость</p>
                <h3 class="text-2xl font-bold text-warning">{{ number_format($costOfGoods, 0, '.', ' ') }} сом</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-warning/10 flex items-center justify-center text-warning">
                <i class="bi bi-box-seam text-2xl"></i>
            </div>
        </div>

        <!-- Gross Profit -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-xs uppercase tracking-wider font-bold mb-1">Валовая прибыль</p>
                <h3 class="text-2xl font-bold text-success">{{ number_format($grossProfit, 0, '.', ' ') }} сом</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-success/10 flex items-center justify-center text-success">
                <i class="bi bi-graph-up-arrow text-2xl"></i>
            </div>
        </div>

        <!-- Expenses -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-xs uppercase tracking-wider font-bold mb-1">Расходы</p>
                <h3 class="text-2xl font-bold text-danger">{{ number_format($expenses, 0, '.', ' ') }} сом</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-danger/10 flex items-center justify-center text-danger">
                <i class="bi bi-credit-card text-2xl"></i>
            </div>
        </div>

        <!-- Purchases -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-xs uppercase tracking-wider font-bold mb-1">Закупки</p>
                <h3 class="text-2xl font-bold text-accent">{{ number_format($purchases, 0, '.', ' ') }} сом</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-accent/10 flex items-center justify-center text-accent">
                <i class="bi bi-truck text-2xl"></i>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-xs uppercase tracking-wider font-bold mb-1">Чистая прибыль</p>
                <h3 class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format(abs($netProfit), 0, '.', ' ') }} сом
                </h3>
            </div>
            <div class="w-14 h-14 rounded-xl {{ $netProfit >= 0 ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }} flex items-center justify-center">
                <i class="bi bi-piggy-bank text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Detailed Report -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-white/5 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                <i class="bi bi-file-earmark-text text-lg"></i>
            </div>
            <h3 class="text-lg font-bold text-text">Отчёт за период: {{ $periodLabel }}</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center py-3 border-b border-white/5">
                <span class="text-muted font-medium">Выручка</span>
                <span class="font-bold text-[#38bdf8]">+{{ number_format($revenue, 0, '.', ' ') }} сом</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-white/5">
                <span class="text-muted font-medium">Себестоимость товаров</span>
                <span class="font-bold text-warning">-{{ number_format($costOfGoods, 0, '.', ' ') }} сом</span>
            </div>
            <div class="flex justify-between items-center py-3 px-4 bg-success/5 rounded-xl">
                <span class="text-text font-bold">Валовая прибыль</span>
                <span class="font-bold text-success">{{ number_format($grossProfit, 0, '.', ' ') }} сом</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-white/5">
                <span class="text-muted font-medium">Операционные расходы</span>
                <span class="font-bold text-danger">-{{ number_format($expenses, 0, '.', ' ') }} сом</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-white/5">
                <span class="text-muted font-medium">Закупки товаров</span>
                <span class="font-bold text-accent">-{{ number_format($purchases, 0, '.', ' ') }} сом</span>
            </div>
            
            <div class="mt-8 p-6 bg-accent/10 rounded-2xl flex justify-between items-center">
                <div>
                    <p class="text-muted text-xs uppercase tracking-wider font-bold mb-1">Итоговая чистая прибыль</p>
                    <h4 class="text-2xl font-extrabold text-text">Чистая прибыль</h4>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-black {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($netProfit, 0, '.', ' ') }} <span class="text-lg font-bold">сом</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
