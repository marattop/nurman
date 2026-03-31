@extends('layouts.warehouse')

@section('page-title', 'QR-коды')
@section('page-subtitle', 'Управление и печать этикеток товаров')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div class="flex gap-2 w-full sm:w-auto">
        <a href="{{ route('warehouse.qrcodes.index') }}" class="btn {{ !request('not_printed') ? 'btn-primary' : 'btn-outline' }} py-2 px-4 text-xs">Все</a>
        <a href="{{ route('warehouse.qrcodes.index', ['not_printed' => 1]) }}" class="btn {{ request('not_printed') ? 'btn-primary' : 'btn-outline' }} py-2 px-4 text-xs">Не напечатано</a>
    </div>
    <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="btn btn-primary w-full sm:w-auto px-8 py-3 rounded-2xl gap-2 font-black shadow-glow">
        <i class="bi bi-plus-lg"></i> НОВЫЙ QR
    </button>
</div>

<div class="glass rounded-[2rem] overflow-hidden border border-white/5 shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] font-black uppercase tracking-widest text-muted">
                    <th class="px-6 py-5">Товар</th>
                    <th class="px-6 py-5">Артикул</th>
                    <th class="px-6 py-5 text-center">Вариант</th>
                    <th class="px-6 py-5">Статус</th>
                    <th class="px-6 py-5 text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($qrCodes as $qrCode)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td data-label="Товар" class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($qrCode->variant?->product?->photo)
                                <img src="{{ asset('storage/' . $qrCode->variant->product->photo) }}" class="w-10 h-10 rounded-lg object-cover border border-white/5 shadow-sm">
                            @endif
                            <div class="font-bold text-white text-sm">{{ $qrCode->variant?->product?->name ?? '—' }}</div>
                        </div>
                    </td>
                    <td data-label="Артикул" class="px-6 py-4 font-mono text-xs text-muted">{{ $qrCode->variant?->product?->article ?? '—' }}</td>
                    <td data-label="Вариант" class="px-6 py-4 text-center">
                        <span class="badge bg-white/5 text-text text-[10px]">{{ $qrCode->variant?->size }} / {{ $qrCode->variant?->color }}</span>
                    </td>
                    <td data-label="Статус" class="px-6 py-4">
                        @if($qrCode->is_printed)
                            <span class="text-[10px] font-bold text-success uppercase">Напечатан</span>
                        @else
                            <span class="text-[10px] font-bold text-warning uppercase tracking-tighter">В очереди</span>
                        @endif
                    </td>
                    <td data-label="Действия" class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('warehouse.qrcodes.print', $qrCode) }}" class="w-10 h-10 glass rounded-xl flex items-center justify-center text-muted hover:text-accent hover:border-accent/30 transition-all">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-24 text-center text-muted italic">QR-коды не найдены</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($qrCodes->hasPages())
    <div class="px-6 py-6 border-t border-white/5">
        {{ $qrCodes->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Модальное окно для генерации QR -->
<div id="generateModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-4">
    <div class="glass rounded-[2rem] p-8 w-full max-w-md border border-white/10 shadow-glow">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black text-white">Новый QR-код</h3>
            <button onclick="document.getElementById('generateModal').classList.add('hidden')" class="text-muted hover:text-white">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>

        <form action="{{ route('warehouse.qrcodes.generate') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-muted uppercase tracking-widest mb-2">Вариант товара</label>
                <select name="variant_id" class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-4 text-white focus:outline-none focus:ring-2 focus:ring-accent cursor-pointer" required>
                    <option value="">Выберите товар...</option>
                    @foreach(\Modules\Warehouse\Models\ProductVariant::with('product')->get() as $variant)
                        <option value="{{ $variant->id }}">
                            {{ $variant->product?->name }} - {{ $variant->size }} / {{ $variant->color }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit" class="btn btn-primary py-4 rounded-2xl font-black shadow-glow">СГЕНЕРИРОВАТЬ</button>
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="btn btn-outline py-4 rounded-2xl font-black">ОТМЕНА</button>
            </div>
        </form>
    </div>
</div>
@endsection
