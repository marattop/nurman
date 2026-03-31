@extends('layouts.warehouse')

@section('page-title', 'Новое списание')
@section('page-subtitle', 'Создание списания товара')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass rounded-[2rem] p-8 border border-white/5 shadow-2xl">
        <form method="POST" action="{{ route('warehouse.writeoff.store') }}" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Причина списания</label>
                <select name="reason" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
                    <option value="">Выберите...</option>
                    <option value="damage">Брак / Повреждение</option>
                    <option value="loss">Потеря</option>
                    <option value="expired">Истёк срок годности</option>
                    <option value="other">Другое</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Комментарий</label>
                <textarea name="comment" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Опишите причину списания..."></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Товары</label>
                <div class="glass rounded-xl border border-white/10 p-4">
                    <p class="text-muted text-sm">Добавление товаров будет реализовано</p>
                </div>
            </div>
            
            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn btn-danger flex-1 py-4 rounded-2xl font-black shadow-glow">
                    <i class="bi bi-trash"></i> СПИСАТЬ ТОВАР
                </button>
                <a href="{{ route('warehouse.writeoff.index') }}" class="btn btn-outline px-8 py-4 rounded-2xl font-black">
                    ОТМЕНА
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
