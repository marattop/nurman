@extends('layouts.owner')

@section('page-title', $product->name)
@section('page-subtitle', 'Детальная информация о товаре')

@section('content')

<!-- Основная информация -->
<div class="glass p-6 md:p-8 rounded-3xl mb-8 border border-white/5">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Фото -->
        <div class="flex-shrink-0">
            @if($product->photo)
                <img src="{{ asset('storage/' . $product->photo) }}" 
                    alt="{{ $product->name }}" 
                    class="w-full md:w-48 h-48 rounded-2xl object-cover border border-white/10">
            @else
                <div class="w-full md:w-48 h-48 rounded-2xl bg-gradient-to-br from-accent/20 to-purple-500/20 flex items-center justify-center border border-white/10">
                    <i class="bi bi-image text-6xl text-accent/50"></i>
                </div>
            @endif
        </div>

        <!-- Информация -->
        <div class="flex-1">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-3xl font-black text-white mb-2">{{ $product->name }}</h2>
                    <p class="text-muted text-sm">Артикул: {{ $product->article }}</p>
                </div>
                <a href="{{ route('owner.module.products') }}" 
                    class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-white text-sm font-bold transition-all border border-white/10">
                    <i class="bi bi-arrow-left mr-2"></i> Назад
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/5 rounded-xl p-4">
                    <p class="text-muted text-xs font-bold uppercase tracking-widest mb-1">Категория</p>
                    <p class="text-white font-bold">{{ $product->category->name ?? '-' }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4">
                    <p class="text-muted text-xs font-bold uppercase tracking-widest mb-1">Бренд</p>
                    <p class="text-white font-bold">{{ $product->brand ?? '-' }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4">
                    <p class="text-muted text-xs font-bold uppercase tracking-widest mb-1">Материал</p>
                    <p class="text-white font-bold">{{ $product->material ?? '-' }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4">
                    <p class="text-muted text-xs font-bold uppercase tracking-widest mb-1">Страна</p>
                    <p class="text-white font-bold">{{ $product->country ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Общая статистика по вариантам -->
@php
    $totalAll = array_sum(array_column($variantsData, 'total_stock'));
    $displayAll = array_sum(array_map(function($v) { return array_sum(array_column($v['display_stock'], 'quantity')); }, $variantsData));
    $warehouseAll = array_sum(array_map(function($v) { return array_sum(array_column($v['warehouse_stock'], 'quantity')); }, $variantsData));
@endphp

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="glass p-5 rounded-2xl border border-white/5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-muted text-xs font-bold uppercase tracking-widest">Всего вариантов</p>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                <i class="bi bi-layers text-purple-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-white">{{ count($variantsData) }}</p>
    </div>

    <div class="glass p-5 rounded-2xl border border-white/5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-muted text-xs font-bold uppercase tracking-widest">На витрине</p>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                <i class="bi bi-shop text-emerald-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-emerald-400">{{ number_format($displayAll) }}</p>
    </div>

    <div class="glass p-5 rounded-2xl border border-white/5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-muted text-xs font-bold uppercase tracking-widest">На складе</p>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                <i class="bi bi-building text-amber-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-amber-400">{{ number_format($warehouseAll) }}</p>
    </div>

    <div class="glass p-5 rounded-2xl border border-white/5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-muted text-xs font-bold uppercase tracking-widest">Общий остаток</p>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <i class="bi bi-box-seam text-blue-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-black {{ $totalAll < 10 ? 'text-rose-500' : 'text-white' }}">{{ number_format($totalAll) }}</p>
    </div>
</div>

<!-- Варианты товара -->
<div class="space-y-6">
    <h3 class="text-2xl font-black text-white">Варианты и остатки</h3>

    @forelse($variantsData as $variant)
    <div class="glass rounded-2xl border border-white/5 overflow-hidden {{ $variant['low_stock'] ? 'border-rose-500/30' : '' }}">
        <!-- Заголовок варианта -->
        <div class="bg-white/5 px-6 py-4 border-b border-white/5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center">
                        <i class="bi bi-box text-accent text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-white">
                            {{ $product->name }}
                            @if($variant['size'] || $variant['color'])
                                (
                                @if($variant['size'])Размер: {{ $variant['size'] }}@endif
                                @if($variant['size'] && $variant['color']) / @endif
                                @if($variant['color'])Цвет: {{ $variant['color'] }}@endif
                                )
                            @endif
                        </h4>
                        <p class="text-xs text-muted">SKU: {{ $variant['sku'] ?? '-' }} | {{ $variant['total_stock'] }} шт всего</p>
                    </div>
                </div>

                @if($variant['low_stock'])
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-500 text-sm font-bold animate-pulse">
                        <i class="bi bi-exclamation-triangle-fill"></i> Мало на складе
                    </span>
                @endif
            </div>
        </div>

        <!-- Информация о ценах -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
            <div class="bg-white/5 rounded-xl p-4">
                <p class="text-muted text-xs font-bold uppercase tracking-widest mb-2">Цена закупки</p>
                <p class="text-2xl font-black text-white">{{ number_format($variant['supplier_price'], 2) }} сом</p>
            </div>
            <div class="bg-white/5 rounded-xl p-4">
                <p class="text-muted text-xs font-bold uppercase tracking-widest mb-2">Цена продажи</p>
                <p class="text-2xl font-black text-emerald-400">{{ number_format($variant['retail_price'], 2) }} сом</p>
            </div>
            <div class="bg-white/5 rounded-xl p-4">
                <p class="text-muted text-xs font-bold uppercase tracking-widest mb-2">Прибыль с единицы</p>
                <p class="text-2xl font-black {{ ($variant['retail_price'] - $variant['supplier_price']) >= 0 ? 'text-emerald-400' : 'text-rose-500' }}">
                    {{ number_format($variant['retail_price'] - $variant['supplier_price'], 2) }} сом
                </p>
            </div>
        </div>

        <!-- Остатки по точкам -->
        <div class="px-6 pb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Витрина -->
                <div class="bg-emerald-500/5 rounded-xl p-4 border border-emerald-500/10">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="bi bi-shop text-emerald-500"></i>
                        <h5 class="text-sm font-bold text-emerald-400 uppercase tracking-widest">Витрина</h5>
                    </div>
                    @if(count($variant['display_stock']) > 0)
                        <div class="space-y-2">
                            @foreach($variant['display_stock'] as $stock)
                            <div class="flex items-center justify-between bg-white/5 rounded-lg p-3">
                                <span class="text-sm text-white">{{ $stock['shop_name'] }}</span>
                                <div class="flex items-center gap-2">
                                    @if($stock['reserved'] > 0)
                                        <span class="text-xs text-muted">{{ $stock['reserved'] }} рез.</span>
                                    @endif
                                    <span class="text-lg font-black {{ $stock['low_stock'] ? 'text-rose-500' : 'text-emerald-400' }}">
                                        {{ $stock['quantity'] }} шт
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-sm">Нет на витрине</p>
                    @endif
                </div>

                <!-- Склад -->
                <div class="bg-amber-500/5 rounded-xl p-4 border border-amber-500/10">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="bi bi-building text-amber-500"></i>
                        <h5 class="text-sm font-bold text-amber-400 uppercase tracking-widest">Склад</h5>
                    </div>
                    @if(count($variant['warehouse_stock']) > 0)
                        <div class="space-y-2">
                            @foreach($variant['warehouse_stock'] as $stock)
                            <div class="flex items-center justify-between bg-white/5 rounded-lg p-3">
                                <span class="text-sm text-white">{{ $stock['warehouse_name'] }}</span>
                                <div class="flex items-center gap-2">
                                    @if($stock['reserved'] > 0)
                                        <span class="text-xs text-muted">{{ $stock['reserved'] }} рез.</span>
                                    @endif
                                    <span class="text-lg font-black {{ $stock['low_stock'] ? 'text-rose-500' : 'text-amber-400' }}">
                                        {{ $stock['quantity'] }} шт
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-sm">Нет на складе</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Итоги по варианту -->
        @if($variant['total_stock'] > 0)
        <div class="px-6 pb-6">
            <div class="bg-white/5 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted text-xs font-bold uppercase tracking-widest mb-1">Общая стоимость товара</p>
                        <p class="text-xl font-black text-white">
                            {{ number_format($variant['supplier_price'] * $variant['total_stock'], 0) }} сом
                            <span class="text-sm text-muted ml-2">({{ $variant['total_stock'] }} шт × {{ number_format($variant['supplier_price'], 2) }} сом)</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-muted text-xs font-bold uppercase tracking-widest mb-1">Общая стоимость продажи</p>
                        <p class="text-xl font-black text-emerald-400">
                            {{ number_format($variant['retail_price'] * $variant['total_stock'], 0) }} сом
                            <span class="text-sm text-muted ml-2">({{ $variant['total_stock'] }} шт × {{ number_format($variant['retail_price'], 2) }} сом)</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="glass rounded-2xl border border-white/5 p-12 text-center">
        <div class="flex flex-col items-center gap-4">
            <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
                <i class="bi bi-layers text-4xl text-muted/50"></i>
            </div>
            <p class="text-muted text-lg">Нет вариантов товара</p>
        </div>
    </div>
    @endforelse
</div>

@endsection
