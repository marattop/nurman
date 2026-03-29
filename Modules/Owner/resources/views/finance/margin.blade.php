@extends('layouts.owner')

@section('page-title', 'Маржинальность')
@section('page-subtitle', 'Анализ эффективности продаж товаров')

@section('page-actions')
    <form method="GET" action="{{ route('owner.finance.margin') }}" class="flex items-center gap-2">
        <select name="period" class="glass px-4 py-2 rounded-xl text-sm font-bold focus:outline-none transition-colors cursor-pointer" onchange="this.form.submit()">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Неделя</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Месяц</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Год</option>
        </select>
    </form>
@endsection

@section('content')

<div class="glass rounded-2xl overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between">
        <h3 class="text-sm font-bold flex items-center gap-2">
            <i class="bi bi-percent text-accent"></i> Маржинальность товаров
        </h3>
        <span class="text-[10px] font-bold text-muted uppercase tracking-widest">{{ $periodLabel }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                    <th class="px-6 py-3 font-bold">Товар</th>
                    <th class="px-6 py-3 font-bold">Артикул</th>
                    <th class="px-6 py-3 font-bold text-right">Закупка</th>
                    <th class="px-6 py-3 font-bold text-right">Продажа</th>
                    <th class="px-6 py-3 font-bold text-center">Продано</th>
                    <th class="px-6 py-3 font-bold text-right">Маржа</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($products as $product)
                @php
                    $margin = $product->avg_retail_price > 0 ? (($product->avg_retail_price - $product->avg_supplier_price) / $product->avg_retail_price) * 100 : 0;
                @endphp
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="Товар">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-white/5 border border-white/10">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-muted">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                            </div>
                            <span class="text-sm font-medium leading-tight">{{ Str::limit($product->name, 40) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4" data-label="Артикул">
                        <span class="px-2 py-0.5 rounded bg-white/5 text-muted text-[10px] font-bold">{{ $product->article }}</span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-muted" data-label="Закупка">
                        {{ number_format($product->avg_supplier_price ?? 0, 0, '.', ' ') }} сом
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-semibold" data-label="Продажа">
                        {{ number_format($product->avg_retail_price ?? 0, 0, '.', ' ') }} сом
                    </td>
                    <td class="px-6 py-4 text-center text-sm" data-label="Продано">
                        <span class="px-2 py-0.5 rounded-lg bg-white/5 text-muted font-bold text-xs">{{ $product->sold_qty }}</span>
                    </td>
                    <td class="px-6 py-4 text-right" data-label="Маржа">
                        @if($margin > 0)
                            <span class="px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-500 text-xs font-black border border-emerald-500/20">{{ number_format($margin, 1) }}%</span>
                        @else
                            <span class="px-2 py-1 rounded-lg bg-rose-500/10 text-rose-500 text-xs font-black border border-rose-500/20">{{ number_format($margin, 1) }}%</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-muted italic">Нет данных о товарах</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div class="px-6 py-4 border-t border-white/5 bg-white/[0.02]">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection
