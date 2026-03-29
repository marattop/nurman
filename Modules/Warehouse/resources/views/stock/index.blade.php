@extends('layouts.warehouse')
@section('page-title', 'Остатки')
@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-left responsive-table">
        <thead>
            <tr class="bg-white/[0.03] text-[10px] uppercase text-muted">
                <th>Товар</th>
                <th>Артикул</th>
                <th class="text-right">Количество</th>
                <th>Место</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($stock ?? [] as $item)
                <tr>
                    <td>{{ $item->variant?->product?->name ?? '—' }}</td>
                    <td>{{ $item->variant?->product?->article ?? '—' }}</td>
                    <td class="text-right font-bold">{{ $item->quantity }} шт</td>
                    <td>{{ $item->shop?->name ?? $item->warehouse?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-20 text-center text-muted">Остатки не найдены</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-6">{{ $stock->links() }}</div>
</div>
@endsection
