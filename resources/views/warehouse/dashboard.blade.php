@extends('layouts.warehouse')

@section('page-title', 'Дашборд')
@section('page-subtitle', 'Обзор склада и операций')

@section('content')
<div class="stats-grid">
    <!-- Total Products -->
    <div class="glass p-6 rounded-2xl flex items-center gap-6 group hover:bg-white/[0.02] transition-colors">
        <div class="w-16 h-16 rounded-2xl bg-accent/10 flex items-center justify-center text-accent group-hover:scale-110 transition-transform shadow-lg shadow-accent/5">
            <i class="bi bi-box text-4xl"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-muted uppercase tracking-widest">Всего товаров</p>
            <h3 class="text-4xl font-black text-white mt-1">{{ number_format($totalProducts ?? 0) }}</h3>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="glass p-6 rounded-2xl flex items-center gap-6 group hover:bg-white/[0.02] transition-colors">
        <div class="w-16 h-16 rounded-2xl bg-danger/10 flex items-center justify-center text-danger group-hover:scale-110 transition-transform shadow-lg shadow-danger/5">
            <i class="bi bi-exclamation-triangle text-4xl"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-muted uppercase tracking-widest">Заканчивается</p>
            <h3 class="text-4xl font-black text-danger mt-1">{{ number_format($lowStock ?? 0) }}</h3>
        </div>
    </div>

    <!-- Quick Action: Receive Goods -->
    <a href="{{ route('warehouse.purchases.index') }}" class="glass p-6 rounded-2xl flex items-center gap-6 group hover:bg-white/[0.02] transition-colors no-underline">
        <div class="w-16 h-16 rounded-2xl bg-success/10 flex items-center justify-center text-success group-hover:scale-110 transition-transform shadow-lg shadow-success/5">
            <i class="bi bi-cart-plus text-4xl"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-muted uppercase tracking-widest">Приёмка товара</p>
            <h3 class="text-xl font-black text-success mt-1">Оформить →</h3>
        </div>
    </a>
</div>

<!-- Recent Operations -->
<div class="glass rounded-2xl overflow-hidden shadow-2xl">
    <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
        <h3 class="font-bold text-white flex items-center gap-2 uppercase tracking-tight text-sm">
            <i class="bi bi-clock-history text-accent"></i>
            Последние операции
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-muted">
                    <th class="px-6 py-4">Тип</th>
                    <th class="px-6 py-4">Дата</th>
                    <th class="px-6 py-4">Товар</th>
                    <th class="px-6 py-4 text-right">Количество</th>
                    <th class="px-6 py-4 text-right">Статус</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center text-muted">
                        <div class="flex flex-col items-center">
                            <i class="bi bi-inbox text-5xl mb-4 opacity-10"></i>
                            <p class="text-sm font-medium">Операций пока нет</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
