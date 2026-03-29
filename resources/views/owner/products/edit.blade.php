@extends('layouts.owner')

@section('page-title', 'Редактировать товар')
@section('page-subtitle', 'Артикул: ' . ($product->article ?? ''))

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div class="glass p-4 rounded-2xl border border-success/30 text-success mb-6 flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="glass p-4 rounded-2xl border border-danger/30 text-danger mb-6">
            <div class="flex items-center gap-3 mb-2">
                <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                <span class="font-bold">Ошибка при сохранении</span>
            </div>
            <ul class="list-disc list-inside text-sm opacity-90 space-y-1 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('owner.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Основная информация --}}
        <div class="glass rounded-[2rem] overflow-hidden border-white/10 shadow-2xl">
            <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="bi bi-info-circle text-accent"></i> Основная информация
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-semibold mb-2 text-white/70 text-sm">Артикул</label>
                        <input type="text" name="article" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent" value="{{ old('article', $product->article) }}" readonly>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-white/70 text-sm">Название <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent" value="{{ old('name', $product->name) }}" required>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-white/70 text-sm">Описание</label>
                    <textarea name="description" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block font-semibold mb-2 text-white/70 text-sm">Категория</label>
                        <select name="category_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent">
                            <option value="">Выберите категорию</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-white/70 text-sm">Бренд</label>
                        <input type="text" name="brand" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent" value="{{ old('brand', $product->brand) }}">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-white/70 text-sm">Цена продажи (сом) <span class="text-danger">*</span></label>
                        <input type="number" name="retail_price" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent" value="{{ old('retail_price', $product->retail_price ?? 0) }}" required min="0" step="0.01">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-semibold mb-2 text-white/70 text-sm">Материал</label>
                        <input type="text" name="material" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent" value="{{ old('material', $product->material) }}">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-white/70 text-sm">Страна</label>
                        <input type="text" name="country" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent" value="{{ old('country', $product->country) }}">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded bg-white/5 border-white/10 text-accent focus:ring-accent">
                    <label for="is_active" class="text-white/70 text-sm font-semibold">Активный товар</label>
                </div>
            </div>
        </div>

        {{-- Фото товара --}}
        <div class="glass rounded-[2rem] overflow-hidden border-white/10 shadow-2xl">
            <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="bi bi-image text-accent"></i> Фотографии
                </h3>
            </div>
            <div class="p-6 space-y-5">
                @if($product->photo)
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('storage/' . $product->photo) }}" class="w-32 h-32 object-cover rounded-xl border border-white/10">
                        <div>
                            <p class="text-white font-semibold mb-1">Основное фото</p>
                            <p class="text-white/50 text-sm">Загрузите новое, чтобы заменить</p>
                        </div>
                    </div>
                @endif
                <div>
                    <label class="block font-semibold mb-2 text-white/70 text-sm">Новое основное фото</label>
                    <input type="file" name="photo" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-accent focus:border-transparent" accept="image/*">
                </div>
            </div>
        </div>

        {{-- Варианты товара --}}
        <div class="glass rounded-[2rem] overflow-hidden border-white/10 shadow-2xl">
            <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="bi bi-layers text-accent"></i> Варианты товара ({{ $product->variants->count() }})
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($product->variants as $variant)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-white font-semibold">{{ $variant->size ?? '—' }} / {{ $variant->color ?? '—' }}</span>
                                <span class="text-white/50 text-sm font-mono">{{ $variant->barcode ?? 'NO BARCODE' }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-white/50">Закупка:</span>
                                    <span class="text-white font-bold ml-2">{{ number_format($variant->supplier_price, 2) }} сом</span>
                                </div>
                                <div>
                                    <span class="text-white/50">Опт:</span>
                                    <span class="text-white font-bold ml-2">{{ $variant->wholesale_price ? number_format($variant->wholesale_price, 2) : '—' }} сом</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-white/50 text-sm mt-4 text-center">
                    <i class="bi bi-info-circle"></i> Для редактирования вариантов используйте админ-панель
                </p>
            </div>
        </div>

        {{-- Кнопки действий --}}
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-accent hover:bg-accent-hover text-bg py-4 rounded-2xl font-black transition-all shadow-lg shadow-accent/20">
                <i class="bi bi-check-lg"></i> Сохранить изменения
            </button>
            <a href="{{ route('owner.scan-product') }}" class="w-16 h-16 glass rounded-2xl flex items-center justify-center text-white/70 hover:text-white transition-colors border-white/10">
                <i class="bi bi-x-lg text-xl"></i>
            </a>
        </div>
    </form>
</div>
@endsection
