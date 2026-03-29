@extends('layouts.owner')

@section('page-title', 'Аналитические отчёты')
@section('page-subtitle', 'Выберите раздел для детального анализа показателей')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Sales Report -->
    <a href="{{ route('owner.reports.sales') }}" class="glass p-6 rounded-3xl group hover:border-accent/30 transition-all duration-300 relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-sky-500/10 flex items-center justify-center text-sky-500 text-2xl group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-graph-up"></i>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1">Продажи</p>
                <h3 class="text-lg font-bold text-white group-hover:text-accent transition-colors">Отчёт по продажам</h3>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-between relative z-10">
            <span class="text-[11px] text-muted">Детализация транзакций</span>
            <i class="bi bi-arrow-right text-muted group-hover:text-accent group-hover:translate-x-1 transition-all"></i>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.02] group-hover:opacity-[0.05] transition-opacity">
            <i class="bi bi-graph-up text-8xl"></i>
        </div>
    </a>

    <!-- Products Report -->
    <a href="{{ route('owner.reports.products') }}" class="glass p-6 rounded-3xl group hover:border-accent/30 transition-all duration-300 relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500 text-2xl group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1">Товары</p>
                <h3 class="text-lg font-bold text-white group-hover:text-accent transition-colors">Анализ ассортимента</h3>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-between relative z-10">
            <span class="text-[11px] text-muted">Топ позиций и остатки</span>
            <i class="bi bi-arrow-right text-muted group-hover:text-accent group-hover:translate-x-1 transition-all"></i>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.02] group-hover:opacity-[0.05] transition-opacity">
            <i class="bi bi-box-seam text-8xl"></i>
        </div>
    </a>

    <!-- Finance Report -->
    <a href="{{ route('owner.finance.profit') }}" class="glass p-6 rounded-3xl group hover:border-accent/30 transition-all duration-300 relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-2xl group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1">Финансы</p>
                <h3 class="text-lg font-bold text-white group-hover:text-accent transition-colors">Прибыль и убытки</h3>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-between relative z-10">
            <span class="text-[11px] text-muted">Полный фин. анализ</span>
            <i class="bi bi-arrow-right text-muted group-hover:text-accent group-hover:translate-x-1 transition-all"></i>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.02] group-hover:opacity-[0.05] transition-opacity">
            <i class="bi bi-cash-stack text-8xl"></i>
        </div>
    </a>

    <!-- Expenses Report -->
    <a href="{{ route('owner.reports.expenses') }}" class="glass p-6 rounded-3xl group hover:border-accent/30 transition-all duration-300 relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 text-2xl group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1">Расходы</p>
                <h3 class="text-lg font-bold text-white group-hover:text-accent transition-colors">Анализ трат</h3>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-between relative z-10">
            <span class="text-[11px] text-muted">Расходы по категориям</span>
            <i class="bi bi-arrow-right text-muted group-hover:text-accent group-hover:translate-x-1 transition-all"></i>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.02] group-hover:opacity-[0.05] transition-opacity">
            <i class="bi bi-wallet2 text-8xl"></i>
        </div>
    </a>

    <!-- Purchases Report -->
    <a href="{{ route('owner.reports.purchases') }}" class="glass p-6 rounded-3xl group hover:border-accent/30 transition-all duration-300 relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 text-2xl group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-cart-check"></i>
            </div>
            <div>
                <p class="text-muted text-[10px] font-bold uppercase tracking-widest mb-1">Закупки</p>
                <h3 class="text-lg font-bold text-white group-hover:text-accent transition-colors">История закупок</h3>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-between relative z-10">
            <span class="text-[11px] text-muted">Закуп товаров</span>
            <i class="bi bi-arrow-right text-muted group-hover:text-accent group-hover:translate-x-1 transition-all"></i>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.02] group-hover:opacity-[0.05] transition-opacity">
            <i class="bi bi-cart-check text-8xl"></i>
        </div>
    </a>
</div>

@endsection
