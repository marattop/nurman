@extends('seller::layouts.seller')
@section('page-title', 'QR-сканер')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code-scan"></i></div>
    <div>
        <h1 class="page-title">QR-сканер</h1>
        <p class="page-subtitle">Сканирование QR-кодов товаров</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
    <div class="card">
        <div style="text-align: center;">
            <div id="scanner-container" style="background: #0f172a; border-radius: 12px; overflow: hidden; margin-bottom: 20px; min-height: 300px; display: flex; align-items: center; justify-content: center;">
                <video id="scanner-video" style="width: 100%; display: none;"></video>
                <div id="scanner-placeholder" style="color: #64748b;">
                    <i class="bi bi-camera" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                    Нажмите "Начать сканирование"
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <button id="start-scan" class="btn btn-primary">
                    <i class="bi bi-camera"></i> Начать сканирование
                </button>
                <button id="stop-scan" class="btn btn-danger" style="display: none;">
                    <i class="bi bi-stop-circle"></i> Остановить
                </button>
            </div>

            <div style="margin-top: 20px;">
                <label style="display: block; color: #94a3b8; font-size: 13px; margin-bottom: 8px;">Или введите код вручную:</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="manual-qr" class="input" placeholder="Введите QR-код">
                    <button class="btn btn-outline" onclick="scanManual()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div id="result-card" style="display: none;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div style="width: 40px; height: 40px; background: rgba(16,185,129,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #10b981;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: #10b981;">Товар найден!</div>
                    <div style="font-size: 13px; color: #94a3b8;">Информация о товаре</div>
                </div>
            </div>
            
            <div id="product-info">
                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                    <div id="product-image" style="width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                        <i class="bi bi-box" style="font-size: 32px; opacity: 0.3;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div id="product-name" style="font-weight: 600; font-size: 18px; margin-bottom: 8px;">—</div>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <div style="color: #94a3b8; font-size: 13px;">Артикул: <span id="product-article" style="color: #f1f5f9;">—</span></div>
                            <div style="color: #94a3b8; font-size: 13px;">Цена: <span id="product-price" style="color: #10b981; font-weight: 600;">—</span></div>
                            <div style="color: #94a3b8; font-size: 13px;">Остаток: <span id="product-stock" style="color: #f1f5f9;">—</span></div>
                        </div>
                    </div>
                </div>
                
                <button class="btn btn-primary" style="width: 100%;" onclick="addToCart()">
                    <i class="bi bi-cart-plus"></i> Добавить в корзину
                </button>
            </div>
        </div>
        
        <div id="no-result" style="text-align: center; padding: 40px 20px; color: #64748b;">
            <i class="bi bi-qr-code" style="font-size: 64px; opacity: 0.2; display: block; margin-bottom: 16px;"></i>
            <div>Отсканируйте QR-код для получения информации о товаре</div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrcodeScanner = null;
let currentProduct = null;

document.getElementById('start-scan').addEventListener('click', function() {
    this.style.display = 'none';
    document.getElementById('stop-scan').style.display = 'inline-flex';
    document.getElementById('scanner-placeholder').style.display = 'none';
    document.getElementById('scanner-video').style.display = 'block';
    
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
    document.getElementById('scanner-placeholder').style.display = 'block';
    document.getElementById('scanner-video').style.display = 'none';
    
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            html5QrcodeScanner.clear();
        }).catch(err => {
            console.error("Ошибка остановки", err);
        });
    }
});

function onScanSuccess(decodedText) {
    document.getElementById('manual-qr').value = decodedText;
    findProduct(decodedText);
}

function scanManual() {
    const code = document.getElementById('manual-qr').value.trim();
    if (code) {
        findProduct(code);
    }
}

function findProduct(code) {
    fetch('/seller/products/search-by-article/' + code)
        .then(r => r.json())
        .then(data => {
            if (data.product) {
                currentProduct = data.product;
                showProduct(data.product);
            } else {
                alert('Товар не найден');
            }
        })
        .catch(() => {
            alert('Ошибка поиска товара');
        });
}

function showProduct(product) {
    document.getElementById('no-result').style.display = 'none';
    document.getElementById('result-card').style.display = 'block';
    
    document.getElementById('product-name').textContent = product.name;
    document.getElementById('product-article').textContent = product.article;
    document.getElementById('product-price').textContent = product.price + ' сом';
    document.getElementById('product-stock').textContent = (product.stock_quantity || 0) + ' шт';
    
    if (product.photo) {
        const storageUrl = "{{ asset('storage') }}/";
        document.getElementById('product-image').innerHTML = `<img src="${storageUrl}${product.photo}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">`;
    }
}

function addToCart() {
    if (currentProduct) {
        // Добавление в корзину (реализуйте вашу логику)
        alert('Добавлено: ' + currentProduct.name);
    }
}
</script>
@endsection
