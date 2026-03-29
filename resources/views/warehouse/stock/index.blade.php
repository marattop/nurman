@extends('layouts.warehouse')
@section('page-title', 'Остатки')
@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-left responsive-table">
        <thead>
            <tr class="bg-white/[0.03] text-[10px] uppercase text-muted">
                <th class="px-6 py-4">Товар</th>
                <th class="px-6 py-4">Артикул</th>
                <th class="px-6 py-4 text-right">Количество</th>
                <th class="px-6 py-4">Магазин/Склад</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($stock ?? [] as $item)
                <tr>
                    <td data-label="Товар">{{ $item->variant?->product?->name ?? '—' }}</td>
                    <td data-label="Артикул">{{ $item->variant?->product?->article ?? '—' }}</td>
                    <td data-label="Количество" class="text-right font-bold">{{ $item->quantity }} шт</td>
                    <td data-label="Место">{{ $item->shop?->name ?? $item->warehouse?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-20 text-center text-muted">Остатки не найдены</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
