@extends('layouts.warehouse')
@section('page-title', 'Товары')
@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-left responsive-table">
        <thead>
            <tr class="bg-white/[0.03] text-[10px] uppercase text-muted">
                <th>Товар</th>
                <th>Артикул</th>
                <th>Категория</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products ?? [] as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->article }}</td>
                    <td>{{ $p->category?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-6 py-20 text-center text-muted">Товары не найдены</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
