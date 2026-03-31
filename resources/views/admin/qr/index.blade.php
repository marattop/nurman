@extends('layouts.admin')
@section('page-title', 'QR-коды')
@section('content')

<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code"></i></div>
    <div>
        <h1 class="page-title">QR-коды</h1>
        <p class="page-subtitle">Управление QR-кодами товаров</p>
    </div>
</div>

{{-- Быстрые действия --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <a href="{{ route('admin.qr.scan') }}" class="card" style="text-decoration: none; color: inherit; transition: 0.2s; border: 1px solid rgba(56,189,248,0.2);"
       onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(56,189,248,0.15)'"
       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
        <div class="card-body" style="padding: 20px; text-align: center;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #38bdf8; font-size: 24px;">
                <i class="bi bi-qr-code-scan"></i>
            </div>
            <div style="font-weight: 600; color: #f1f5f9; margin-bottom: 4px;">Сканировать</div>
            <div style="font-size: 13px; color: #94a3b8;">QR-сканер товаров</div>
        </div>
    </a>
    
    <button onclick="generateAllQr()" class="card" style="text-decoration: none; color: inherit; transition: 0.2s; border: 1px solid rgba(16,185,129,0.2); cursor: pointer; background: transparent; width: 100%;"
       onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(16,185,129,0.15)'"
       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
        <div class="card-body" style="padding: 20px; text-align: center;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #10b981; font-size: 24px;">
                <i class="bi bi-qr-code"></i>
            </div>
            <div style="font-weight: 600; color: #f1f5f9; margin-bottom: 4px;">Генерировать все</div>
            <div style="font-size: 13px; color: #94a3b8;">QR для всех товаров</div>
        </div>
    </button>
</div>

{{-- Список товаров --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <h3 class="card-header-title" style="margin-bottom: 0;">
            <i class="bi bi-list-ul" style="color: #38bdf8;"></i> 
            Список товаров
            <span style="color: #94a3b8; font-size: 14px; font-weight: 400;">({{ $products->count() }} шт)</span>
        </h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; width: 100%; sm:width: auto;">
            <form action="{{ route('admin.qr.manage') }}" method="GET" style="display: flex; margin-bottom: 0; width: 100%; max-width: 400px; gap: 8px;">
                <div style="position: relative; flex: 1;">
                    <i class="bi bi-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="text" name="search" class="form-control" placeholder="Поиск товаров или артикула..." value="{{ request('search') }}" 
                           style="padding-left: 36px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.2); color: #f1f5f9; border-radius: 10px; width: 100%; height: 42px; transition: 0.2s;"
                           onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 4px rgba(56, 189, 248, 0.1)'"
                           onblur="this.style.borderColor='rgba(56, 189, 248, 0.2)'; this.style.boxShadow='none'">
                </div>
                <button class="btn btn-primary" type="submit" style="height: 42px; border-radius: 10px; padding: 0 16px;">
                    Найти
                </button>
            </form>
        </div>
    </div>
    
    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 16px; width: 35%;">Товар</th>
                    <th style="text-align: left; padding: 16px; width: 15%;">Артикул</th>
                    <th style="text-align: left; padding: 16px; width: 20%;">QR-коды</th>
                    <th style="text-align: left; padding: 16px; width: 15%;">Статус</th>
                    <th style="text-align: right; padding: 16px; width: 15%;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $qrCount = $product->variants->sum(fn($v) => $v->qrCodes->count());
                        $printedCount = $product->variants->sum(fn($v) => $v->qrCodes->where('is_printed', true)->count());
                        $variantsCount = $product->variants->count();
                    @endphp
                    
                    <tr style="transition: 0.2s;" 
                        onmouseover="this.style.background='rgba(56,189,248,0.05)'" 
                        onmouseout="this.style.background='transparent'">
                        <td data-label="Товар" style="padding: 16px; text-align: left;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" 
                                         style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover; flex-shrink: 0;">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 6px; background: linear-gradient(135deg, rgba(56,189,248,0.2), rgba(16,185,129,0.2)); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 18px; flex-shrink: 0;">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                                <div style="min-width: 0;">
                                    <div style="font-weight: 600; color: #f1f5f9; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product->name }}</div>
                                    <div style="font-size: 11px; color: #94a3b8;">
                                        {{ $variantsCount }} вар.
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Артикул" style="padding: 16px; text-align: left; color: #94a3b8; font-family: monospace; font-size: 13px;">{{ $product->article }}</td>
                        <td data-label="QR-коды" style="padding: 16px; text-align: left;">
                            <div style="display: flex; align-items: center; gap: 10px; justify-content: flex-end; md:justify-content: flex-start;">
                                <div style="width: 38px; height: 38px; background: white; border-radius: 4px; padding: 2px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(255,255,255,0.1);">
                                    @php
                                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' . urlencode($product->article);
                                    @endphp
                                    <img src="{{ $qrUrl }}" alt="QR" style="width: 34px; height: 34px; object-fit: contain;">
                                </div>

                                @if($qrCount > 0)
                                    <div style="display: flex; flex-direction: column; text-align: left;">
                                        <span style="color: #10b981; font-weight: 600; font-size: 12px;">{{ $qrCount }} QR</span>
                                        @if($printedCount > 0)
                                            <span style="color: #94a3b8; font-size: 10px;">{{ $printedCount }} печ.</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: #64748b; font-size: 12px;">Нет QR</span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Статус" style="padding: 16px; text-align: left;">
                            @if($qrCount >= $variantsCount && $variantsCount > 0)
                                <span class="badge badge-success" style="font-size: 11px; padding: 4px 8px;">Готов</span>
                            @elseif($qrCount > 0)
                                <span class="badge badge-warning" style="font-size: 11px; padding: 4px 8px;">Частично</span>
                            @else
                                <span class="badge badge-danger" style="font-size: 11px; padding: 4px 8px;">Нет</span>
                            @endif
                        </td>
                        <td data-label="Действия" style="padding: 16px; text-align: right;">
                            <a href="{{ route('admin.qr.manage.product', $product->id) }}" 
                               class="btn btn-primary btn-sm"
                               style="padding: 6px 12px; font-size: 12px; width: 100%; justify-content: center;">
                                <i class="bi bi-gear"></i> Управление
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 60px 20px; color: #64748b;">
                            <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                            <p style="font-size: 16px;">Товары не найдены</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
        <div style="color: #94a3b8; font-size: 13px;">Показано {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} из {{ $products->total() }}</div>
        <div style="display: flex; gap: 8px;">
            @if($products->onFirstPage())
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">← Назад</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: rgba(56,189,248,0.1); color: #38bdf8; text-decoration: none; font-size: 14px;">← Назад</a>
            @endif
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: rgba(56,189,248,0.1); color: #38bdf8; text-decoration: none; font-size: 14px;">Вперёд →</a>
            @else
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">Вперёд →</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Модальное окно подтверждения генерации --}}
<div id="generateAllModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #1e293b; border-radius: 16px; padding: 32px; max-width: 450px; width: 90%; text-align: center; border: 1px solid rgba(16,185,129,0.3);">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #10b981; font-size: 32px;">
            <i class="bi bi-qr-code"></i>
        </div>
        <h3 style="font-size: 20px; font-weight: 700; color: #f1f5f9; margin: 0 0 8px 0;">Генерация QR-кодов</h3>
        <p style="color: #94a3b8; margin: 0 0 24px 0;">
            Вы уверены, что хотите сгенерировать QR-коды для всех товаров?<br>
            <strong style="color: #f1f5f9;">Это может занять некоторое время.</strong>
        </p>
        
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeGenerateModal()" class="btn btn-outline" style="flex: 1;">
                <i class="bi bi-x-lg"></i> Отмена
            </button>
            <button onclick="confirmGenerateAll()" id="confirmGenerateBtn" class="btn btn-primary" style="flex: 1;">
                <i class="bi bi-check-lg"></i> Генерировать
            </button>
        </div>
    </div>
</div>

{{-- Индикатор загрузки --}}
<div id="loadingOverlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 9998; align-items: center; justify-content: center; flex-direction: column;">
    <div style="width: 64px; height: 64px; border: 4px solid rgba(16,185,129,0.3); border-top-color: #10b981; border-radius: 50%; animation: spin 1s linear infinite;"></div>
    <p style="color: #f1f5f9; margin-top: 20px; font-size: 16px; font-weight: 600;">Генерация QR-кодов...</p>
    <p style="color: #94a3b8; margin-top: 8px; font-size: 14px;">Пожалуйста, дождитесь завершения</p>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
// Генерация всех QR-кодов
function generateAllQr() {
    document.getElementById('generateAllModal').style.display = 'flex';
}

function closeGenerateModal() {
    document.getElementById('generateAllModal').style.display = 'none';
}

async function confirmGenerateAll() {
    const btn = document.getElementById('confirmGenerateBtn');
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Обработка...';
    
    // Показываем индикатор загрузки
    closeGenerateModal();
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    try {
        const response = await fetch('{{ route('admin.qr.generate-all') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Перезагружаем страницу
            window.location.reload();
        } else {
            alert('Ошибка: ' + (result.message || 'Неизвестная ошибка'));
            document.getElementById('loadingOverlay').style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    } catch (error) {
        alert('Ошибка: ' + error.message);
        document.getElementById('loadingOverlay').style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

// Закрытие модального окна по клику вне его
document.getElementById('generateAllModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerateModal();
    }
});
</script>
@endsection
