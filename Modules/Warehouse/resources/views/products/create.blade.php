@extends('layouts.warehouse')

@section('page-title', 'Новый товар')
@section('page-subtitle', 'Добавление товара')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass rounded-[2rem] p-8 border border-white/5 shadow-2xl">
        <form method="POST" action="{{ route('warehouse.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Название</label>
                <input type="text" name="name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent" required>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Категория</label>
                    <select name="category_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Выберите...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Артикул</label>
                    <input type="text" name="article" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Описание</label>
                <textarea name="description" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Фото товара</label>
                <input type="file" name="photo" accept="image/*" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent">
            </div>
            
            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn btn-primary flex-1 py-4 rounded-2xl font-black shadow-glow">
                    <i class="bi bi-check-lg"></i> СОЗДАТЬ ТОВАР
                </button>
                <a href="{{ route('warehouse.products.index') }}" class="btn btn-outline px-8 py-4 rounded-2xl font-black">
                    ОТМЕНА
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
