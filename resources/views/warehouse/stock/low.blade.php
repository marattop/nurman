@extends('layouts.warehouse')
@section('page-title', 'Дефицит товаров')
@section('page-subtitle', 'Товары с минимальным остатком')

@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-left responsive-table">
        <thead>
            <tr class="bg-white/[0.03] text-[10px] uppercase text-muted">
                <th class="px-6 py-4">Товар</th>
                <th class="px-6 py-4">Артикул</th>
                <th class="px-6 py-4 text-right">Остаток</th>
                <th class="px-6 py-4 text-right">Мин.</th>
                <th class="px-6 py-4">Статус</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($lowStock ?? [] as $item)
                <tr>
                    <td data-label="Товар">{{ $item->variant?->product?->name ?? '—' }}</td>
                    <td data-label="Артикул">{{ $item->variant?->product?->article ?? '—' }}</td>
                    <td data-label="Остаток" class="text-right font-bold text-danger">{{ $item->quantity }} шт</td>
                    <td data-label="Мин." class="text-right">{{ $item->min_stock }}</td>
                    <td data-label="Статус"><span class="px-2 py-1 bg-danger/20 text-danger text-[10px] font-bold rounded">Дефицит</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-20 text-center text-muted">Дефицита нет</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
