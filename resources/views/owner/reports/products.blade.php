@extends('layouts.owner')

@section('page-title', 'Отчёт по товарам')
@section('page-subtitle', 'Анализ популярности и доходности товарных позиций за период: ' . $periodLabel)

@section('page-actions')
    <form method="GET" action="{{ route('owner.reports.products') }}" class="flex items-center gap-2">
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
    <!-- Products Table -->
    <div class="glass rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h3 class="font-bold text-white flex items-center gap-2 text-sm uppercase tracking-wider">
                <i class="bi bi-box-seam text-accent"></i>
                Статистика по товарам
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-muted">
                        <th class="px-6 py-4">Товар</th>
                        <th class="px-6 py-4">Артикул</th>
                        <th class="px-6 py-4 text-center">Вариантов</th>
                        <th class="px-6 py-4 text-center">Продано</th>
                        <th class="px-6 py-4 text-right">Выручка</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($products as $product)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-4" data-label="Товар">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-12 h-12 rounded-xl overflow-hidden bg-white/5 border border-white/10 flex-shrink-0">
                                        @if($product->photo)
                                            <img src="{{ asset('storage/' . $product->photo) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-muted">
                                                <i class="bi bi-image text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-white text-sm truncate max-w-[250px]">{{ $product->name }}</div>
                                        <div class="text-[10px] text-muted truncate">{{ $product->category?->name ?? 'Без категории' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4" data-label="Артикул">
                                <span class="px-2 py-0.5 rounded-md bg-accent/10 text-accent text-[10px] font-mono border border-accent/20">
                                    {{ $product->article }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center" data-label="Вариантов">
                                <span class="text-xs font-bold text-white/80">{{ $product->variants_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center" data-label="Продано">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-white">{{ $product->sold_qty }}</span>
                                    <span class="text-[8px] text-muted uppercase">единиц</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Выручка">
                                <span class="font-bold text-emerald-400 text-sm tracking-tight">{{ number_format($product->revenue, 0, '.', ' ') }} сом</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center text-muted">
                                <div class="flex flex-col items-center">
                                    <i class="bi bi-box-seam text-5xl mb-4 opacity-10"></i>
                                    <p class="text-sm font-medium">Товары не найдены за этот период</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
