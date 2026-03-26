@extends('layouts.admin')
@section('page-title', 'Управление QR-кодами')
@section('content')

<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code"></i></div>
    <div>
        <h1 class="page-title">Управление QR-кодами</h1>
        <p class="page-subtitle">{{ $product->name }}</p>
    </div>
</div>

{{-- Карточка товара --}}
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="padding: 24px;">
        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            @if($product->photo)
                <img src="{{ asset('storage/' . $product->photo) }}" 
                     alt="{{ $product->name }}" 
                     style="width: 100px; height: 100px; border-radius: 12px; object-fit: cover; border: 2px solid rgba(56,189,248,0.3);">
            @else
                <div style="width: 100px; height: 100px; border-radius: 12px; background: linear-gradient(135deg, rgba(56,189,248,0.2), rgba(16,185,129,0.2)); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 40px;">
                    <i class="bi bi-box"></i>
                </div>
            @endif
            
            <div style="flex: 1; min-width: 200px;">
                <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 8px 0; color: #f1f5f9;">{{ $product->name }}</h2>
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    <span style="color: #94a3b8; font-size: 14px;">
                        <i class="bi bi-upc-scan" style="color: #38bdf8;"></i> 
                        Артикул: <strong style="color: #f1f5f9;">{{ $product->article }}</strong>
                    </span>
                    @if($product->retail_price)
                        <span style="color: #94a3b8; font-size: 14px;">
                            <i class="bi bi-currency-dollar" style="color: #10b981;"></i> 
                            Цена: <strong style="color: #10b981;">{{ number_format($product->retail_price, 0, ',', ' ') }} сом</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route('admin.qr.manage') }}" class="btn btn-outline">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
                <a href="{{ route('admin.qr.generate.page', $product->id) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Генерировать QR
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Статистика QR-кодов --}}
@php
    $totalQr = $product->variants->sum(fn($v) => $v->qrCodes->count());
    $printedQr = $product->variants->sum(fn($v) => $v->qrCodes->where('is_printed', true)->count());
    $notPrintedQr = $totalQr - $printedQr;
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div style="background: linear-gradient(135deg, rgba(56,189,248,0.15), rgba(56,189,248,0.05)); border: 1px solid rgba(56,189,248,0.2); border-radius: 16px; padding: 20px; text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #38bdf8; margin-bottom: 4px;">{{ $totalQr }}</div>
        <div style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Всего QR-кодов</div>
    </div>
    <div style="background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05)); border: 1px solid rgba(16,185,129,0.2); border-radius: 16px; padding: 20px; text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 4px;">{{ $printedQr }}</div>
        <div style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Напечатано</div>
    </div>
    <div style="background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border: 1px solid rgba(245,158,11,0.2); border-radius: 16px; padding: 20px; text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #f59e0b; margin-bottom: 4px;">{{ $notPrintedQr }}</div>
        <div style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Не напечатано</div>
    </div>
</div>

{{-- Список вариантов с QR-кодами --}}
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <h3 class="card-header-title">
            <i class="bi bi-grid-3x3-gap" style="color: #38bdf8;"></i> 
            Варианты товара 
            <span style="color: #94a3b8; font-size: 14px; font-weight: 400;">({{ $product->variants->count() }} шт)</span>
        </h3>
        @if($product->variants->count() > 0 && $totalQr === 0)
            <a href="{{ route('admin.qr.generate.page', $product->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-qr-code-scan"></i> Создать QR для всех
            </a>
        @endif
    </div>
    
    <div class="card-body" style="padding: 0;">
        @if($product->variants->count() === 0)
            <div style="padding: 60px 20px; text-align: center; color: #64748b;">
                <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                <p style="font-size: 16px;">У этого товара нет вариантов</p>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline" style="margin-top: 12px;">
                    <i class="bi bi-pencil"></i> Добавить варианты
                </a>
            </div>
        @else
            <div style="display: grid; gap: 12px; padding: 16px;">
                @foreach($product->variants as $variant)
                    @php
                        $variantQr = $variant->qrCodes->first();
                        $hasQr = $variantQr !== null;
                        $isPrinted = $hasQr && $variantQr->is_printed;
                        
                        // Подсчёт остатков
                        $totalQty = $variant->stock->sum('quantity') ?? 0;
                        $showcaseQty = $variant->stock->where('location_id', 2)->sum('quantity') ?? 0;
                        $warehouseQty = $variant->stock->where('location_id', 1)->sum('quantity') ?? 0;
                    @endphp
                    
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px; transition: 0.2s;" 
                         onmouseover="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(56,189,248,0.2)'" 
                         onmouseout="this.style.background='rgba(255,255,255,0.03)'; this.style.borderColor='rgba(255,255,255,0.05)'">
                        
                        <div style="display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: center; flex-wrap: wrap;">
                            {{-- Информация о варианте --}}
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; flex-wrap: wrap;">
                                    <span style="font-size: 18px; font-weight: 700; color: #f1f5f9;">
                                        {{ $variant->size ?? 'Стандарт' }}
                                        @if($variant->color)
                                            <span style="color: #94a3b8; font-weight: 400;">({{ $variant->color }})</span>
                                        @endif
                                    </span>
                                    @if($hasQr)
                                        <span class="badge {{ $isPrinted ? 'badge-success' : 'badge-warning' }}" style="font-size: 12px;">
                                            <i class="bi bi-{{ $isPrinted ? 'check-circle' : 'clock' }}"></i>
                                            {{ $isPrinted ? 'Напечатан' : 'Не напечатан' }}
                                        </span>
                                    @else
                                        <span class="badge badge-danger" style="font-size: 12px;">
                                            <i class="bi bi-x-circle"></i> Нет QR
                                        </span>
                                    @endif
                                </div>
                                
                                <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 12px;">
                                    <span style="color: #94a3b8; font-size: 13px;">
                                        <i class="bi bi-upc-scan" style="color: #38bdf8;"></i> 
                                        Штрихкод: <strong style="color: #f1f5f9;">{{ $variant->barcode ?? '—' }}</strong>
                                    </span>
                                    <span style="color: #94a3b8; font-size: 13px;">
                                        <i class="bi bi-box-seam" style="color: #10b981;"></i> 
                                        Всего: <strong style="color: #10b981;">{{ $totalQty }} шт</strong>
                                    </span>
                                </div>
                                
                                {{-- Остатки по локациям --}}
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <span style="background: rgba(16,185,129,0.1); color: #10b981; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                        <i class="bi bi-shop"></i> Витрина: {{ $showcaseQty }}
                                    </span>
                                    <span style="background: rgba(245,158,11,0.1); color: #f59e0b; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                        <i class="bi bi-building"></i> Склад: {{ $warehouseQty }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Кнопки действий --}}
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end;">
                                @if($hasQr)
                                    <button onclick="printQr({{ $variant->id }}, '{{ $product->article }}', '{{ addslashes($product->name) }}', '{{ $variant->size ?? 'Стандарт' }}', '{{ $variant->color ?? '' }}')" 
                                            class="btn btn-primary btn-sm"
                                            style="min-width: 120px;">
                                        <i class="bi bi-printer"></i> 
                                        {{ $isPrinted ? 'Печатать снова' : 'Печать' }}
                                    </button>
                                    
                                    <button onclick="deleteQr({{ $variantQr->id }}, {{ $variant->id }})" 
                                            class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                @else
                                    <button onclick="generateSingleQr({{ $variant->id }})" 
                                            class="btn btn-outline-success btn-sm"
                                            style="min-width: 120px;">
                                        <i class="bi bi-plus-lg"></i> Создать QR
                                    </button>
                                @endif
                                
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="btn btn-outline btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Модальное окно печати --}}
<div id="printModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #1e293b; border-radius: 16px; padding: 32px; max-width: 500px; width: 90%; text-align: center; border: 1px solid rgba(56,189,248,0.3);">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #38bdf8; font-size: 32px;">
            <i class="bi bi-printer"></i>
        </div>
        <h3 style="font-size: 20px; font-weight: 700; color: #f1f5f9; margin: 0 0 8px 0;">Печать QR-кода</h3>
        <p id="printInfo" style="color: #94a3b8; margin: 0 0 24px 0;"></p>
        
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closePrintModal()" class="btn btn-outline" style="flex: 1;">
                <i class="bi bi-x-lg"></i> Отмена
            </button>
            <button onclick="confirmPrint()" class="btn btn-primary" style="flex: 1;">
                <i class="bi bi-check-lg"></i> Подтвердить
            </button>
        </div>
    </div>
</div>

<script>
let currentVariantId = null;
let currentQrId = null;

// Генерация QR для одного варианта
async function generateSingleQr(variantId) {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Создание...';
    
    try {
        const response = await fetch('{{ route('admin.qr.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin',
            body: JSON.stringify({ 
                product_id: {{ $product->id }},
                variant_id: variantId 
            })
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            location.reload();
        } else {
            alert('Ошибка: ' + (result.message || 'Неизвестная ошибка'));
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    } catch (error) {
        alert('Ошибка: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

// Печать QR
function printQr(variantId, article, productName, size, color) {
    currentVariantId = variantId;
    
    document.getElementById('printInfo').innerHTML = `
        <strong style="color: #f1f5f9;">${productName}</strong><br>
        Размер: ${size} ${color ? '(' + color + ')' : ''}<br>
        Артикул: <strong style="color: #38bdf8;">${article}</strong>
    `;
    
    document.getElementById('printModal').style.display = 'flex';
}

function closePrintModal() {
    document.getElementById('printModal').style.display = 'none';
    currentVariantId = null;
}

async function confirmPrint() {
    if (!currentVariantId) return;
    
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Печать...';
    
    try {
        const response = await fetch('{{ route('admin.qr.print') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin',
            body: JSON.stringify({ variant_id: currentVariantId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Здесь можно открыть окно печати или отправить данные на принтер
            alert('QR-код готов к печати!\n\nАртикул: ' + result.qr_data + '\nТовар: ' + result.product_name);
            closePrintModal();
            location.reload();
        } else {
            alert('Ошибка печати: ' + (result.message || 'Неизвестная ошибка'));
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    } catch (error) {
        alert('Ошибка: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

// Удаление QR
async function deleteQr(qrId, variantId) {
    if (!confirm('Вы уверены, что хотите удалить QR-код?')) return;
    
    currentQrId = qrId;
    currentVariantId = variantId;
    
    try {
        const response = await fetch(`{{ route('admin.qr.delete', '') }}/${qrId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Ошибка удаления: ' + (result.message || 'Неизвестная ошибка'));
        }
    } catch (error) {
        alert('Ошибка: ' + error.message);
    }
}

// Закрытие модального окна по клику вне его
document.getElementById('printModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePrintModal();
    }
});
</script>

@endsection
