@extends('layouts.warehouse')

@section('page-title', $shop->name ?? 'Магазин')
@section('page-subtitle', 'Информация о магазине')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('warehouse.shops.index') }}" class="btn btn-outline btn-sm px-4 py-2 rounded-xl">
            <i class="bi bi-arrow-left"></i> Назад к списку
        </a>
    </div>
    
    <div class="glass rounded-[2rem] p-8 border border-white/5 shadow-2xl">
        <div class="flex items-center gap-6 mb-8">
            <div class="w-20 h-20 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                <i class="bi bi-shop text-4xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-white mb-2">{{ $shop->name ?? 'Магазин' }}</h2>
                <p class="text-muted text-sm">{{ $shop->address ?? 'Адрес не указан' }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="glass rounded-xl p-6 border border-white/5">
                <div class="text-xs font-bold text-muted uppercase tracking-widest mb-2">Телефон</div>
                <div class="text-white">{{ $shop->phone ?? 'Не указан' }}</div>
            </div>
            
            <div class="glass rounded-xl p-6 border border-white/5">
                <div class="text-xs font-bold text-muted uppercase tracking-widest mb-2">График работы</div>
                <div class="text-white">{{ $shop->working_hours ?? 'Не указан' }}</div>
            </div>
        </div>
        
        <div class="mt-8">
            <a href="{{ route('warehouse.stock.by-shop', ['shop_id' => $shop->id]) }}" class="btn btn-primary w-full py-4 rounded-2xl font-black shadow-glow">
                <i class="bi bi-box-seam"></i> ПОСМОТРЕТЬ ОСТАТКИ
            </a>
        </div>
    </div>
</div>
@endsection
