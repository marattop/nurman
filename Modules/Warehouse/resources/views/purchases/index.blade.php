@extends('layouts.warehouse')
@section('page-title', 'Приёмка')
@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/5">
        <a href="{{ route('warehouse.purchases.create') }}" class="bg-accent hover:bg-accent-hover text-bg px-6 py-3 rounded-xl font-bold">+ Оформить приёмку</a>
    </div>
    <table class="w-full text-left responsive-table">
        <thead>
            <tr class="bg-white/[0.03] text-[10px] uppercase text-muted">
                <th>№</th>
                <th>Дата</th>
                <th>Товар</th>
                <th>Кол-во</th>
                <th>Цена</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases ?? [] as $p)
                <tr>
                    <td>#{{ $p->id }}</td>
                    <td>{{ $p->created_at->format('d.m.Y') }}</td>
                    <td>{{ $p->variant?->product?->name ?? '—' }}</td>
                    <td>{{ $p->quantity }} шт</td>
                    <td>{{ number_format($p->supplier_price, 2) }} сом</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-20 text-center text-muted">Приёмки не найдены</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-6">{{ $purchases->links() }}</div>
</div>
@endsection
