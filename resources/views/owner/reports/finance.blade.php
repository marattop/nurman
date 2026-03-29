@extends('layouts.owner')

@section('page-title', 'Финансовый итог')
@section('page-subtitle', 'Сводный финансовый отчёт за период: ' . $periodLabel)

@section('page-actions')
    <form method="GET" action="{{ route('owner.reports.finance') }}" class="flex items-center gap-2">
        <select name="period" onchange="this.form.submit()" 
            class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer text-sm">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Неделя</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Месяц</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Год</option>
        </select>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Main Financial Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass p-6 rounded-2xl flex items-center justify-between group">
            <div>
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Выручка</p>
                <h3 class="text-2xl font-black text-blue-400 mt-1">{{ number_format($revenue, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-400/10 flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                <i class="bi bi-currency-dollar text-2xl"></i>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl flex items-center justify-between group">
            <div>
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Себестоимость</p>
                <h3 class="text-2xl font-black text-rose-400 mt-1">{{ number_format($costOfGoods, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-400/10 flex items-center justify-center text-rose-400 group-hover:scale-110 transition-transform">
                <i class="bi bi-box-seam text-2xl"></i>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl flex items-center justify-between group">
            <div>
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Валовая прибыль</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($grossProfit, 0, '.', ' ') }} <span class="text-xs font-normal opacity-60">сом</span></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-400/10 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                <i class="bi bi-graph-up-arrow text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <!-- Expenses Breakdown -->
        <div class="glass rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                <h3 class="font-bold text-white flex items-center gap-2 text-sm uppercase tracking-wider">
                    <i class="bi bi-receipt text-rose-400"></i>
                    Расходы по категориям
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left responsive-table">
                    <thead>
                        <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-muted">
                            <th class="px-6 py-3">Категория</th>
                            <th class="px-6 py-3 text-right">Сумма</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($expenses as $expense)
                            <tr class="hover:bg-white/[0.01] transition-colors">
                                <td class="px-6 py-4 text-sm text-white/80 capitalize" data-label="Категория">
                                    {{ $expense->type }}
                                </td>
                                <td class="px-6 py-4 text-right" data-label="Сумма">
                                    <span class="font-bold text-rose-400">{{ number_format($expense->amount, 0, '.', ' ') }} сом</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-10 text-center text-muted italic text-xs">Расходов не зафиксировано</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Final Summary -->
        <div class="space-y-6">
            <div class="glass p-8 rounded-2xl border border-white/10 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <i class="bi bi-piggy-bank text-9xl"></i>
                </div>
                
                <h3 class="text-lg font-bold text-white mb-6 uppercase tracking-widest border-b border-white/10 pb-4">Итоговый баланс</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted">Валовая прибыль</span>
                        <span class="font-bold text-emerald-400">{{ number_format($grossProfit, 0, '.', ' ') }} сом</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted">Общие расходы</span>
                        <span class="font-bold text-rose-400">- {{ number_format($expenses->sum('amount'), 0, '.', ' ') }} сом</span>
                    </div>
                    
                    <div class="pt-6 mt-6 border-t border-white/10">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[10px] font-black text-muted uppercase tracking-[0.2em] mb-1">Чистая прибыль</p>
                                <h2 class="text-4xl font-black {{ $profit >= 0 ? 'text-emerald-400' : 'text-rose-500' }} tracking-tighter">
                                    {{ number_format($profit, 0, '.', ' ') }} <span class="text-xl font-medium">сом</span>
                                </h2>
                            </div>
                            <div class="w-16 h-16 rounded-2xl {{ $profit >= 0 ? 'bg-emerald-400/20 text-emerald-400' : 'bg-rose-500/20 text-rose-500' }} flex items-center justify-center border border-current/20">
                                <i class="bi {{ $profit >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }} text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="glass p-6 rounded-2xl bg-accent/5 border border-accent/20">
                <div class="flex gap-4 items-start">
                    <div class="p-2 bg-accent/20 rounded-lg text-accent">
                        <i class="bi bi-info-circle-fill text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white mb-1">Финансовый совет</h4>
                        <p class="text-xs text-muted leading-relaxed">Данный отчёт автоматически рассчитывает чистую прибыль на основе зафиксированных продаж и операционных расходов за выбранный период.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
