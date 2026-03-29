@extends('layouts.owner')

@section('page-title', 'Финансы')
@section('page-subtitle', 'Мониторинг доходов и расходов за период')

@section('page-actions')
    <form method="GET" action="{{ route('owner.finance.index') }}" class="flex items-center gap-2">
        <select name="period" class="glass px-4 py-2 rounded-xl text-sm font-bold focus:outline-none transition-colors cursor-pointer" onchange="this.form.submit()">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Неделя</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Месяц</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Год</option>
        </select>
    </form>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Доходы по дням -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-graph-up text-emerald-500"></i> Доходы по дням
            </h3>
            <span class="text-[10px] font-bold text-muted uppercase tracking-widest">{{ $periodLabel }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                        <th class="px-6 py-3 font-bold">Дата</th>
                        <th class="px-6 py-3 font-bold text-right">Сумма</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($income as $item)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4 text-sm font-semibold" data-label="Дата">
                            {{ \Carbon\Carbon::parse($item->date)->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Сумма">
                            <span class="text-sm font-bold text-emerald-500">{{ number_format($item->amount, 0, '.', ' ') }} сом</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-12 text-center text-muted italic">
                            <div class="flex flex-col items-center gap-2">
                                <i class="bi bi-inbox text-3xl opacity-20"></i>
                                <span>Нет данных о доходах</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Расходы по категориям -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-credit-card text-rose-500"></i> Расходы за период
            </h3>
            <span class="text-[10px] font-bold text-muted uppercase tracking-widest">{{ $periodLabel }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                        <th class="px-6 py-3 font-bold">Дата</th>
                        <th class="px-6 py-3 font-bold">Категория</th>
                        <th class="px-6 py-3 font-bold text-right">Сумма</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4 text-sm text-muted" data-label="Дата">
                            {{ \Carbon\Carbon::parse($expense->date)->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold capitalize" data-label="Категория">
                            {{ $expense->type }}
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Сумма">
                            <span class="text-sm font-bold text-rose-400">{{ number_format($expense->amount, 0, '.', ' ') }} сом</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-muted italic">
                            <div class="flex flex-col items-center gap-2">
                                <i class="bi bi-inbox text-3xl opacity-20"></i>
                                <span>Нет данных о расходах</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
