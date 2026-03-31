@extends('layouts.warehouse')

@section('page-title', 'Магазины и Точки')
@section('page-subtitle', 'Список доступных локаций для перемещения и продаж')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach(\Modules\Warehouse\Models\Shop::where('is_active', true)->get() as $shop)
    <div class="glass p-6 rounded-[2rem] border border-white/5 shadow-xl group hover:border-accent/30 transition-all">
        <div class="flex items-start justify-between mb-6">
            <div class="stats-icon bg-accent/10 text-accent group-hover:bg-accent group-hover:text-bg transition-colors">
                <i class="bi bi-shop"></i>
            </div>
            <span class="badge bg-success/10 text-success text-[10px]">АКТИВЕН</span>
        </div>
        
        <h3 class="text-xl font-black text-white mb-2">{{ $shop->name }}</h3>
        <p class="text-muted text-sm mb-6 flex items-start gap-2">
            <i class="bi bi-geo-alt text-accent"></i>
            {{ $shop->address ?? 'Адрес не указан' }}
        </p>

        <div class="space-y-3 pt-6 border-t border-white/5">
            <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-muted">
                <span>Код точки</span>
                <span class="text-white">{{ $shop->code }}</span>
            </div>
            <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-muted">
                <span>Телефон</span>
                <span class="text-white">{{ $shop->phone ?? '—' }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
