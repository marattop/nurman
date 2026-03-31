@extends('layouts.warehouse')

@section('page-title', 'Складские остатки')
@section('page-subtitle', 'Контроль наличия товаров на складе и в магазине')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div class="flex gap-2 w-full sm:w-auto">
        <a href="{{ route('warehouse.stock.index') }}" class="btn {{ !request('low') ? 'btn-primary' : 'btn-outline' }} py-2 px-4 text-xs">Все</a>
        <a href="{{ route('warehouse.stock.low') }}" class="btn {{ request('low') ? 'btn-danger' : 'btn-outline' }} py-2 px-4 text-xs">Дефицит</a>
    </div>
    <div class="glass p-2 rounded-2xl flex items-center gap-2 border-white/5 w-full sm:w-80">
        <i class="bi bi-filter text-muted ml-3"></i>
        <select class="bg-transparent border-none text-white px-2 py-1 focus:ring-0 text-sm w-full cursor-pointer">
            <option value="">Все локации</option>
            <option value="warehouse">Только Склад</option>
            <option value="shop">Только Магазин</option>
        </select>
    </div>
</div>

<div class="glass rounded-[2rem] overflow-hidden border border-white/5 shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] font-black uppercase tracking-widest text-muted">
                    <th class="px-6 py-5">Товар</th>
                    <th class="px-6 py-5">Артикул</th>
                    <th class="px-6 py-5 text-center">Остаток</th>
                    <th class="px-6 py-5">Локация</th>
                    <th class="px-6 py-5 text-right">Статус</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($stock as $item)
                @php
                    $isLow = $item->quantity <= $item->min_stock;
                @endphp
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td data-label="Товар" class="px-6 py-4">
                        <div class="font-bold text-white text-sm">{{ $item->variant?->product?->name ?? '—' }}</div>
                        <div class="text-[10px] text-muted">{{ $item->variant?->size }} {{ $item->variant?->color }}</div>
                    </td>
                    <td data-label="Артикул" class="px-6 py-4 font-mono text-xs text-muted">{{ $item->variant?->product?->article ?? '—' }}</td>
                    <td data-label="Остаток" class="px-6 py-4 text-center">
                        <span class="text-base font-black {{ $isLow ? 'text-danger' : 'text-white' }}">
                            {{ $item->quantity }} шт
                        </span>
                    </td>
                    <td data-label="Локация" class="px-6 py-4">
                        @if($item->shop_id)
                            <span class="badge bg-accent/10 text-accent text-[10px]">МАГАЗИН</span>
                        @else
                            <span class="badge bg-warning/10 text-warning text-[10px]">СКЛАД</span>
                        @endif
                    </td>
                    <td data-label="Статус" class="px-6 py-4 text-right">
                        @if($isLow)
                            <span class="text-[10px] font-black text-danger uppercase tracking-tighter">Критично</span>
                        @else
                            <span class="text-[10px] font-black text-success uppercase tracking-tighter">Норма</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-24 text-center text-muted italic">Остатки не найдены</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stock->hasPages())
    <div class="px-6 py-6 border-t border-white/5">
        {{ $stock->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
