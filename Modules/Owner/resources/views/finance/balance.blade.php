@extends('layouts.owner')

@section('page-title', 'Баланс')
@section('page-subtitle', 'Финансовое состояние вашего бизнеса')

@section('content')

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-2xl">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Деньги</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-emerald-500">{{ number_format($cash, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
            <p class="text-[10px] text-muted mt-1 italic text-emerald-500/80">Доступно в кассах</p>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity text-emerald-500">
            <i class="bi bi-wallet2 text-7xl"></i>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-sky-500/10 flex items-center justify-center text-sky-500 text-2xl">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Запасы</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-sky-500">{{ number_format($inventoryValue, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
            <p class="text-[10px] text-muted mt-1 italic text-sky-500/80">Стоимость товаров</p>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity text-sky-500">
            <i class="bi bi-box-seam text-7xl"></i>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 text-2xl">
                <i class="bi bi-credit-card"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Обязательства</p>
            <h3 class="text-2xl font-extrabold mt-1 truncate text-rose-500">{{ number_format($accountsPayable, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60 text-white">сом</span></h3>
            <p class="text-[10px] text-muted mt-1 italic text-rose-500/80">Неоплаченные расходы</p>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity text-rose-500">
            <i class="bi bi-credit-card text-7xl"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Активы -->
    <div class="glass rounded-2xl overflow-hidden border-t-4 border-emerald-500/30">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-pie-chart text-emerald-500"></i> Активы
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center pb-4 border-b border-white/5">
                <span class="text-sm text-muted">Денежные средства</span>
                <span class="text-sm font-bold text-emerald-500">{{ number_format($cash, 0, '.', ' ') }} сом</span>
            </div>
            <div class="flex justify-between items-center pb-4 border-b border-white/5">
                <span class="text-sm text-muted">Товарные запасы</span>
                <span class="text-sm font-bold text-sky-500">{{ number_format($inventoryValue, 0, '.', ' ') }} сом</span>
            </div>
            <div class="flex justify-between items-center bg-emerald-500/10 p-4 rounded-xl border border-emerald-500/20">
                <span class="text-sm font-extrabold">Итого активов</span>
                <span class="text-lg font-black text-emerald-500">{{ number_format($totalAssets, 0, '.', ' ') }} сом</span>
            </div>
        </div>
    </div>

    <!-- Обязательства -->
    <div class="glass rounded-2xl overflow-hidden border-t-4 border-rose-500/30">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-receipt text-rose-500"></i> Обязательства
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center pb-4 border-b border-white/5">
                <span class="text-sm text-muted">Неоплаченные расходы</span>
                <span class="text-sm font-bold text-rose-500">{{ number_format($accountsPayable, 0, '.', ' ') }} сом</span>
            </div>
            <div class="h-10"></div> <!-- Spacer to match layout -->
            <div class="flex justify-between items-center bg-rose-500/10 p-4 rounded-xl border border-rose-500/20">
                <span class="text-sm font-extrabold">Итого обязательств</span>
                <span class="text-lg font-black text-rose-500">{{ number_format($totalLiabilities, 0, '.', ' ') }} сом</span>
            </div>
        </div>
    </div>

    <!-- Капитал -->
    <div class="glass rounded-2xl overflow-hidden border-t-4 border-accent/30">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-piggy-bank text-accent"></i> Капитал
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center pb-4 border-b border-white/5">
                <span class="text-sm text-muted">Собственный капитал</span>
                <span class="text-sm font-bold {{ $equity >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">{{ number_format($equity, 0, '.', ' ') }} сом</span>
            </div>
            <div class="h-10"></div> <!-- Spacer to match layout -->
            <div class="flex justify-between items-center bg-accent/10 p-4 rounded-xl border border-accent/20 shadow-glow">
                <span class="text-sm font-extrabold">Баланс</span>
                <span class="text-lg font-black {{ $equity >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">{{ number_format($equity, 0, '.', ' ') }} сом</span>
            </div>
        </div>
    </div>
</div>

@endsection
