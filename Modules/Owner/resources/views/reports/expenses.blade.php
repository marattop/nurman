@extends('layouts.owner')

@section('page-title', 'Отчёт по расходам')
@section('page-subtitle', 'Все расходы по категориям')

@section('page-actions')
    <a href="{{ route('owner.reports.index') }}" class="glass px-4 py-2 rounded-xl text-sm font-bold hover:bg-white/10 transition-colors flex items-center gap-2">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
@endsection

@section('content')

<!-- Filter Card -->
<div class="glass p-6 rounded-2xl mb-8">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2">Период</label>
            <select name="period" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Сегодня</option>
                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Неделя</option>
                <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Месяц</option>
                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Год</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2">Категория</label>
            <select name="category" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
                <option value="">Все категории</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                        @if($cat === 'rent') Аренда @elseif($cat === 'utilities') Коммуналка @elseif($cat === 'salary') Зарплата
                        @elseif($cat === 'taxes') Налоги @elseif($cat === 'marketing') Маркетинг @else {{ ucfirst($cat) }} @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2">Магазин</label>
            <select name="shop_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
                <option value="">Все магазины</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-accent hover:bg-accent-hover text-white font-bold py-2.5 rounded-xl transition-all shadow-glow flex items-center justify-center gap-2">
            <i class="bi bi-funnel"></i> Фильтр
        </button>
    </form>
</div>

<!-- Stats Card -->
<div class="glass p-6 rounded-2xl mb-8 flex items-center justify-between overflow-hidden relative">
    <div>
        <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Общие расходы за период</p>
        <h3 class="text-3xl font-extrabold mt-1 text-rose-500">{{ number_format($totalAmount, 0, '.', ' ') }} <span class="text-sm font-normal opacity-60 text-white">сом</span></h3>
    </div>
    <div class="w-16 h-16 rounded-full bg-rose-500/10 flex items-center justify-center text-rose-500 text-3xl border border-rose-500/20">
        <i class="bi bi-receipt-cutoff"></i>
    </div>
    <div class="absolute -right-4 -bottom-4 opacity-[0.03]">
        <i class="bi bi-receipt-cutoff text-7xl"></i>
    </div>
</div>

<!-- Data Table -->
<div class="glass rounded-2xl overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-white/5 bg-white/5">
        <h3 class="text-sm font-bold flex items-center gap-2">
            <i class="bi bi-list-ul text-accent"></i> Детализация расходов
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                    <th class="px-6 py-3 font-bold">№</th>
                    <th class="px-6 py-3 font-bold">Дата</th>
                    <th class="px-6 py-3 font-bold">Категория</th>
                    <th class="px-6 py-3 font-bold">Описание</th>
                    <th class="px-6 py-3 font-bold">Магазин</th>
                    <th class="px-6 py-3 font-bold text-right">Сумма</th>
                    <th class="px-6 py-3 font-bold">Автор</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($expenses as $expense)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="№">
                        <span class="text-[10px] font-mono text-muted">#{{ $expense->id }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-muted" data-label="Дата">{{ $expense->expense_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4" data-label="Категория">
                        @if($expense->category === 'rent') 
                            <span class="px-2 py-0.5 rounded-lg bg-orange-500/10 text-orange-400 text-[10px] font-bold border border-orange-500/20"><i class="bi bi-house-door mr-1"></i> Аренда</span>
                        @elseif($expense->category === 'utilities') 
                            <span class="px-2 py-0.5 rounded-lg bg-sky-500/10 text-sky-400 text-[10px] font-bold border border-sky-500/20"><i class="bi bi-lightning mr-1"></i> Коммуналка</span>
                        @elseif($expense->category === 'salary') 
                            <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20"><i class="bi bi-people mr-1"></i> Зарплата</span>
                        @elseif($expense->category === 'taxes') 
                            <span class="px-2 py-0.5 rounded-lg bg-rose-500/10 text-rose-400 text-[10px] font-bold border border-rose-500/20"><i class="bi bi-bank mr-1"></i> Налоги</span>
                        @elseif($expense->category === 'marketing') 
                            <span class="px-2 py-0.5 rounded-lg bg-accent/10 text-accent text-[10px] font-bold border border-accent/20"><i class="bi bi-megaphone mr-1"></i> Маркетинг</span>
                        @else 
                            <span class="px-2 py-0.5 rounded-lg bg-white/5 text-muted text-[10px] font-bold border border-white/10">{{ ucfirst($expense->category) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm" data-label="Описание">{{ Str::limit($expense->description, 40) ?? '-' }}</td>
                    <td class="px-6 py-4" data-label="Магазин">
                        <span class="text-sm font-semibold">{{ $expense->shop?->name ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right" data-label="Сумма">
                        <span class="text-sm font-extrabold text-rose-400">{{ number_format($expense->amount, 0, '.', ' ') }} сом</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-muted" data-label="Автор">
                        {{ $expense->user?->full_name ?? $expense->user?->username ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-muted italic">Нет данных о расходах</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
        <div class="px-6 py-4 border-t border-white/5 bg-white/[0.02]">
            {{ $expenses->links() }}
        </div>
    @endif
</div>

@endsection
