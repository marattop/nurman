@extends('core::layouts.master')
@section('page-title', 'Сканер QR-кода')
@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-qr-code-scan me-2"></i>Сканировать QR-код</div>
            <div class="card-body text-center">
                <div id="scanner-container" class="mb-3"></div>
                
                <div class="d-grid gap-2">
                    <button id="start-scan" class="btn btn-primary">
                        <i class="bi bi-camera"></i> Включить камеру
                    </button>
                    <button id="stop-scan" class="btn btn-danger" disabled>
                        <i class="bi bi-camera-off"></i> Остановить
                    </button>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <label class="form-label">Или введите данные QR-кода вручную:</label>
                    <div class="input-group">
                        <input type="text" id="manual-qr" class="form-control" placeholder="Отсканируйте или введите код">
                        <button class="btn btn-outline-primary" type="button" onclick="scanManual()">
                            <i class="bi bi-search"></i> Найти
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card" id="result-card" style="display: none;">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-info-circle me-2"></i>Информация о товаре
            </div>
            <div class="card-body">
                <div id="result-content"></div>
            </div>
        </div>
        
        <div class="card" id="error-card" style="display: none;">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle me-2"></i>Ошибка
            </div>
            <div class="card-body">
                <p id="error-message" class="mb-0"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrcodeScanner = null;
let isScanning = false;

function startScan() {
    if (isScanning) return;
    html5QrcodeScanner = new Html5Qrcode("scanner-container");
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess,
        onScanFailure
    ).then(() => {
        isScanning = true;
        document.getElementById('start-scan').disabled = true;
        document.getElementById('stop-scan').disabled = false;
    }).catch(err => {
        console.error("Ошибка:", err);
        showError("Не удалось запустить камеру");
    });
}

function stopScan() {
    if (!isScanning || !html5QrcodeScanner) return;
    html5QrcodeScanner.stop().then(() => {
        isScanning = false;
        document.getElementById('start-scan').disabled = false;
        document.getElementById('stop-scan').disabled = true;
        html5QrcodeScanner.clear();
    });
}

function onScanSuccess(decodedText, decodedResult) {
    processQrData(decodedText);
}

function onScanFailure(error) {}

function processQrData(qrData) {
    document.getElementById('manual-qr').value = qrData;
    fetch('{{ route("warehouse.scan-qr.api") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ qr_data: qrData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) showResult(data);
        else showError(data.error || 'Товар не найден');
    })
    .catch(error => showError('Ошибка при получении данных'));
}

function scanManual() {
    const qrData = document.getElementById('manual-qr').value.trim();
    if (!qrData) { showError('Введите данные QR-кода'); return; }
    processQrData(qrData);
}

function showResult(data) {
    document.getElementById('error-card').style.display = 'none';
    document.getElementById('result-card').style.display = 'block';
    
    const stockHtml = data.stock.map(s => `
        <tr>
            <td>${s.location}</td>
            <td><strong>${s.quantity}</strong></td>
            <td>${s.reserved}</td>
            <td><strong class="${s.is_low ? 'text-danger' : 'text-success'}">${s.available}</strong></td>
            <td>${s.min_stock}</td>
            <td>${s.is_low ? '<span class="badge bg-danger">Мало</span>' : '<span class="badge bg-success">Норма</span>'}</td>
        </tr>
    `).join('');
    
    document.getElementById('result-content').innerHTML = `
        <h5 class="text-primary">${data.product.name}</h5>
        <p class="mb-1"><strong>Артикул:</strong> ${data.product.article}</p>
        <p class="mb-1"><strong>Категория:</strong> ${data.product.category}</p>
        ${data.product.brand !== '-' ? `<p class="mb-1"><strong>Бренд:</strong> ${data.product.brand}</p>` : ''}
        <hr>
        <p class="mb-1"><strong>Размер:</strong> ${data.variant.size}</p>
        <p class="mb-1"><strong>Цвет:</strong> ${data.variant.color}</p>
        <p class="mb-1"><strong>SKU:</strong> ${data.variant.sku}</p>
        <p class="mb-1"><strong>Штрих-код:</strong> ${data.variant.barcode}</p>
        <hr>
        <h6>Остатки по точкам:</h6>
        <table class="table table-sm table-bordered">
            <thead><tr><th>Точка</th><th>Всего</th><th>Резерв</th><th>Доступно</th><th>Мин.</th><th>Статус</th></tr></thead>
            <tbody>${stockHtml}</tbody>
        </table>
        <div class="row g-2 mt-2">
            <div class="col-4">
                <div class="p-2 bg-light rounded text-center">
                    <small class="text-muted">Всего</small>
                    <div class="fs-6 fw-bold">${data.total.total_quantity} шт</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 bg-light rounded text-center">
                    <small class="text-muted">Резерв</small>
                    <div class="fs-6 fw-bold text-warning">${data.total.total_reserved} шт</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 bg-light rounded text-center">
                    <small class="text-muted">Доступно</small>
                    <div class="fs-6 fw-bold text-success">${data.total.total_available} шт</div>
                </div>
            </div>
        </div>
    `;
}

function showError(message) {
    document.getElementById('result-card').style.display = 'none';
    document.getElementById('error-card').style.display = 'block';
    document.getElementById('error-message').textContent = message;
}

document.getElementById('start-scan').addEventListener('click', startScan);
document.getElementById('stop-scan').addEventListener('click', stopScan);
window.addEventListener('beforeunload', () => { if (isScanning) stopScan(); });
</script>
@endsection
