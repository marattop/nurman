@extends('layouts.warehouse')

@section('page-title', 'Товары')
@section('page-subtitle', 'Управление ассортиментом склада')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div class="glass p-2 rounded-2xl flex items-center gap-2 border-white/5 w-full sm:w-96">
        <i class="bi bi-search text-muted ml-3"></i>
        <input type="text" placeholder="Поиск по названию или артикулу..." class="bg-transparent border-none text-white placeholder-white/20 px-2 py-2 focus:ring-0 text-sm w-full">
    </div>
    <a href="{{ route('warehouse.products.create') }}" class="btn btn-primary w-full sm:w-auto justify-center gap-2">
        <i class="bi bi-plus-lg"></i> Новый товар
    </a>
</div>

<div class="glass rounded-[2rem] overflow-hidden border border-white/5 shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] font-black uppercase tracking-widest text-muted">
                    <th class="px-6 py-5">Товар</th>
                    <th class="px-6 py-5">Артикул</th>
                    <th class="px-6 py-5">Категория</th>
                    <th class="px-6 py-5 text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($products as $product)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td data-label="Товар" class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            @if($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" class="w-12 h-12 rounded-xl object-cover border border-white/10 shadow-lg">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-muted/30 border border-dashed border-white/10">
                                    <i class="bi bi-box"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-bold text-white text-sm group-hover:text-accent transition-colors">{{ $product->name }}</div>
                                <div class="text-[10px] text-muted">{{ $product->brand ?? 'NURMAN' }}</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Артикул" class="px-6 py-4">
                        <span class="font-mono text-xs text-muted bg-white/5 px-2 py-1 rounded-md">{{ $product->article }}</span>
                    </td>
                    <td data-label="Категория" class="px-6 py-4">
                        <span class="text-xs text-text">{{ $product->category?->name ?? '—' }}</span>
                    </td>
                    <td data-label="Действия" class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('warehouse.products.edit', $product) }}" class="w-10 h-10 glass rounded-xl flex items-center justify-center text-muted hover:text-accent hover:border-accent/30 transition-all">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-24 text-center">
                        <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-muted/20 mx-auto mb-4">
                            <i class="bi bi-box text-4xl"></i>
                        </div>
                        <p class="text-muted italic">Товары не найдены</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($products->hasPages())
    <div class="px-6 py-6 border-t border-white/5 bg-white/[0.01]">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
