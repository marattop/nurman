@extends('layouts.owner')

@section('page-title', 'Добавить расход')
@section('page-subtitle', 'Регистрация нового расхода в системе')

@section('page-actions')
    <a href="{{ route('owner.module.reports.expenses') }}" class="glass px-4 py-2 rounded-xl text-sm font-bold hover:bg-white/10 transition-colors flex items-center gap-2">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="glass p-8 rounded-3xl relative overflow-hidden">
        <form action="{{ route('owner.module.expenses.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Category -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-muted uppercase tracking-widest ml-1">Категория расхода <span class="text-accent">*</span></label>
                    <select name="category" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm focus:outline-none focus:border-accent transition-colors @error('category') border-rose-500 @enderror">
                        <option value="" disabled selected>Выберите категорию</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Amount -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-muted uppercase tracking-widest ml-1">Сумма (сом) <span class="text-accent">*</span></label>
                    <div class="relative">
                        <input type="number" name="amount" step="0.01" min="0" required value="{{ old('amount') }}" placeholder="0.00" 
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm focus:outline-none focus:border-accent transition-colors @error('amount') border-rose-500 @enderror">
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 text-muted text-xs">сом</div>
                    </div>
                    @error('amount') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Date -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-muted uppercase tracking-widest ml-1">Дата <span class="text-accent">*</span></label>
                    <input type="date" name="expense_date" required value="{{ old('expense_date', date('Y-m-d')) }}" 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm focus:outline-none focus:border-accent transition-colors @error('expense_date') border-rose-500 @enderror">
                    @error('expense_date') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Shop -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-muted uppercase tracking-widest ml-1">Магазин (опционально)</label>
                    <select name="shop_id" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm focus:outline-none focus:border-accent transition-colors @error('shop_id') border-rose-500 @enderror">
                        <option value="">Общий расход</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                        @endforeach
                    </select>
                    @error('shop_id') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2 mb-8">
                <label class="block text-[10px] font-bold text-muted uppercase tracking-widest ml-1">Описание / Комментарий</label>
                <textarea name="description" rows="3" placeholder="Укажите подробности расхода..." 
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm focus:outline-none focus:border-accent transition-colors @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4">
                <button type="reset" class="px-6 py-3 rounded-2xl text-sm font-bold text-muted hover:bg-white/5 transition-colors">
                    Очистить
                </button>
                <button type="submit" class="bg-accent hover:bg-accent-hover text-white font-bold px-10 py-3.5 rounded-2xl transition-all shadow-glow flex items-center gap-2">
                    <i class="bi bi-check2-circle text-lg"></i> Сохранить расход
                </button>
            </div>
        </form>

        <div class="absolute -right-8 -bottom-8 opacity-[0.02]">
            <i class="bi bi-receipt text-9xl"></i>
        </div>
    </div>
</div>
@endsection
