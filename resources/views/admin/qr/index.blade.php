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
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form action="{{ route('admin.qr.manage') }}" method="GET" style="display: flex; margin-bottom: 0;">
                <input type="text" name="search" class="form-control" placeholder="Поиск товаров..." value="{{ request('search') }}" style="min-width: 200px;">
                <button class="btn btn-outline-secondary" type="submit" style="margin-left: 8px;">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 16px;">Товар</th>
                    <th style="padding: 16px;">Артикул</th>
                    <th style="padding: 16px;">QR-коды</th>
                    <th style="padding: 16px;">Статус</th>
                    <th style="padding: 16px; text-align: right;">Действия</th>
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
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" 
                                         style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;">
                                @else
                                    <div style="width: 48px; height: 48px; border-radius: 8px; background: linear-gradient(135deg, rgba(56,189,248,0.2), rgba(16,185,129,0.2)); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 20px;">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight: 600; color: #f1f5f9; margin-bottom: 4px;">{{ Str::limit($product->name, 50) }}</div>
                                    <div style="font-size: 12px; color: #94a3b8;">
                                        <i class="bi bi-grid-3x3-gap"></i> {{ $variantsCount }} вариант(-ов)
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px; color: #94a3b8; font-family: monospace; font-size: 14px;">{{ $product->article }}</td>
                        <td style="padding: 16px;">
                            @if($qrCount > 0)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="background: rgba(16,185,129,0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                        <i class="bi bi-check-circle"></i> {{ $qrCount }}
                                    </span>
                                    @if($printedCount > 0)
                                        <span style="color: #94a3b8; font-size: 12px;">
                                            ({{ $printedCount }} напечатано)
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span style="color: #94a3b8; font-size: 13px;">
                                    <i class="bi bi-x-circle"></i> Нет QR
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($qrCount >= $variantsCount)
                                <span class="badge badge-success">
                                    <i class="bi bi-check-all"></i> Все готовы
                                </span>
                            @elseif($qrCount > 0)
                                <span class="badge badge-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Частично
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="bi bi-x-lg"></i> Не готов
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap;">
                                <a href="{{ route('admin.qr.manage.product', $product->id) }}" 
                                   class="btn btn-primary btn-sm"
                                   style="min-width: 100px;">
                                    <i class="bi bi-grid-3x3-gap"></i> Управление
                                </a>
                                @if($qrCount < $variantsCount)
                                    <a href="{{ route('admin.qr.generate.page', $product->id) }}" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>
                                @endif
                            </div>
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
