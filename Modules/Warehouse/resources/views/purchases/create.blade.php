@extends('layouts.warehouse')

@section('page-title', 'Новая приёмка')
@section('page-subtitle', 'Поступление товаров на склад')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass rounded-[2rem] p-8 border border-white/5 shadow-2xl">
        <form method="POST" action="{{ route('warehouse.purchases.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Накладная №</label>
                    <input type="text" name="invoice_number" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Введите номер накладной">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Дата приёмки</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Товар</label>
                <select name="variant_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent" required>
                    <option value="">Выберите товар...</option>
                    @foreach($products ?? [] as $product)
                        <option value="{{ $product->variants->first()?->id ?? '' }}">{{ $product->name }} ({{ $product->variants->first()?->size ?? '' }} {{ $product->variants->first()?->color ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Количество</label>
                    <input type="number" name="quantity" min="1" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent" required>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Закупочная цена</label>
                    <input type="number" name="supplier_price" step="0.01" min="0" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Склад</label>
                    <select name="warehouse_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Выберите склад...</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Магазин</label>
                    <select name="shop_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Выберите магазин...</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Комментарий</label>
                <textarea name="comment" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Дополнительная информация..."></textarea>
            </div>
            
            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn btn-primary flex-1 py-4 rounded-2xl font-black shadow-glow">
                    <i class="bi bi-check-lg"></i> СОЗДАТЬ ПРИЁМКУ
                </button>
                <a href="{{ route('warehouse.purchases.index') }}" class="btn btn-outline px-8 py-4 rounded-2xl font-black">
                    ОТМЕНА
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
