@extends('layouts.owner')

@section('page-title', 'Отчёт по продажам')
@section('page-subtitle', 'Анализ выручки за период: ' . $periodLabel)

@section('page-actions')
    <form method="GET" action="{{ route('owner.reports.sales') }}" class="flex items-center gap-2">
        <select name="period" onchange="this.form.submit()" 
            class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Неделя</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Месяц</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Год</option>
        </select>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted uppercase tracking-wider">Всего продаж</p>
                <h3 class="text-3xl font-extrabold text-blue-400 mt-1">{{ $totalTransactions }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-blue-400/10 flex items-center justify-center text-blue-400">
                <i class="bi bi-cart-check-fill text-3xl"></i>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted uppercase tracking-wider">Общая сумма</p>
                <h3 class="text-3xl font-extrabold text-emerald-400 mt-1">{{ number_format($totalSales, 0, '.', ' ') }} <span class="text-lg font-medium opacity-70">сом</span></h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-400/10 flex items-center justify-center text-emerald-400">
                <i class="bi bi-currency-dollar text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
            <h3 class="font-bold text-white flex items-center gap-2">
                <i class="bi bi-list-ul text-accent"></i>
                История транзакций
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.02]">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">ID</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Магазин</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Продавец</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted text-right">Сумма</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Дата</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4" data-label="ID">
                                <span class="text-xs font-mono text-muted">#{{ $sale->id }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-white" data-label="Магазин">
                                {{ $sale->shop?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-muted" data-label="Продавец">
                                {{ $sale->user?->username ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Сумма">
                                <span class="font-bold text-emerald-400">{{ number_format($sale->total_amount, 0, '.', ' ') }} сом</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-muted" data-label="Дата">
                                {{ $sale->created_at->format('d.m.Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-muted">
                                <i class="bi bi-inbox text-4xl mb-4 block opacity-20"></i>
                                <p>За выбранный период продаж не было</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
