@extends('layouts.admin')
@section('page-title', 'Товары')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-success/15 flex items-center justify-center text-success text-2xl">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold m-0">Товары</h2>
            <p class="text-sm text-muted m-0">Управление ассортиментом</p>
        </div>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-3">
        <form method="GET" class="flex items-center gap-2 bg-sidebar border border-white/10 rounded-xl px-3 py-2 focus-within:border-accent transition-colors">
            <i class="bi bi-search text-muted"></i>
            <input type="text" name="search" placeholder="Поиск товара..." value="{{ request('search') }}" class="bg-transparent border-none text-text outline-none text-sm w-full sm:w-48">
        </form>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary justify-center shadow-lg shadow-accent/20">
            <i class="bi bi-plus-lg"></i> <span>Добавить</span>
        </a>
    </div>
</div>

{{-- Статистика --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-center">
    <div class="card p-4">
        <div class="text-muted text-[10px] uppercase font-bold mb-1">Всего</div>
        <div class="text-xl font-bold text-text">{{ $stats['total'] }}</div>
    </div>
    <div class="card p-4">
        <div class="text-muted text-[10px] uppercase font-bold mb-1">Активных</div>
        <div class="text-xl font-bold text-success">{{ $stats['active'] }}</div>
    </div>
    <div class="card p-4">
        <div class="text-muted text-[10px] uppercase font-bold mb-1">С QR-кодом</div>
        <div class="text-xl font-bold text-accent">{{ $stats['with_qr'] }}</div>
    </div>
    <div class="card p-4">
        <div class="text-muted text-[10px] uppercase font-bold mb-1">Ценность</div>
        <div class="text-lg font-bold text-warning">{{ number_format($stats['total_value'], 0, '.', ' ') }}</div>
    </div>
</div>

<div class="card !p-0 overflow-hidden border-white/5">
    <div class="px-5 py-4 border-b border-white/5 flex justify-between items-center bg-white/5">
        <h3 class="text-base font-semibold m-0 flex items-center gap-2">
            <i class="bi bi-list-ul text-accent"></i> Список
        </h3>
        <div class="text-xs text-muted">Найдено: {{ $products->total() }}</div>
    </div>

    {{-- Десктопная таблица --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="table w-full border-collapse text-sm">
            <thead>
                <tr>
                    <th class="text-left px-5">Фото</th>
                    <th class="text-left">Название</th>
                    <th>Артикул</th>
                    <th>Цена</th>
                    <th>Остаток</th>
                    <th class="text-right px-5">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="hover:bg-white/[0.02] transition-colors border-b border-white/5 last:border-0">
                    <td class="px-5 py-3">
                        @if($product->photo)
                            <img src="{{ asset('storage/' . $product->photo) }}" class="w-10 h-10 rounded-lg object-cover">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center text-success border border-success/10">
                                <i class="bi bi-box"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="font-bold text-text truncate max-w-[200px]">{{ $product->name }}</div>
                        <div class="text-muted text-[11px]">{{ $product->category?->name ?? 'Без категории' }}</div>
                    </td>
                    <td class="text-muted font-mono text-xs">{{ $product->article }}</td>
                    <td class="text-success font-bold">
                        {{ number_format($product->retail_price ?? 0, 0, '.', ' ') }} сом
                    </td>
                    <td>
                        @php
                            $warehouseQty = $product->variants->flatMap(fn($v) => $v->stock)->whereNotNull('warehouse_id')->sum('quantity');
                            $shopQty = $product->variants->flatMap(fn($v) => $v->stock)->whereNotNull('shop_id')->sum('quantity');
                        @endphp
                        <div class="flex flex-col gap-1.5 py-1">
                            <div class="flex items-center gap-2 text-sm font-semibold {{ $warehouseQty > 0 ? 'text-accent' : 'text-muted opacity-50' }}">
                                <i class="bi bi-box-seam"></i> 
                                <span>Склад: {{ $warehouseQty }} шт</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm font-semibold {{ $shopQty > 0 ? 'text-success' : 'text-muted opacity-50' }}">
                                <i class="bi bi-shop"></i> 
                                <span>Витрина: {{ $shopQty }} шт</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5">
                        <div class="flex justify-end gap-2">
                            @php
                                $hasQr = false;
                                $firstQrCodes = [];
                                foreach($product->variants as $variant) {
                                    if($variant->qrCodes && $variant->qrCodes->count() > 0) {
                                        $hasQr = true;
                                        $firstQrCodes = $variant->qrCodes->toArray();
                                        break;
                                    }
                                }
                            @endphp
                            
                            @if($hasQr)
                                <button type="button" 
                                    onclick='showQrModal(@json($product->id), @json($product->name), @json($firstQrCodes))'
                                    class="w-8 h-8 rounded-lg bg-success/15 text-success flex items-center justify-center hover:bg-success/25 transition-colors border border-success/10">
                                    <i class="bi bi-qr-code"></i>
                                </button>
                            @else
                                <a href="{{ route('admin.qr.generate.page', $product->id) }}" 
                                    class="w-8 h-8 rounded-lg bg-warning/15 text-warning flex items-center justify-center hover:bg-warning/25 transition-colors border border-warning/10">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                            @endif
                            
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                                class="w-8 h-8 rounded-lg bg-accent/15 text-accent flex items-center justify-center hover:bg-accent/25 transition-colors border border-accent/10">
                                <i class="bi bi-pencil"></i>
                            </a>
                            
                            <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Удалить?')">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-lg bg-danger/15 text-danger flex items-center justify-center hover:bg-danger/25 transition-colors border border-danger/10">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12 text-muted italic">Товары не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Мобильный список (карточки) --}}
    <div class="md:hidden divide-y divide-white/5">
        @forelse($products as $product)
        <div class="p-4 active:bg-white/[0.03] transition-colors">
            <div class="flex gap-4 mb-3">
                <div class="flex-shrink-0">
                    @if($product->photo)
                        <img src="{{ asset('storage/' . $product->photo) }}" class="w-14 h-14 rounded-xl object-cover border border-white/10 shadow-sm">
                    @else
                        <div class="w-14 h-14 rounded-xl bg-success/15 flex items-center justify-center text-success border border-success/10 text-xl">
                            <i class="bi bi-box"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-grow min-w-0">
                    <div class="flex justify-between items-start gap-2">
                        <div class="font-bold text-text leading-tight truncate text-sm">{{ $product->name }}</div>
                        <div class="text-success font-bold text-sm whitespace-nowrap">
                            {{ number_format($product->retail_price ?? 0, 0, '.', ' ') }} <span class="text-[9px] opacity-70">сом</span>
                        </div>
                    </div>
                    <div class="text-muted text-[10px] mt-0.5 mb-1.5">{{ $product->category?->name ?? 'Без категории' }}</div>
                    
                    <div class="flex flex-col gap-1 mt-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-mono text-muted bg-white/5 px-1.5 py-0.5 rounded border border-white/5">
                                {{ $product->article }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                            @php
                                $warehouseQty = $product->variants->flatMap(fn($v) => $v->stock)->whereNotNull('warehouse_id')->sum('quantity');
                                $shopQty = $product->variants->flatMap(fn($v) => $v->stock)->whereNotNull('shop_id')->sum('quantity');
                            @endphp
                            <span class="text-[11px] font-bold flex items-center gap-1 {{ $warehouseQty > 0 ? 'text-accent' : 'text-muted opacity-60' }}">
                                <i class="bi bi-box-seam"></i> Склад: {{ $warehouseQty }} шт
                            </span>
                            <span class="text-[11px] font-bold flex items-center gap-1 {{ $shopQty > 0 ? 'text-success' : 'text-muted opacity-60' }}">
                                <i class="bi bi-shop"></i> Витрина: {{ $shopQty }} шт
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2 pt-1">
                @php
                    $hasQr = false;
                    $firstQrCodes = [];
                    foreach($product->variants as $variant) {
                        if($variant->qrCodes && $variant->qrCodes->count() > 0) {
                            $hasQr = true;
                            $firstQrCodes = $variant->qrCodes->toArray();
                            break;
                        }
                    }
                @endphp
                
                @if($hasQr)
                    <button type="button" 
                        onclick='showQrModal(@json($product->id), @json($product->name), @json($firstQrCodes))'
                        class="flex-1 py-2 rounded-lg bg-success/10 text-success flex items-center justify-center gap-2 text-xs border border-success/10">
                        <i class="bi bi-qr-code"></i> QR-код
                    </button>
                @else
                    <a href="{{ route('admin.qr.generate.page', $product->id) }}" 
                        class="flex-1 py-2 rounded-lg bg-warning/10 text-warning flex items-center justify-center gap-2 text-xs border border-warning/10">
                        <i class="bi bi-qr-code"></i> QR-код
                    </a>
                @endif
                
                <a href="{{ route('admin.products.edit', $product->id) }}" 
                    class="flex-1 py-2 rounded-lg bg-accent/10 text-accent flex items-center justify-center gap-2 text-xs border border-accent/10">
                    <i class="bi bi-pencil text-[10px]"></i> Изменить
                </a>
                
                <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" class="flex-shrink-0" onsubmit="return confirm('Удалить?')">
                    @csrf
                    <button type="submit" class="w-10 py-2 rounded-lg bg-danger/10 text-danger flex items-center justify-center border border-danger/10">
                        <i class="bi bi-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="py-12 text-center text-muted italic">Товары не найдены</div>
        @endforelse
    </div>
    
    @if($products->hasPages())
    <div class="px-5 py-4 border-t border-white/5 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white/5">
        <div class="text-xs text-muted">Показано {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} из {{ $products->total() }}</div>
        <div class="flex gap-2 w-full sm:w-auto">
            @if($products->onFirstPage())
                <span class="flex-1 sm:flex-none px-4 py-2 rounded-lg bg-white/5 text-muted/50 text-center text-sm">←</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="flex-1 sm:flex-none px-4 py-2 rounded-lg bg-accent/10 text-accent text-center text-sm">←</a>
            @endif
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="flex-1 sm:flex-none px-4 py-2 rounded-lg bg-accent/10 text-accent text-center text-sm">→</a>
            @else
                <span class="flex-1 sm:flex-none px-4 py-2 rounded-lg bg-white/5 text-muted/50 text-center text-sm">→</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Модальное окно просмотра QR-кода --}}
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-b border-white/5">
                <h5 class="modal-title flex items-center gap-2 text-base">
                    <i class="bi bi-qr-code text-accent"></i> QR-коды
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-6">
                <h4 id="qr-product-name" class="text-base font-bold text-text mb-4 leading-tight"></h4>
                <div id="qr-codes-container" class="flex flex-wrap gap-4 justify-center"></div>
            </div>
            <div class="modal-footer border-t border-white/5">
                <button type="button" class="btn btn-outline w-full" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script>
function showQrModal(productId, productName, qrCodes) {
    document.getElementById('qr-product-name').textContent = productName;
    const container = document.getElementById('qr-codes-container');
    container.innerHTML = '';
    
    qrCodes.forEach(qr => {
        const div = document.createElement('div');
        div.className = 'flex flex-col items-center gap-3 p-4 bg-sidebar/50 rounded-2xl border border-white/5';
        
        const qrContent = qr.qr_data && qr.qr_data.article ? qr.qr_data.article : '—';
        const storageUrl = "{{ asset('storage') }}/";
        const qrImageUrl = qr.qr_code_path ? storageUrl + qr.qr_code_path : 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrContent);
        
        div.innerHTML = `
            <div class="bg-white p-2 rounded-xl">
                <img src="${qrImageUrl}" alt="QR" class="w-40 h-40 object-contain">
            </div>
            <div class="text-[10px] font-mono text-muted uppercase tracking-wider">${qrContent}</div>
            <a href="${qrImageUrl}" download class="btn btn-primary w-full py-2 text-[10px]">
                <i class="bi bi-download"></i> Скачать
            </a>
        `;
        container.appendChild(div);
    });
    
    const modalElement = document.getElementById('qrModal');
    if (window.bootstrap && window.bootstrap.Modal) {
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
    }
}
</script>
@endsection
