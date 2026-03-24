@extends('layouts.admin')
@section('page-title', 'QR-сканер')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code-scan"></i></div>
    <div>
        <h1 class="page-title">QR-сканер</h1>
        <p class="page-subtitle">Сканирование QR-кодов товаров</p>
    </div>
</div>

<div class="card">
    <div style="max-width: 640px; margin: 0 auto; text-align: center;">
        <div id="scanner-container" style="background: #0f172a; border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
            <video id="scanner-video" style="width: 100%; display: block;"></video>
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <button id="start-scan" class="btn btn-primary">
                <i class="bi bi-camera"></i> Начать сканирование
            </button>
            <button id="stop-scan" class="btn btn-danger" style="display: none;">
                <i class="bi bi-stop-circle"></i> Остановить
            </button>
        </div>

        <div id="scan-result" style="margin-top: 24px; display: none;">
            <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; padding: 20px;">
                <h3 style="margin: 0 0 8px 0; color: #10b981;"><i class="bi bi-check-circle"></i> Товар найден!</h3>
                <div id="product-info" style="color: #94a3b8;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrcodeScanner = null;

document.getElementById('start-scan').addEventListener('click', function() {
    this.style.display = 'none';
    document.getElementById('stop-scan').style.display = 'inline-flex';
    
    html5QrcodeScanner = new Html5Qrcode("scanner-container");
    
    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess
            ).catch(err => {
                console.error("Ошибка запуска сканера", err);
                alert("Не удалось запустить камеру");
            });
        }
    }).catch(err => {
        console.error("Ошибка получения камер", err);
        alert("Камера не найдена");
    });
});

document.getElementById('stop-scan').addEventListener('click', function() {
    this.style.display = 'none';
    document.getElementById('start-scan').style.display = 'inline-flex';
    
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            html5QrcodeScanner.clear();
        }).catch(err => {
            console.error("Ошибка остановки", err);
        });
    }
});

function onScanSuccess(decodedText) {
    document.getElementById('scan-result').style.display = 'block';
    document.getElementById('product-info').innerHTML = `
        <div style="font-size: 18px; font-weight: 600; color: #f1f5f9; margin-bottom: 8px;">${decodedText}</div>
        <div style="color: #94a3b8;">Артикул: ${decodedText}</div>
    `;
    
    // Поиск товара по артикулу
    fetch('/admin/products/search-by-article/' + decodedText)
        .then(r => r.json())
        .then(data => {
            if (data.product) {
                document.getElementById('product-info').innerHTML = `
                    <div style="font-size: 20px; font-weight: 600; color: #f1f5f9; margin-bottom: 4px;">${data.product.name}</div>
                    <div style="color: #94a3b8;">Артикул: ${data.product.article}</div>
                    <div style="color: #10b981; font-weight: 600; margin-top: 8px;">${data.product.price} сом</div>
                `;
            }
        })
        .catch(() => {});
}
</script>
@endsection
