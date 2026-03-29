@extends('layouts.warehouse')

@section('page-title', 'Приёмка товара')
@section('page-subtitle', 'История поступлений')

@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/5">
        <a href="{{ route('warehouse.purchases.create') }}" class="bg-accent hover:bg-accent-hover text-bg px-6 py-3 rounded-xl font-bold inline-flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Оформить приёмку
        </a>
    </div>
    <table class="w-full text-left responsive-table">
        <thead>
            <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-muted">
                <th class="px-6 py-4">№</th>
                <th class="px-6 py-4">Дата</th>
                <th class="px-6 py-4">Товар</th>
                <th class="px-6 py-4">Количество</th>
                <th class="px-6 py-4">Цена</th>
                <th class="px-6 py-4">Статус</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($purchases ?? [] as $purchase)
                <tr class="hover:bg-white/[0.02]">
                    <td class="px-6 py-4" data-label="№">#{{ $purchase->id }}</td>
                    <td class="px-6 py-4" data-label="Дата">{{ $purchase->created_at->format('d.m.Y') }}</td>
                    <td class="px-6 py-4" data-label="Товар">{{ $purchase->variant?->product?->name ?? '—' }}</td>
                    <td class="px-6 py-4" data-label="Количество">{{ $purchase->quantity }} шт</td>
                    <td class="px-6 py-4" data-label="Цена">{{ number_format($purchase->supplier_price, 2) }} сом</td>
                    <td class="px-6 py-4" data-label="Статус"><span class="text-success">✓ Принято</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-20 text-center text-muted">Приёмки не найдены</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-6 border-t border-white/5">{{ $purchases ?? null }}</div>
</div>
@endsection
