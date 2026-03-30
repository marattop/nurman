@extends('layouts.admin')
@section('page-title', 'Управление QR-кодами')
@section('content')

{{-- Расчет статистики QR-кодов --}}
@php
    $totalQr = $product->variants->sum(fn($v) => $v->qrCodes->count());
    $printedQr = $product->variants->sum(fn($v) => $v->qrCodes->where('is_printed', true)->count());
    $notPrintedQr = $totalQr - $printedQr;
@endphp

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

            <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <a href="{{ route('admin.qr.manage') }}" class="btn btn-outline">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
                @if($totalQr < $product->variants->count() || $product->variants->count() === 0)
                    <a href="{{ route('admin.qr.generate.page', $product->id) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> {{ $totalQr > 0 ? 'Создать недостающие' : 'Генерировать QR' }}
                    </a>
                @else
                    <div style="background: rgba(16,185,129,0.15); color: #10b981; padding: 10px 18px; border-radius: 12px; font-weight: 600; font-size: 14px; border: 1px solid rgba(16,185,129,0.3); display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-check-all" style="font-size: 20px;"></i> Все QR готовы
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Статистика QR-кодов --}}

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
        @if($product->variants->count() > 0)
            @if($totalQr < $product->variants->count())
                <a href="{{ route('admin.qr.generate.page', $product->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-qr-code-scan"></i> {{ $totalQr > 0 ? 'Создать недостающие QR' : 'Создать QR для всех' }}
                </a>
            @else
                <div class="badge badge-success" style="padding: 8px 12px; font-size: 13px;">
                    <i class="bi bi-check-all"></i> Все QR-коды созданы
                </div>
            @endif
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
                        
                        <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 20px; align-items: center; flex-wrap: wrap;">
                            {{-- Предпросмотр QR --}}
                            <div style="width: 80px; height: 80px; background: white; border-radius: 8px; padding: 4px; display: flex; align-items: center; justify-content: center; position: relative;">
                                @if($hasQr)
                                    @php
                                        $qrContent = $variantQr->qr_data['article'] ?? $product->article;
                                        $qrImageUrl = $variantQr->qr_code_path && file_exists(public_path('storage/' . $variantQr->qr_code_path))
                                            ? asset('storage/' . $variantQr->qr_code_path)
                                            : 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrContent);
                                    @endphp
                                    <img src="{{ $qrImageUrl }}" alt="QR" style="width: 72px; height: 72px; object-fit: contain;">
                                    @if($isPrinted)
                                        <div style="position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; border: 2px solid #1e293b;">
                                            <i class="bi bi-check"></i>
                                        </div>
                                    @endif
                                @else
                                    <div style="color: #cbd5e1; font-size: 24px; opacity: 0.2;">
                                        <i class="bi bi-qr-code"></i>
                                    </div>
                                    <div style="position: absolute; inset: 0; background: rgba(30,41,59,0.5); display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                        <i class="bi bi-slash-circle" style="color: #ef4444; font-size: 20px;"></i>
                                    </div>
                                @endif
                            </div>

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
                                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                        <button onclick="printQr({{ $variant->id }}, '{{ $product->article }}', '{{ addslashes($product->name) }}', '{{ $variant->size ?? 'Стандарт' }}', '{{ $variant->color ?? '' }}')" 
                                                class="btn btn-primary btn-sm"
                                                style="min-width: 130px; background: #38bdf8; border-color: #38bdf8; color: #1e293b; font-weight: 700;">
                                            <i class="bi bi-printer"></i> 
                                            {{ $isPrinted ? 'Печать снова' : 'Печать QR' }}
                                        </button>
                                        
                                        <button onclick="deleteQr({{ $variantQr->id }}, {{ $variant->id }})" 
                                                class="btn btn-outline-danger btn-sm"
                                                style="font-size: 11px; padding: 2px 8px; opacity: 0.8;">
                                            <i class="bi bi-trash"></i> Удалить QR
                                        </button>
                                    </div>
                                @else
                                    <button onclick="generateSingleQr({{ $variant->id }})" 
                                            class="btn btn-outline-success btn-sm"
                                            style="min-width: 130px; border-width: 2px;">
                                        <i class="bi bi-plus-lg"></i> Создать QR
                                    </button>
                                @endif
                                
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="btn btn-outline btn-sm"
                                   style="display: flex; align-items: center; justify-content: center; width: 34px;">
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
        const response = await fetch(`{{ route('admin.qr.delete', ['id' => 'QR_ID']) }}`.replace('QR_ID', qrId), {
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
