@extends('layouts.owner')

@section('page-title', 'Баланс')
@section('page-subtitle', 'Финансовое состояние компании на текущий момент')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cash -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-[10px] uppercase tracking-widest font-bold mb-1">Деньги</p>
                <h3 class="text-2xl font-extrabold text-success">{{ number_format($cash, 0, '.', ' ') }} <small class="text-xs">сом</small></h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-success/10 flex items-center justify-center text-success">
                <i class="bi bi-wallet2 text-2xl"></i>
            </div>
        </div>

        <!-- Inventory -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-[10px] uppercase tracking-widest font-bold mb-1">Запасы</p>
                <h3 class="text-2xl font-extrabold text-[#38bdf8]">{{ number_format($inventoryValue, 0, '.', ' ') }} <small class="text-xs">сом</small></h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-[#38bdf8]/10 flex items-center justify-center text-[#38bdf8]">
                <i class="bi bi-box-seam text-2xl"></i>
            </div>
        </div>

        <!-- Liabilities -->
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-muted text-[10px] uppercase tracking-widest font-bold mb-1">Обязательства</p>
                <h3 class="text-2xl font-extrabold text-danger">{{ number_format($accountsPayable, 0, '.', ' ') }} <small class="text-xs">сом</small></h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-danger/10 flex items-center justify-center text-danger">
                <i class="bi bi-credit-card text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Assets Card -->
        <div class="glass rounded-2xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-white/5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center text-success">
                    <i class="bi bi-pie-chart text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-text">Активы</h3>
            </div>
            <div class="p-6 space-y-4 flex-1">
                <div class="flex justify-between items-center py-2 border-b border-white/5">
                    <span class="text-muted text-sm font-medium">Денежные средства</span>
                    <span class="font-bold text-success">{{ number_format($cash, 0, '.', ' ') }} <small class="text-[10px]">сом</small></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-white/5">
                    <span class="text-muted text-sm font-medium">Товарные запасы</span>
                    <span class="font-bold text-[#38bdf8]">{{ number_format($inventoryValue, 0, '.', ' ') }} <small class="text-[10px]">сом</small></span>
                </div>
            </div>
            <div class="p-6 bg-success/5 border-t border-white/5">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-text text-sm">Итого активов</span>
                    <span class="font-black text-lg text-success">{{ number_format($totalAssets, 0, '.', ' ') }} <small class="text-xs">сом</small></span>
                </div>
            </div>
        </div>

        <!-- Liabilities Card -->
        <div class="glass rounded-2xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-white/5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-danger/10 flex items-center justify-center text-danger">
                    <i class="bi bi-receipt text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-text">Обязательства</h3>
            </div>
            <div class="p-6 space-y-4 flex-1">
                <div class="flex justify-between items-center py-2 border-b border-white/5">
                    <span class="text-muted text-sm font-medium">Неоплаченные расходы</span>
                    <span class="font-bold text-danger">{{ number_format($accountsPayable, 0, '.', ' ') }} <small class="text-[10px]">сом</small></span>
                </div>
            </div>
            <div class="p-6 bg-danger/5 border-t border-white/5">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-text text-sm">Итого обязательств</span>
                    <span class="font-black text-lg text-danger">{{ number_format($totalLiabilities, 0, '.', ' ') }} <small class="text-xs">сом</small></span>
                </div>
            </div>
        </div>

        <!-- Equity Card -->
        <div class="glass rounded-2xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-white/5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                    <i class="bi bi-piggy-bank text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-text">Капитал</h3>
            </div>
            <div class="p-6 space-y-4 flex-1">
                <div class="flex justify-between items-center py-2 border-b border-white/5">
                    <span class="text-muted text-sm font-medium">Собственный капитал</span>
                    <span class="font-bold {{ $equity >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($equity, 0, '.', ' ') }} <small class="text-[10px]">сом</small></span>
                </div>
            </div>
            <div class="p-6 bg-accent/5 border-t border-white/5">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-text text-sm">Баланс</span>
                    <span class="font-black text-lg {{ $equity >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($equity, 0, '.', ' ') }} <small class="text-xs">сом</small></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
