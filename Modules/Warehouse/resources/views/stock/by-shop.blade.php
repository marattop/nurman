@extends('layouts.warehouse')

@section('page-title', 'Остатки по магазину')
@section('page-subtitle', 'Просмотр остатков товаров по магазинам')

@section('content')
<div class="glass rounded-[2rem] overflow-hidden border border-white/5 shadow-2xl">
    <div class="p-6 border-b border-white/5">
        <a href="{{ route('warehouse.stock.index') }}" class="btn btn-outline btn-sm px-4 py-2 rounded-xl">
            <i class="bi bi-arrow-left"></i> Назад к остаткам
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] font-black uppercase tracking-widest text-muted">
                    <th class="px-6 py-5">Товар</th>
                    <th class="px-6 py-5 text-center">Размер</th>
                    <th class="px-6 py-5 text-center">Цвет</th>
                    <th class="px-6 py-5 text-center">Остаток</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($stock ?? [] as $item)
                <tr class="hover:bg-white/[0.02]">
                    <td class="px-6 py-4">
                        <div class="font-bold text-white text-sm">{{ $item->variant->product->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-white text-sm">{{ $item->variant->size ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-white text-sm">{{ $item->variant->color ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-white font-bold text-lg">{{ $item->quantity }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="text-muted">
                            <i class="bi bi-inbox text-3xl mb-3"></i>
                            <p class="text-sm">Нет данных об остатках</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
