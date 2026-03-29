@extends('layouts.owner')

@section('page-title', 'Маржинальность')
@section('page-subtitle', 'Анализ прибыльности товаров за ' . $periodLabel)

@section('page-actions')
    <form method="GET" action="{{ route('owner.finance.margin') }}" class="flex items-center gap-2">
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
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-muted text-[10px] uppercase tracking-widest border-b border-white/5">
                        <th class="px-6 py-4 font-bold">Товар</th>
                        <th class="px-6 py-4 font-bold">Артикул</th>
                        <th class="px-6 py-4 font-bold text-right">Закупка</th>
                        <th class="px-6 py-4 font-bold text-right">Продажа</th>
                        <th class="px-6 py-4 font-bold text-center">Продано</th>
                        <th class="px-6 py-4 font-bold text-right">Маржа</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($products as $product)
                    @php
                        $margin = $product->avg_retail_price > 0 ? (($product->avg_retail_price - $product->avg_supplier_price) / $product->avg_retail_price) * 100 : 0;
                    @endphp
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4" data-label="Товар">
                            <div class="flex items-center gap-3">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" class="w-10 h-10 rounded-lg object-cover border border-white/10 shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-text font-semibold text-sm leading-tight">{{ Str::limit($product->name, 35) }}</span>
                                    <span class="text-muted text-[10px] mt-0.5">{{ $product->category?->name ?? 'Без категории' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4" data-label="Артикул">
                            <span class="px-2 py-1 rounded-md bg-accent/10 text-accent text-[10px] font-bold tracking-wider">{{ $product->article }}</span>
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Закупка">
                            <span class="text-muted font-medium">{{ number_format($product->avg_supplier_price ?? 0, 0, '.', ' ') }} <small>сом</small></span>
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Продажа">
                            <span class="text-text font-bold">{{ number_format($product->avg_retail_price ?? 0, 0, '.', ' ') }} <small>сом</small></span>
                        </td>
                        <td class="px-6 py-4 text-center" data-label="Продано">
                            <span class="text-text font-medium">{{ $product->sold_qty }}</span>
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Маржа">
                            @if($margin > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-success/10 text-success">
                                    <i class="bi bi-arrow-up-short mr-0.5 text-lg"></i>{{ number_format($margin, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-danger/10 text-danger">
                                    <i class="bi bi-arrow-down-short mr-0.5 text-lg"></i>{{ number_format($margin, 1) }}%
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-muted">
                            <i class="bi bi-inbox text-5xl block mb-4 opacity-20"></i>
                            <p class="text-sm font-medium tracking-wide">Нет данных о продажах за этот период</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-white/5 bg-white/2">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
