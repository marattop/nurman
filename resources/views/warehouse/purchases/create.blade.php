@extends('layouts.warehouse')

@section('page-title', 'Оформление приёмки')

@section('content')
<div class="glass rounded-2xl p-6 max-w-2xl">
    <form method="POST" action="{{ route('warehouse.purchases.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-bold mb-2">Товар</label>
            <select name="product_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3">
                @foreach($products ?? [] as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold mb-2">Количество</label>
            <input type="number" name="quantity" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3" value="1" min="1">
        </div>
        <div>
            <label class="block text-sm font-bold mb-2">Закупочная цена</label>
            <input type="number" step="0.01" name="supplier_price" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-accent hover:bg-accent-hover text-bg py-3 rounded-xl font-bold">Сохранить</button>
            <a href="{{ route('warehouse.purchases.index') }}" class="px-6 py-3 glass rounded-xl font-bold">Отмена</a>
        </div>
    </form>
</div>
@endsection
