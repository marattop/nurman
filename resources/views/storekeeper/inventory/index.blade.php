@extends('layouts.storekeeper')

@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code-scan"></i></div>
    <div>
        <h1 class="page-title">Инвентаризация</h1>
        <p class="page-subtitle">Сканирование товаров</p>
    </div>
    <div style="margin-left: auto;">
        <button type="button" onclick="startScan()" class="btn btn-primary">
            <i class="bi bi-qr-code-scan"></i> Сканировать
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    @if(session('success'))
    <div style="grid-column: 1 / -1; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 10px;">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Панель сканирования --}}
    <div class="card">
        <h3 style="margin: 0 0 16px 0; font-size: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-qr-code-scan" style="color: #22c55e;"></i>
            Сканирование штрих-кода
        </h3>

        <form id="scan-form" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8; font-size: 14px;">Штрих-код товара</label>
                <input type="text" id="barcode-input" name="barcode" 
                    class="input" 
                    placeholder="Отсканируйте или введите штрих-код" 
                    autofocus 
                    style="font-size: 16px; padding: 12px 16px;">
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 12px 20px; font-size: 15px;">
                <i class="bi bi-search"></i> Найти товар
            </button>
        </form>

        <div id="scan-result" style="margin-top: 24px; display: none;">
            <div style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); border-radius: 12px; padding: 16px;">
                <div style="display: flex; gap: 16px;">
                    <div style="width: 64px; height: 64px; border-radius: 12px; background: rgba(34,197,94,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-box" style="font-size: 28px; color: #22c55e;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h4 style="margin: 0 0 4px 0; font-size: 16px;" id="result-name"></h4>
                        <p style="margin: 0 0 8px 0; font-size: 13px; color: #94a3b8;" id="result-article"></p>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <span class="badge badge-blue" id="result-size"></span>
                            <span class="badge badge-purple" id="result-color"></span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(34,197,94,0.2); display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">На складе</p>
                        <p style="margin: 0; font-size: 24px; font-weight: 700;" id="result-quantity"></p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">SKU</p>
                        <p style="margin: 0; font-family: monospace; font-size: 13px;" id="result-sku"></p>
                    </div>
                </div>
            </div>

            {{-- Форма сохранения --}}
            <form id="inventory-form" style="margin-top: 16px; display: flex; flex-direction: column; gap: 12px;">
                @csrf
                <input type="hidden" name="variant_id" id="inventory-variant-id">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8; font-size: 14px;">Фактическое количество</label>
                    <input type="number" name="actual_quantity" id="actual-quantity" 
                        class="input" 
                        placeholder="0" 
                        min="0"
                        style="font-size: 18px; padding: 12px 16px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8; font-size: 14px;">Комментарий</label>
                    <textarea name="comment" id="inventory-comment" rows="2"
                        class="input resize-none"
                        placeholder="Причина расхождения..."></textarea>
                </div>
                <button type="submit" class="btn btn-success" style="padding: 12px 20px;">
                    <i class="bi bi-check-circle"></i> Сохранить результат
                </button>
            </form>
        </div>

        <div id="scan-error" style="margin-top: 24px; display: none;">
            <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; padding: 16px; color: #ef4444;">
                <i class="bi bi-exclamation-triangle"></i>
                <span id="error-message"></span>
            </div>
        </div>
    </div>

    {{-- История --}}
    <div class="card">
        <h3 style="margin: 0 0 16px 0; font-size: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-clock-history" style="color: #22c55e;"></i>
            Последние операции
        </h3>
        <div id="scan-history" style="display: flex; flex-direction: column; gap: 8px;">
            <p style="text-align: center; color: #64748b; padding: 24px 0;">История сканирований пуста</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const scanHistory = [];

document.getElementById('scan-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const barcode = document.getElementById('barcode-input').value.trim();
    if (!barcode) {
        showError('Введите штрих-код');
        return;
    }

    try {
        const response = await fetch('{{ route("storekeeper.inventory.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ barcode })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            showResult(data.product);
            addToHistory(data.product);
        } else {
            showError(data.error || 'Товар не найден');
        }
    } catch (error) {
        showError('Ошибка соединения: ' + error.message);
    }
});

document.getElementById('inventory-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const variantId = document.getElementById('inventory-variant-id').value;
    
    try {
        const response = await fetch('{{ route("storekeeper.inventory.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                variant_id: variantId,
                actual_quantity: formData.get('actual_quantity'),
                comment: formData.get('comment')
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            alert('✅ ' + data.message);
            document.getElementById('scan-result').style.display = 'none';
            document.getElementById('barcode-input').value = '';
            document.getElementById('barcode-input').focus();
        } else {
            showError(data.error || 'Ошибка сохранения');
        }
    } catch (error) {
        showError('Ошибка сохранения: ' + error.message);
    }
});

function showResult(product) {
    document.getElementById('result-name').textContent = product.name;
    document.getElementById('result-article').textContent = product.article + ' • ' + product.category;
    document.getElementById('result-size').textContent = product.size || '—';
    document.getElementById('result-color').textContent = product.color || '—';
    document.getElementById('result-quantity').textContent = product.quantity + ' шт';
    document.getElementById('result-sku').textContent = product.sku || '—';
    
    document.getElementById('inventory-variant-id').value = product.id;
    document.getElementById('actual-quantity').value = product.quantity;
    
    document.getElementById('scan-error').style.display = 'none';
    document.getElementById('scan-result').style.display = 'block';
}

function showError(message) {
    document.getElementById('error-message').textContent = message;
    document.getElementById('scan-result').style.display = 'none';
    document.getElementById('scan-error').style.display = 'block';
}

function addToHistory(product) {
    scanHistory.unshift(product);
    if (scanHistory.length > 10) scanHistory.pop();
    renderHistory();
}

function renderHistory() {
    const container = document.getElementById('scan-history');
    
    if (scanHistory.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #64748b; padding: 24px 0;">История сканирований пуста</p>';
        return;
    }
    
    container.innerHTML = scanHistory.map(p => `
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.03);">
            <div style="flex: 1; min-width: 0;">
                <p style="margin: 0; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${p.name}</p>
                <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">${p.article} • ${p.size}/${p.color}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; font-weight: 700; color: #22c55e;">${p.quantity} шт</p>
            </div>
        </div>
    `).join('');
}

function startScan() {
    document.getElementById('barcode-input').focus();
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('form')) {
        document.getElementById('barcode-input').focus();
    }
});
</script>
@endpush
