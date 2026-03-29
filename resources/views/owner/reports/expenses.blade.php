@extends('layouts.owner')

@section('page-title', 'Отчёт по расходам')
@section('page-subtitle', 'Контроль операционных затрат и издержек')

@section('page-actions')
    <div class="flex flex-col md:flex-row gap-3">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <select name="period" onchange="this.form.submit()" 
                class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Сегодня</option>
                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Неделя</option>
                <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Месяц</option>
                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Год</option>
            </select>

            <select name="category" onchange="this.form.submit()" 
                class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                <option value="">Все категории</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                        @if($cat === 'rent') Аренда @elseif($cat === 'utilities') Коммуналка @elseif($cat === 'salary') Зарплата
                        @elseif($cat === 'taxes') Налоги @elseif($cat === 'marketing') Маркетинг @else {{ ucfirst($cat) }} @endif
                    </option>
                @endforeach
            </select>
            
            <select name="shop_id" onchange="this.form.submit()" 
                class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                <option value="">Все магазины</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('owner.reports.index') }}" class="btn glass bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-xl flex items-center justify-center gap-2 transition-all border border-white/10">
            <i class="bi bi-arrow-left"></i>
            <span>Назад</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Total Expenses Card -->
    <div class="glass p-8 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6 group">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-receipt-cutoff text-4xl"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-muted uppercase tracking-widest">Общие расходы за период</p>
                <h3 class="text-4xl font-black text-rose-500 mt-1">{{ number_format($totalAmount, 0, '.', ' ') }} <span class="text-xl font-medium opacity-60">сом</span></h3>
            </div>
        </div>
        <div class="hidden md:block h-12 w-px bg-white/10"></div>
        <div class="flex gap-4">
            <div class="text-center">
                <p class="text-[10px] text-muted uppercase font-bold">Активных статей</p>
                <p class="text-xl font-bold text-white">{{ count($categories) }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] text-muted uppercase font-bold">Транзакций</p>
                <p class="text-xl font-bold text-white">{{ $expenses->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Expenses Details Table -->
    <div class="glass rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
            <h3 class="font-bold text-white flex items-center gap-2 uppercase tracking-tight text-sm">
                <i class="bi bi-list-check text-rose-500"></i>
                Детализация расходов
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-muted">
                        <th class="px-6 py-4">№</th>
                        <th class="px-6 py-4">Дата</th>
                        <th class="px-6 py-4">Категория</th>
                        <th class="px-6 py-4">Описание</th>
                        <th class="px-6 py-4">Магазин</th>
                        <th class="px-6 py-4 text-right">Сумма</th>
                        <th class="px-6 py-4 text-right">Автор</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4" data-label="№">
                                <span class="text-[10px] font-mono text-muted">#{{ $expense->id }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-white/90" data-label="Дата">
                                {{ $expense->expense_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4" data-label="Категория">
                                @if($expense->category === 'rent') 
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-500 text-[10px] font-bold border border-amber-500/20">Аренда</span>
                                @elseif($expense->category === 'utilities') 
                                    <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-500 text-[10px] font-bold border border-cyan-500/20">Коммуналка</span>
                                @elseif($expense->category === 'salary') 
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold border border-emerald-500/20">Зарплата</span>
                                @elseif($expense->category === 'taxes') 
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-500 text-[10px] font-bold border border-rose-500/20">Налоги</span>
                                @elseif($expense->category === 'marketing') 
                                    <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">Маркетинг</span>
                                @else 
                                    <span class="px-2 py-0.5 rounded-full bg-white/10 text-white/60 text-[10px] font-bold border border-white/20">{{ ucfirst($expense->category) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-muted max-w-[200px] truncate" data-label="Описание">
                                {{ $expense->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4" data-label="Магазин">
                                <span class="text-xs text-white/80">{{ $expense->shop?->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Сумма">
                                <span class="font-black text-rose-500 text-sm">{{ number_format($expense->amount, 0, '.', ' ') }} сом</span>
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Автор">
                                <span class="text-[10px] text-muted italic">{{ $expense->user?->username ?? '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-muted">
                                <div class="flex flex-col items-center">
                                    <i class="bi bi-wallet2 text-5xl mb-4 opacity-10"></i>
                                    <p class="text-sm font-medium">Расходы не найдены за этот период</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($expenses->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
