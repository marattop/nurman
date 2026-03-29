@extends('layouts.warehouse')
@section('page-title', 'Дефицит')
@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-left responsive-table">
        <thead>
            <tr class="bg-white/[0.03] text-[10px] uppercase text-muted">
                <th>Товар</th>
                <th>Артикул</th>
                <th class="text-right">Остаток</th>
                <th>Статус</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stock ?? [] as $item)
                <tr>
                    <td>{{ $item->variant?->product?->name ?? '—' }}</td>
                    <td>{{ $item->variant?->product?->article ?? '—' }}</td>
                    <td class="text-right font-bold text-danger">{{ $item->quantity }} шт</td>
                    <td><span class="px-2 py-1 bg-danger/20 text-danger text-[10px] font-bold rounded">Дефицит</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-20 text-center text-muted">Дефицита нет</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
