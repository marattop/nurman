@extends('layouts.owner')

@section('page-title', 'Товары')
@section('page-subtitle', 'Управление товарами и остатками')

@section('content')

<!-- Фильтры и поиск -->
<div class="glass p-5 md:p-6 rounded-3xl mb-8 border border-white/5">
    <form method="GET" action="{{ route('owner.module.products') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Поиск -->
            <div>
                <label class="block text-[10px] font-extrabold text-muted mb-2 uppercase tracking-widest ml-1">Поиск</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-accent/50 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Название, артикул, бренд..."
                        class="w-full bg-bg/50 border border-white/10 rounded-2xl py-3 pl-11 pr-4 text-sm font-semibold text-white placeholder:text-muted/50 focus:border-accent/50 focus:ring-0 transition-all outline-none">
                </div>
            </div>

            <!-- Категория -->
            <div>
                <label class="block text-[10px] font-extrabold text-muted mb-2 uppercase tracking-widest ml-1">Категория</label>
                <div class="relative">
                    <i class="bi bi-folder absolute left-4 top-1/2 -translate-y-1/2 text-accent/50 text-sm"></i>
                    <select name="category" 
                        class="w-full bg-bg/50 border border-white/10 rounded-2xl py-3 pl-11 pr-4 text-sm font-semibold text-white focus:border-accent/50 focus:ring-0 transition-all outline-none">
                        <option value="">Все категории</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Фильтр по остаткам -->
            <div>
                <label class="block text-[10px] font-extrabold text-muted mb-2 uppercase tracking-widest ml-1">Остатки</label>
                <div class="relative">
                    <i class="bi bi-box-seam absolute left-4 top-1/2 -translate-y-1/2 text-accent/50 text-sm"></i>
                    <select name="stock_filter" 
                        class="w-full bg-bg/50 border border-white/10 rounded-2xl py-3 pl-11 pr-4 text-sm font-semibold text-white focus:border-accent/50 focus:ring-0 transition-all outline-none">
                        <option value="">Все товары</option>
                        <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>Мало (< 10 шт)</option>
                        <option value="out" {{ request('stock_filter') == 'out' ? 'selected' : '' }}>Нет в наличии</option>
                        <option value="available" {{ request('stock_filter') == 'available' ? 'selected' : '' }}>Есть в наличии</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" 
                class="bg-accent hover:bg-accent-hover text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-accent/20 active:scale-95 flex items-center gap-2">
                <i class="bi bi-funnel-fill"></i> Применить
            </button>
            <a href="{{ route('owner.module.products') }}" 
                class="bg-white/5 hover:bg-white/10 text-white px-4 py-3 rounded-2xl font-bold text-sm transition-all border border-white/10 active:scale-95 flex items-center justify-center">
                <i class="bi bi-arrow-counterclockwise"></i> Сбросить
            </a>
        </div>
    </form>
</div>

<!-- Общая статистика -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="glass p-5 rounded-2xl border border-white/5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-muted text-xs font-bold uppercase tracking-widest">Всего товаров</p>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <i class="bi bi-box text-blue-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-white">{{ $products->total() }}</p>
    </div>

    <div class="glass p-5 rounded-2xl border border-white/5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-muted text-xs font-bold uppercase tracking-widest">На витрине</p>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                <i class="bi bi-shop text-emerald-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-white">{{ number_format($products->sum('display_quantity')) }}</p>
    </div>

    <div class="glass p-5 rounded-2xl border border-white/5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-muted text-xs font-bold uppercase tracking-widest">На складе</p>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                <i class="bi bi-building text-amber-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-white">{{ number_format($products->sum('warehouse_quantity')) }}</p>
    </div>

    <div class="glass p-5 rounded-2xl border border-white/5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-muted text-xs font-bold uppercase tracking-widest">Мало (< 10)</p>
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center">
                <i class="bi bi-exclamation-triangle text-rose-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-rose-500">{{ number_format($products->filter(fn($p) => $p->low_stock)->count()) }}</p>
    </div>
</div>

<!-- Список товаров -->
<div class="glass rounded-3xl border border-white/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-white/5 border-b border-white/5">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] font-extrabold text-muted uppercase tracking-widest">Товар</th>
                    <th class="px-6 py-4 text-left text-[10px] font-extrabold text-muted uppercase tracking-widest">Категория</th>
                    <th class="px-6 py-4 text-center text-[10px] font-extrabold text-muted uppercase tracking-widest">Всего</th>
                    <th class="px-6 py-4 text-center text-[10px] font-extrabold text-muted uppercase tracking-widest">Витрина</th>
                    <th class="px-6 py-4 text-center text-[10px] font-extrabold text-muted uppercase tracking-widest">Склад</th>
                    <th class="px-6 py-4 text-right text-[10px] font-extrabold text-muted uppercase tracking-widest">Закупка</th>
                    <th class="px-6 py-4 text-right text-[10px] font-extrabold text-muted uppercase tracking-widest">Продажа</th>
                    <th class="px-6 py-4 text-right text-[10px] font-extrabold text-muted uppercase tracking-widest">Стоимость</th>
                    <th class="px-6 py-4 text-right text-[10px] font-extrabold text-muted uppercase tracking-widest">Прибыль</th>
                    <th class="px-6 py-4 text-center text-[10px] font-extrabold text-muted uppercase tracking-widest">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($products as $product)
                <tr class="hover:bg-white/5 transition-all {{ $product->low_stock ? 'bg-rose-500/5' : '' }}">
                    <!-- Товар -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-12 h-12 rounded-xl object-cover border border-white/10">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-accent/20 to-purple-500/20 flex items-center justify-center border border-white/10">
                                    <i class="bi bi-image text-accent/50"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-white">{{ $product->name }}</p>
                                <p class="text-xs text-muted">{{ $product->article }}</p>
                                @if($product->low_stock)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-500 text-[10px] font-bold mt-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Мало
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- Категория -->
                    <td class="px-6 py-4">
                        <span class="text-sm text-white">{{ $product->category->name ?? '-' }}</span>
                    </td>

                    <!-- Всего -->
                    <td class="px-6 py-4 text-center">
                        <span class="text-lg font-black {{ $product->total_quantity < 10 ? 'text-rose-500' : 'text-white' }}">
                            {{ number_format($product->total_quantity) }}
                        </span>
                        <span class="text-xs text-muted ml-1">шт</span>
                    </td>

                    <!-- Витрина -->
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold {{ $product->display_quantity < 10 ? 'text-rose-500' : 'text-emerald-400' }}">
                            {{ number_format($product->display_quantity) }}
                        </span>
                    </td>

                    <!-- Склад -->
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold {{ $product->warehouse_quantity < 10 ? 'text-rose-500' : 'text-amber-400' }}">
                            {{ number_format($product->warehouse_quantity) }}
                        </span>
                    </td>

                    <!-- Цена закупки -->
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm text-muted">{{ number_format($product->total_cost, 0) }} сом</span>
                    </td>

                    <!-- Цена продажи -->
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm font-bold text-white">{{ number_format($product->total_value, 0) }} сом</span>
                    </td>

                    <!-- Стоимость -->
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm font-semibold text-white">{{ number_format($product->total_value, 0) }} сом</span>
                    </td>

                    <!-- Прибыль -->
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm font-bold {{ $product->potential_profit >= 0 ? 'text-emerald-400' : 'text-rose-500' }}">
                            {{ number_format($product->potential_profit, 0) }} сом
                        </span>
                    </td>

                    <!-- Действия -->
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('owner.module.products.show', $product->id) }}" 
                                class="w-9 h-9 rounded-xl bg-accent/10 hover:bg-accent/20 text-accent flex items-center justify-center transition-all"
                                title="Просмотр">
                                <i class="bi bi-eye text-sm"></i>
                            </a>
                            <a href="{{ route('owner.module.products.edit', $product->id) }}" 
                                class="w-9 h-9 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 text-blue-500 flex items-center justify-center transition-all"
                                title="Редактировать">
                                <i class="bi bi-pencil text-sm"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
                                <i class="bi bi-box-seam text-4xl text-muted/50"></i>
                            </div>
                            <p class="text-muted text-sm">Товары не найдены</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Пагинация -->
    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-white/5">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection
