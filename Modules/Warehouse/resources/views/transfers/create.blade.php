@extends('layouts.warehouse')

@section('page-title', 'Новое перемещение')
@section('page-subtitle', 'Создание перемещения товаров')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass rounded-[2rem] p-8 border border-white/5 shadow-2xl">
        <form method="POST" action="{{ route('warehouse.transfers.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Откуда</label>
                    <select name="from_shop" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Выберите...</option>
                        <option value="warehouse">Основной склад</option>
                        <!-- Добавить другие магазины -->
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Куда</label>
                    <select name="to_shop" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Выберите...</option>
                        <!-- Добавить магазины -->
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Товары</label>
                <div class="glass rounded-xl border border-white/10 p-4">
                    <p class="text-muted text-sm">Добавление товаров будет реализовано</p>
                </div>
            </div>
            
            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn btn-primary flex-1 py-4 rounded-2xl font-black shadow-glow">
                    <i class="bi bi-check-lg"></i> СОЗДАТЬ ПЕРЕМЕЩЕНИЕ
                </button>
                <a href="{{ route('warehouse.transfers.index') }}" class="btn btn-outline px-8 py-4 rounded-2xl font-black">
                    ОТМЕНА
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
