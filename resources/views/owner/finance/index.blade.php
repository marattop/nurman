@extends('layouts.owner')

@section('page-title', 'Финансы')
@section('page-subtitle', 'Обзор доходов и расходов за период')

@section('page-actions')
    <form method="GET" action="{{ route('owner.finance.index') }}" class="flex items-center gap-2">
        <select name="period" class="bg-sidebar border border-border text-text text-sm rounded-xl focus:ring-accent focus:border-accent block w-full p-2.5" onchange="this.form.submit()">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Неделя</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Месяц</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Год</option>
        </select>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Period Label Card -->
    <div class="glass p-6 rounded-2xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent">
                <i class="bi bi-calendar3 text-xl"></i>
            </div>
            <div>
                <p class="text-muted text-xs uppercase tracking-wider font-bold">Выбранный период</p>
                <h3 class="text-xl font-bold text-text">{{ $periodLabel }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Income Table -->
        <div class="glass p-6 rounded-2xl overflow-hidden">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center text-success">
                    <i class="bi bi-graph-up text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-text">Доходы по дням</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left responsive-table">
                    <thead>
                        <tr class="text-muted text-xs uppercase tracking-wider border-b border-white/5">
                            <th class="px-4 py-3 font-semibold">Дата</th>
                            <th class="px-4 py-3 font-semibold text-right">Сумма</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($income as $item)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-text font-medium" data-label="Дата">
                                {{ \Carbon\Carbon::parse($item->date)->format('d.m.Y') }}
                            </td>
                            <td class="px-4 py-4 text-right text-success font-bold" data-label="Сумма">
                                {{ number_format($item->amount, 0, '.', ' ') }} сом
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-12 text-center text-muted">
                                <i class="bi bi-inbox text-4xl block mb-2 opacity-20"></i>
                                Нет данных
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="glass p-6 rounded-2xl overflow-hidden">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-danger/10 flex items-center justify-center text-danger">
                    <i class="bi bi-credit-card text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-text">Расходы по категориям</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left responsive-table">
                    <thead>
                        <tr class="text-muted text-xs uppercase tracking-wider border-b border-white/5">
                            <th class="px-4 py-3 font-semibold">Дата</th>
                            <th class="px-4 py-3 font-semibold">Категория</th>
                            <th class="px-4 py-3 font-semibold text-right">Сумма</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($expenses as $expense)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-muted text-sm" data-label="Дата">
                                {{ \Carbon\Carbon::parse($expense->date)->format('d.m.Y') }}
                            </td>
                            <td class="px-4 py-4 text-text font-medium capitalize" data-label="Категория">
                                {{ $expense->type }}
                            </td>
                            <td class="px-4 py-4 text-right text-danger font-bold" data-label="Сумма">
                                {{ number_format($expense->amount, 0, '.', ' ') }} сом
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center text-muted">
                                <i class="bi bi-inbox text-4xl block mb-2 opacity-20"></i>
                                Нет расходов
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
