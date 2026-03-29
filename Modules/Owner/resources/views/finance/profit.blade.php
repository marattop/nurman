@extends('layouts.owner')

@section('page-title', 'Прибыль')
@section('page-subtitle', 'Краткий финансовый отчёт за период')

@section('page-actions')
    <div class="glass px-4 py-2 rounded-xl text-xs font-bold text-muted uppercase tracking-widest border border-white/5">
        Период: {{ $periodLabel }}
    </div>
@endsection

@section('content')

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-sky-500/10 flex items-center justify-center text-sky-500 text-2xl">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Выручка</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-sky-400">{{ number_format($revenue, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 text-2xl">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Себестоимость</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-orange-400">{{ number_format($costOfGoods, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300 border-l-4 border-l-emerald-500/50">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-2xl">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Вал. прибыль</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-emerald-500">{{ number_format($grossProfit, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 text-2xl">
                <i class="bi bi-credit-card"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Расходы</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-rose-400">{{ number_format($expenses, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent text-2xl">
                <i class="bi bi-truck"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Закупки</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-accent">{{ number_format($purchases, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300 border-l-4 border-l-{{ $netProfit >= 0 ? 'emerald' : 'rose' }}-500/50">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-{{ $netProfit >= 0 ? 'emerald' : 'rose' }}-500/10 flex items-center justify-center text-{{ $netProfit >= 0 ? 'emerald' : 'rose' }}-500 text-2xl">
                <i class="bi bi-piggy-bank"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Чистая прибыль</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-{{ $netProfit >= 0 ? 'emerald' : 'rose' }}-500">{{ number_format(abs($netProfit), 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
        </div>
    </div>
</div>

<!-- Detailed Report -->
<div class="glass rounded-2xl overflow-hidden shadow-glow">
    <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between">
        <h3 class="text-sm font-bold flex items-center gap-2">
            <i class="bi bi-file-earmark-text text-accent"></i> Отчёт за период: {{ $periodLabel }}
        </h3>
    </div>
    <div class="p-6 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-white/5">
            <span class="text-sm text-muted">Выручка</span>
            <span class="text-sm font-bold text-sky-400">+{{ number_format($revenue, 0, '.', ' ') }} сом</span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b border-white/5">
            <span class="text-sm text-muted">Себестоимость товаров</span>
            <span class="text-sm font-bold text-orange-400">-{{ number_format($costOfGoods, 0, '.', ' ') }} сом</span>
        </div>
        <div class="flex justify-between items-center p-3 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
            <span class="text-sm font-bold">Валовая прибыль</span>
            <span class="text-sm font-black text-emerald-500">{{ number_format($grossProfit, 0, '.', ' ') }} сом</span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b border-white/5">
            <span class="text-sm text-muted">Операционные расходы</span>
            <span class="text-sm font-bold text-rose-400">-{{ number_format($expenses, 0, '.', ' ') }} сом</span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b border-white/5">
            <span class="text-sm text-muted">Закупки товаров</span>
            <span class="text-sm font-bold text-accent">-{{ number_format($purchases, 0, '.', ' ') }} сом</span>
        </div>
        
        <div class="flex justify-between items-center p-5 bg-{{ $netProfit >= 0 ? 'emerald' : 'rose' }}-500/10 rounded-2xl border border-{{ $netProfit >= 0 ? 'emerald' : 'rose' }}-500/20 shadow-glow mt-6">
            <span class="text-lg font-black uppercase tracking-wider">Чистая прибыль</span>
            <span class="text-2xl font-black text-{{ $netProfit >= 0 ? 'emerald' : 'rose' }}-500">{{ number_format($netProfit, 0, '.', ' ') }} сом</span>
        </div>
    </div>
</div>

@endsection
