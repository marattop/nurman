@extends('layouts.admin')
@section('page-title', 'QR-сканер')
@section('content')

<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 24px;">
        <i class="bi bi-qr-code-scan"></i>
    </div>
    <div>
        <h2 style="font-size: 28px; font-weight: 700; margin: 0;">QR-сканер</h2>
        <p style="font-size: 14px; color: #94a3b8; margin: 4px 0 0 0;">Быстрое сканирование товаров</p>
    </div>
</div>

{{-- Основная зона сканирования --}}
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 0;">
        {{-- Камера --}}
        <div style="background: #0f172a; position: relative; min-height: 500px;">
            <div id="scanner-container" style="position: absolute; inset: 0; width: 100%; height: 100%;"></div>
            
            {{-- Рамка сканирования --}}
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 280px; height: 280px; border: 3px solid rgba(56,189,248,0.8); border-radius: 16px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.7);">
                <div style="position: absolute; top: -3px; left: -3px; width: 40px; height: 40px; border-top: 4px solid #10b981; border-left: 4px solid #10b981; border-top-left-radius: 16px;"></div>
                <div style="position: absolute; top: -3px; right: -3px; width: 40px; height: 40px; border-top: 4px solid #10b981; border-right: 4px solid #10b981; border-top-right-radius: 16px;"></div>
                <div style="position: absolute; bottom: -3px; left: -3px; width: 40px; height: 40px; border-bottom: 4px solid #10b981; border-left: 4px solid #10b981; border-bottom-left-radius: 16px;"></div>
                <div style="position: absolute; bottom: -3px; right: -3px; width: 40px; height: 40px; border-bottom: 4px solid #10b981; border-right: 4px solid #10b981; border-bottom-right-radius: 16px;"></div>
                
                {{-- Линия сканирования --}}
                <div style="position: absolute; left: 10px; right: 10px; height: 2px; background: linear-gradient(90deg, transparent, #10b981, transparent); animation: scan-line 2s ease-in-out infinite;"></div>
            </div>

            {{-- Кнопки управления --}}
            <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 12px;">
                <button id="start-scan" class="btn btn-primary" style="padding: 14px 28px; font-size: 16px;">
                    <i class="bi bi-camera"></i> Начать сканирование
                </button>
                <button id="stop-scan" class="btn btn-danger" style="display: none; padding: 14px 28px; font-size: 16px;">
                    <i class="bi bi-stop-circle"></i> Остановить
                </button>
            </div>

            {{-- Индикатор сканирования --}}
            <div id="scan-indicator" style="position: absolute; top: 20px; right: 20px; display: none;">
                <div style="background: rgba(16,185,129,0.9); color: #fff; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                    <i class="bi bi-check-circle"></i> Сканирование...
                </div>
            </div>
        </div>

        {{-- Результат --}}
        <div style="background: #1e293b; padding: 24px; overflow-y: auto; max-height: 500px;">
            <div id="no-result" style="text-align: center; padding: 60px 20px; color: #64748b;">
                <i class="bi bi-qr-code" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;"></i>
                <p style="font-size: 16px;">Наведите камеру на QR-код</p>
                <p style="font-size: 13px; margin-top: 8px;">Товар отобразится здесь автоматически</p>
            </div>

            <div id="scan-result" style="display: none;">
                {{-- Фото товара --}}
                <div style="text-align: center; margin-bottom: 20px;">
                    <img id="product-photo" src="" alt="Товар" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 12px; border: 2px solid rgba(56,189,248,0.3);">
                </div>

                {{-- Информация о товаре --}}
                <div style="margin-bottom: 20px;">
                    <h3 id="product-name" style="font-size: 20px; font-weight: 700; color: #f1f5f9; margin: 0 0 8px 0;"></h3>
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 12px;">
                        <span style="color: #38bdf8;">Артикул:</span> <span id="product-article" style="color: #f1f5f9; font-weight: 500;"></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <span style="font-size: 28px; font-weight: 700; color: #10b981;" id="product-price"></span>
                    </div>
                    
                    {{-- Статистика --}}
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px;">
                        <div style="background: rgba(56,189,248,0.1); border-radius: 10px; padding: 12px; text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: #38bdf8;" id="total-stock">0</div>
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Всего</div>
                        </div>
                        <div style="background: rgba(16,185,129,0.1); border-radius: 10px; padding: 12px; text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: #10b981;" id="showcase-stock">0</div>
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Витрина</div>
                        </div>
                        <div style="background: rgba(245,158,11,0.1); border-radius: 10px; padding: 12px; text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: #f59e0b;" id="warehouse-stock">0</div>
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Склад</div>
                        </div>
                    </div>
                </div>

                {{-- Варианты --}}
                <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 16px;">
                    <h4 style="font-size: 14px; font-weight: 600; color: #94a3b8; margin: 0 0 12px 0; text-transform: uppercase;">Варианты</h4>
                    <div id="variants-list" style="display: flex; flex-direction: column; gap: 8px;"></div>
                </div>

                {{-- Кнопки действий --}}
                <div style="margin-top: 20px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <a id="edit-product-btn" href="#" class="btn btn-outline" style="flex: 1; justify-content: center;">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <button onclick="clearResult()" class="btn btn-outline" style="flex: 1; justify-content: center;">
                        <i class="bi bi-arrow-clockwise"></i> Ещё раз
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Звуковой сигнал --}}
<audio id="scan-sound" preload="auto">
    <source src="data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU" type="audio/wav">
</audio>

<style>
@keyframes scan-line {
    0%, 100% { top: 10px; opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { top: 270px; opacity: 0; }
}

.btn-primary {
    background: linear-gradient(135deg, #38bdf8, #0284c7);
    color: #0f172a;
    border: none;
    font-weight: 600;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0ea5e9, #0369a1);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(56,189,248,0.3);
}

.variant-item {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 10px;
    padding: 12px;
    transition: 0.2s;
}

.variant-item:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(56,189,248,0.3);
}
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let isScanning = false;
let lastScanTime = 0;
const SCAN_COOLDOWN = 2000; // 2 секунды между сканированиями

function startScanning() {
    if (isScanning) return;
    isScanning = true;

    document.getElementById('start-scan').style.display = 'none';
    document.getElementById('stop-scan').style.display = 'inline-flex';
    document.getElementById('scan-indicator').style.display = 'block';

    // Html5Qrcode сам создаёт/крутит видео внутри контейнера
    html5QrCode = new Html5Qrcode('scanner-container');

    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 15, qrbox: { width: 250, height: 250 } },
        onScanSuccess,
        onScanError
    ).catch(err => {
        console.error('Ошибка запуска сканера:', err);
        isScanning = false;
        document.getElementById('start-scan').style.display = 'inline-flex';
        document.getElementById('stop-scan').style.display = 'none';
        document.getElementById('scan-indicator').style.display = 'none';
        alert('Не удалось запустить сканер. Проверьте разрешения камеры.');
    });
}

// Запуск сканирования
document.getElementById('start-scan').addEventListener('click', startScanning);

// Остановка сканирования
document.getElementById('stop-scan').addEventListener('click', stopScanning);

function stopScanning() {
    if (!html5QrCode) return;

    html5QrCode.stop()
        .then(() => html5QrCode.clear())
        .catch(err => console.error('Ошибка остановки:', err))
        .finally(() => {
            isScanning = false;
            document.getElementById('start-scan').style.display = 'inline-flex';
            document.getElementById('stop-scan').style.display = 'none';
            document.getElementById('scan-indicator').style.display = 'none';
        });
}

// Успешное сканирование
function onScanSuccess(decodedText) {
    const now = Date.now();
    if (now - lastScanTime < SCAN_COOLDOWN) return;
    lastScanTime = now;

    // Звуковой сигнал
    playBeep();

    // Вибрация (если поддерживается)
    if (navigator.vibrate) navigator.vibrate(200);

    console.log('Отсканировано:', decodedText);
    fetchProductInfo(decodedText);
}

function onScanError(error) {
    // Игнорируем ошибки сканирования
}

// Получение информации о товаре
async function fetchProductInfo(article) {
    try {
        const response = await fetch('{{ route('admin.qr.process') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qr_data: article })
        });

        const data = await response.json();
        
        if (data.success) {
            displayProduct(data);
        } else {
            showNotFound(article);
        }
    } catch (err) {
        console.error('Ошибка:', err);
        showNotFound(article);
    }
}

// Отображение товара
function displayProduct(data) {
    document.getElementById('no-result').style.display = 'none';
    document.getElementById('scan-result').style.display = 'block';

    const product = data.product;
    const summary = data.summary;

    // Фото
    const photoEl = document.getElementById('product-photo');
    if (product.photo) {
        photoEl.src = product.photo;
        photoEl.style.display = 'inline-block';
    } else {
        photoEl.style.display = 'none';
    }

    // Информация
    document.getElementById('product-name').textContent = product.name;
    document.getElementById('product-article').textContent = product.article;
    document.getElementById('product-price').textContent = product.retail_price + ' сом';
    document.getElementById('edit-product-btn').href = '/admin/products/edit/' + product.id;

    // Статистика
    document.getElementById('total-stock').textContent = summary.total_stock;
    document.getElementById('showcase-stock').textContent = summary.showcase;
    document.getElementById('warehouse-stock').textContent = summary.warehouse;

    // Варианты
    const variantsList = document.getElementById('variants-list');
    variantsList.innerHTML = '';

    data.variants.forEach(v => {
        const div = document.createElement('div');
        div.className = 'variant-item';
        div.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-weight: 600; color: #f1f5f9;">${v.size || '—'} ${v.color ? '(' + v.color + ')' : ''}</span>
                <span style="color: #10b981; font-weight: 700;">${v.total_quantity} шт</span>
            </div>
            <div style="font-size: 12px; color: #94a3b8;">
                <span>Витрина: <strong style="color: #10b981;">${v.showcase_quantity}</strong></span>
                <span style="margin: 0 8px;">|</span>
                <span>Склад: <strong style="color: #f59e0b;">${v.warehouse_quantity}</strong></span>
            </div>
        `;
        variantsList.appendChild(div);
    });
}

// Товар не найден
function showNotFound(article) {
    document.getElementById('no-result').style.display = 'block';
    document.getElementById('scan-result').style.display = 'none';
    document.getElementById('no-result').innerHTML = `
        <i class="bi bi-x-circle" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3; color: #ef4444;"></i>
        <p style="font-size: 16px; color: #ef4444;">Товар не найден</p>
        <p style="font-size: 13px; color: #94a3b8; margin-top: 8px;">Артикул: ${article}</p>
    `;
}

// Очистка результата
function clearResult() {
    document.getElementById('no-result').style.display = 'block';
    document.getElementById('no-result').innerHTML = `
        <i class="bi bi-qr-code" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;"></i>
        <p style="font-size: 16px;">Наведите камеру на QR-код</p>
        <p style="font-size: 13px; margin-top: 8px;">Товар отобразится здесь автоматически</p>
    `;
    document.getElementById('scan-result').style.display = 'none';
}

// Звуковой сигнал
function playBeep() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();

    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);

    oscillator.frequency.value = 880;
    oscillator.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);

    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.15);
}

// Остановка при уходе со страницы
window.addEventListener('beforeunload', stopScanning);
</script>
@endsection
