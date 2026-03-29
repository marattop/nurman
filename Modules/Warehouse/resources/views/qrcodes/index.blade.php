@extends('layouts.warehouse')
@section('page-title', 'Печать QR')
@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <!-- Заголовок и фильтры -->
    <div class="p-4 border-b border-white/10 flex flex-wrap gap-3 items-center justify-between">
        <h2 class="text-xl font-bold">QR-коды</h2>

        <div class="flex gap-2">
            <a href="{{ route('warehouse.qrcodes.index') }}" class="btn btn-sm {{ !request('not_printed') ? 'btn-primary' : 'btn-outline' }}">
                Все
            </a>
            <a href="{{ route('warehouse.qrcodes.index', ['not_printed' => 1]) }}" class="btn btn-sm {{ request('not_printed') ? 'btn-primary' : 'btn-outline' }}">
                Не напечатано
            </a>
        </div>
    </div>

    <!-- Таблица -->
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] uppercase text-muted">
                    <th>Товар</th>
                    <th>Артикул</th>
                    <th>Вариант</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th class="text-right">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($qrCodes ?? [] as $qrCode)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                @if($qrCode->variant?->product?->photo)
                                    <img src="{{ asset('storage/' . $qrCode->variant->product->photo) }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                @endif
                                <span class="font-medium">{{ $qrCode->variant?->product?->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td>{{ $qrCode->variant?->product?->article ?? '—' }}</td>
                        <td>
                            @if($qrCode->variant)
                                <span class="text-muted">{{ $qrCode->variant->size }} / {{ $qrCode->variant->color }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($qrCode->is_printed)
                                <span class="px-2 py-1 bg-success/20 text-success text-[10px] font-bold rounded">Напечатан</span>
                            @else
                                <span class="px-2 py-1 bg-warning/20 text-warning text-[10px] font-bold rounded">Не напечатан</span>
                            @endif
                        </td>
                        <td class="text-muted text-sm">{{ $qrCode->created_at->format('d.m.Y H:i') }}</td>
                        <td class="text-right">
                            <div class="flex gap-2 justify-end">
                                <a href="{{ route('warehouse.qrcodes.print', $qrCode) }}" class="btn btn-sm btn-primary" title="Показать QR">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                                <a href="{{ route('warehouse.qrcodes.print', $qrCode) }}" class="btn btn-sm btn-success" title="Напечатать" onclick="window.open('{{ route('warehouse.qrcodes.print', $qrCode) }}', '_blank'); return false;">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-muted">
                            <i class="bi bi-qr-code text-6xl text-muted/30 mb-4 block"></i>
                            QR-кодов нет
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Пагинация -->
    @if($qrCodes?->hasPages())
        <div class="p-4 border-t border-white/10">
            {{ $qrCodes->links() }}
        </div>
    @endif
</div>

<!-- Модальное окно для генерации QR -->
<div id="generateModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="glass rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold mb-4">Сгенерировать QR-код</h3>

        <form action="{{ route('warehouse.qrcodes.generate') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Вариант товара</label>
                <select name="variant_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                    <option value="">Выберите вариант</option>
                    @foreach(\Modules\Warehouse\Models\ProductVariant::with('product')->get() as $variant)
                        <option value="{{ $variant->id }}">
                            {{ $variant->product?->name }} - {{ $variant->size }} / {{ $variant->color }} ({{ $variant->sku }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Сгенерировать</button>
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="btn btn-outline flex-1">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Кнопка генерации - большая и заметная -->
<div class="fixed bottom-24 right-4 md:bottom-8 md:right-8 z-50">
    <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="group relative btn btn-primary rounded-full p-5 shadow-2xl shadow-primary/50 hover:shadow-primary/70 transition-all hover:scale-110 animate-pulse-slow">
        <i class="bi bi-plus-lg text-2xl font-bold"></i>
        <span class="absolute -top-12 left-1/2 -translate-x-1/2 bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
            + QR-код
        </span>
    </button>
</div>

@if(session('success'))
    <div class="fixed bottom-24 right-4 md:bottom-8 md:right-24 glass rounded-xl px-4 py-3 bg-success/20 border border-success/30 animate-bounce">
        <p class="text-success font-medium"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</p>
    </div>
@endif

@if(session('info'))
    <div class="fixed bottom-24 right-4 md:bottom-8 md:right-24 glass rounded-xl px-4 py-3 bg-warning/20 border border-warning/30">
        <p class="text-warning font-medium"><i class="bi bi-info-circle me-2"></i>{{ session('info') }}</p>
    </div>
@endif

<style>
    @keyframes pulse-slow {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .animate-pulse-slow {
        animation: pulse-slow 2s infinite;
    }
</style>
@endsection
