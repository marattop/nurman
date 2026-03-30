@extends('layouts.admin')

@section('page-title', 'Сканер товаров')
@section('page-subtitle', 'Мгновенный поиск товаров по QR и штрих-кодам')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Scanner Column -->
        <div class="lg:col-span-7 space-y-6">
            <div id="scanner-wrapper" class="glass overflow-visible rounded-[2rem] relative aspect-[4/5] md:aspect-square bg-black shadow-2xl border-white/10">
                <!-- Camera Viewport -->
                <div id="scanner-container" class="absolute inset-0 rounded-[2rem] overflow-hidden"></div>

                <!-- Overlay Gradients - ПОВЕРХ видео -->
                <div class="scanner-overlay absolute inset-0 pointer-events-none bg-gradient-to-t from-black/60 via-transparent to-black/30 rounded-[2rem]" style="opacity: 1;"></div>

                <!-- Scanning Frame - ПОВЕРХ видео -->
                <div class="scanner-overlay absolute inset-0 flex items-center justify-center pointer-events-none rounded-[2rem]" style="opacity: 1;">
                    <div class="w-64 h-64 md:w-80 md:h-80 border-2 border-white/20 rounded-[2rem] relative">
                        <!-- Corners -->
                        <div class="absolute -top-1 -left-1 w-12 h-12 border-t-4 border-l-4 border-accent rounded-tl-3xl"></div>
                        <div class="absolute -top-1 -right-1 w-12 h-12 border-t-4 border-r-4 border-accent rounded-tr-3xl"></div>
                        <div class="absolute -bottom-1 -left-1 w-12 h-12 border-b-4 border-l-4 border-accent rounded-bl-3xl"></div>
                        <div class="absolute -bottom-1 -right-1 w-12 h-12 border-b-4 border-r-4 border-accent rounded-br-3xl"></div>

                        <!-- Scanning Line -->
                        <div class="absolute left-4 right-4 h-1 bg-accent/50 shadow-[0_0_15px_rgba(168,85,247,0.8)] animate-[scan_2s_ease-in-out_infinite] top-0 opacity-0"></div>
                    </div>
                </div>

                <!-- Manual Input (Floating) -->
                <div class="absolute bottom-24 left-6 right-6" style="z-index: 30;">
                    <div class="glass p-2 rounded-2xl flex items-center gap-2 border-white/20 shadow-xl">
                        <input type="text" id="manual-article" placeholder="Введите артикул вручную..."
                            class="flex-1 bg-transparent border-none text-white placeholder-white/40 px-4 py-2 focus:ring-0 text-sm"
                            onkeypress="if(event.key==='Enter') lookupManualArticle()">
                        <button onclick="lookupManualArticle()" class="w-10 h-10 rounded-xl bg-accent text-bg flex items-center justify-center hover:scale-95 transition-transform">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Scanner Controls -->
                <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-4 px-6" style="z-index: 30;">
                    <button id="start-scan" class="flex-1 glass py-4 rounded-2xl flex items-center justify-center gap-3 font-bold text-white hover:bg-white/10 transition-all border-white/20">
                        <i class="bi bi-camera-fill text-xl text-accent"></i> Запустить камеру
                    </button>
                    <button id="stop-scan" class="hidden flex-1 glass py-4 rounded-2xl flex items-center justify-center gap-3 font-bold text-danger hover:bg-danger/10 transition-all border-danger/20">
                        <i class="bi bi-stop-circle-fill text-xl"></i> Остановить
                    </button>
                </div>

                <!-- Status Badge -->
                <div id="scan-indicator" class="hidden absolute top-6 right-6" style="z-index: 30;">
                    <div class="bg-success text-bg px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 shadow-lg animate-pulse">
                        <span class="w-2 h-2 bg-bg rounded-full animate-ping"></span> Активно
                    </div>
                </div>
            </div>

            <!-- Hint Card -->
            <div class="glass p-6 rounded-3xl border-white/5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-accent">
                    <i class="bi bi-info-circle text-xl"></i>
                </div>
                <p class="text-xs text-muted leading-relaxed">
                    Наведите камеру на QR-код или штрих-код товара. Система автоматически распознает его и отобразит информацию о цене, остатках и характеристиках.
                </p>
            </div>
        </div>

        <!-- Result Column -->
        <div class="lg:col-span-5">
            <div id="result-placeholder" class="glass p-12 rounded-[2rem] text-center border-dashed border-white/10 flex flex-col items-center justify-center h-full min-h-[400px]">
                <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-muted/30 mb-6">
                    <i class="bi bi-qr-code text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Ожидание сканирования</h3>
                <p class="text-muted text-sm max-w-[200px] mx-auto leading-relaxed">
                    Данные товара появятся здесь сразу после успешного считывания
                </p>
            </div>

            <div id="scan-result" class="hidden space-y-4">
                <!-- Product Card -->
                <div class="glass rounded-[2rem] overflow-hidden shadow-2xl border-white/10">
                    <div class="relative h-48 bg-white/5 flex items-center justify-center">
                        <img id="product-photo" src="" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-bg via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4 flex gap-2" id="badges-container">
                            <!-- Badges injected here -->
                        </div>
                    </div>

                    <div class="p-6">
                        <h2 id="product-name" class="text-2xl font-black text-white leading-tight mb-1 truncate"></h2>
                        <div class="flex items-center gap-2 text-muted text-xs mb-6">
                            <span class="font-bold text-accent uppercase tracking-tighter">Артикул:</span>
                            <span id="product-article" class="font-mono"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="glass p-4 rounded-2xl border-white/5">
                                <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-1">Розница</p>
                                <p id="product-retail-price" class="text-xl font-black text-success"></p>
                            </div>
                            <div class="glass p-4 rounded-2xl border-white/5">
                                <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-1">Прибыль</p>
                                <p id="product-profit" class="text-xl font-black text-accent"></p>
                            </div>
                        </div>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center justify-between px-2">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-box-seam text-muted"></i>
                                    <span class="text-sm font-bold text-text">Общий остаток</span>
                                </div>
                                <span id="total-stock" class="text-lg font-black text-white"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                                    <span class="text-[10px] font-bold text-muted uppercase">Витрина</span>
                                    <span id="showcase-stock" class="font-bold text-white"></span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                                    <span class="text-[10px] font-bold text-muted uppercase">Склад</span>
                                    <span id="warehouse-stock" class="font-bold text-white"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <a id="edit-product-btn" href="#" class="flex-1 bg-accent hover:bg-accent-hover text-bg py-4 rounded-2xl flex items-center justify-center gap-2 font-black transition-all">
                                <i class="bi bi-pencil-square"></i> Изменить
                            </a>
                            <button onclick="clearResult()" class="w-14 h-14 glass rounded-2xl flex items-center justify-center text-muted hover:text-white transition-colors border-white/10">
                                <i class="bi bi-arrow-clockwise text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Variants Card -->
                <div class="glass p-6 rounded-[2rem] border-white/5">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="bi bi-layers text-accent"></i> Варианты и штрих-коды
                    </h3>
                    <div id="variants-list" class="space-y-3">
                        <!-- Variants injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Drawer Result Overlay -->
<div id="mobile-result-overlay" class="fixed inset-0 z-[9999]" style="display: none;">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeMobileResult()"></div>
    <div class="absolute bottom-0 left-0 right-0 glass rounded-t-[2.5rem] border-t border-white/20 p-6 transform translate-y-full transition-transform duration-300 ease-out max-h-[90vh] overflow-y-auto" id="mobile-drawer" style="z-index: 10000; position: relative;">
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-8"></div>
        <div id="mobile-result-content"></div>
    </div>
</div>

<style>
@keyframes scan {
    0% { top: 0; opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}

/* Анимация успешного сканирования */
@keyframes scan-success {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Пульсация при сканировании */
@keyframes scanning-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.4); }
    50% { box-shadow: 0 0 0 20px rgba(168, 85, 247, 0); }
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Glass morphism effect */
.glass {
    background: rgba(30, 41, 59, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
}

/* Улучшенная рамка сканера */
.scanner-active #scanner-container {
    animation: scanning-pulse 2s infinite;
}

.scanner-success {
    animation: scan-success 0.3s ease-in-out;
}

/* Видео превью - базовые стили */
#scanner-container {
    background: #000;
    border-radius: 2rem;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    min-height: 500px;
    overflow: hidden;
    z-index: 0 !important;
}

/* Стили для видео от html5-qrcode библиотеки */
#scanner-container video,
#scanner-container .__scanner video,
#scanner-container video#__scan_video {
    width: 100% !important;
    height: 100% !important;
    min-height: 100% !important;
    min-width: 100% !important;
    object-fit: cover;
    border-radius: 2rem;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    z-index: 10 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Контейнер сканирования от библиотеки */
#scanner-container .__scanner {
    width: 100% !important;
    height: 100% !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    z-index: 0 !important;
    background: #000 !important;
}

.scanner-overlay {
    z-index: 20 !important;
}

/* Принудительное отображение видео на десктопе */
@media (min-width: 769px) {
    #scanner-container video,
    #scanner-container .__scanner video,
    #scanner-container video#__scan_video {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Явная высота для десктопа */
    #scanner-wrapper {
        min-height: 500px !important;
    }
    
    #scanner-container {
        height: 100% !important;
        min-height: 500px !important;
    }
}

/* Mobile Adaptation */
@media (max-width: 768px) {
    body { padding-bottom: 0 !important; }
    .sidebar { transform: translateX(-100%) !important; transition: 0.3s; z-index: 1001; }
    .sidebar.show { transform: translateX(0) !important; }
    .main-content { margin-left: 0 !important; padding-top: 0 !important; }

    /* Hide page header on mobile */
    .qr-scan-header { display: none !important; }

    /* Full width container */
    .max-w-5xl { max-width: 100% !important; padding: 0 !important; }
    .grid { gap: 0 !important; }

    /* Scanner full screen */
    .lg\\:col-span-7 { grid-column: span 12 !important; }
    .lg\\:col-span-5 { display: none !important; }

    /* Camera container full viewport */
    #scanner-wrapper {
        height: 100dvh !important;
        width: 100% !important;
        border-radius: 0 !important;
    }
    
    #scanner-container {
        height: 100dvh !important;
        width: 100% !important;
        border-radius: 0 !important;
    }

    /* Manual input positioned at bottom */
    .absolute.bottom-24 { bottom: 80px !important; }

    /* Scanner controls at very bottom */
    .absolute.bottom-6 { bottom: 16px !important; }

    /* Show scanner indicator on mobile */
    #scan-indicator { top: 16px !important; right: 16px !important; }
    
    /* Mobile result overlay - full screen */
    #mobile-result-overlay {
        position: fixed !important;
        inset: 0 !important;
        z-index: 9999 !important;
    }
    
    #mobile-result-overlay .absolute {
        z-index: 1 !important;
    }

    #mobile-drawer {
        max-height: 90vh !important;
        border-radius: 24px 24px 0 0 !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    
    /* Убираем скругления для overlay на мобильном */
    #mobile-result-overlay > div:first-child {
        border-radius: 0 !important;
    }
}

/* Desktop hides mobile overlay */
@media (min-width: 769px) {
    #mobile-result-overlay { display: none !important; }
}

/* Mobile overlay visibility classes */
.mobile-overlay-visible {
    display: block !important;
}

.mobile-overlay-hidden {
    display: none !important; }

/* Mobile overlay explicit styles */
#mobile-result-overlay {
    display: none;
}

#mobile-result-overlay.mobile-overlay-visible {
    display: block !important;
}

#mobile-drawer {
    background: rgba(30, 41, 59, 0.95);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}
</style>

<!-- Громкий звук сканирования -->
<audio id="scan-sound" preload="auto" volume="1.0">
    <source src="data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAADhAC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7////////////////////////////////////////////////////////////AAAAAExhdmM1OC4xMzQAAAAAAAAAAAAAAAAkAAAAAAAAA4S/T4LKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/7kGQAD/AAAGkAAAAIAAANIAAAAQAAAaQAAAAgAAA0gAAABExBTUUzLjEwMFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV//uQZAoP8AAAaQAAAAgAAA0gAAABAAABpAAAACAAADSAAAAEVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV" type="audio/mpeg">
</audio>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let isScanning = false;
let lastScanTime = 0;
let lastScannedCode = '';
const SCAN_COOLDOWN = 300; // Ещё меньше для быстрой реакции
let scanInFlight = false;
let audioContext = null;

function isMobile() {
    return window.matchMedia('(max-width: 768px)').matches;
}

async function startScanning() {
    if (isScanning) return;
    isScanning = true;
    scanInFlight = false;

    document.getElementById('start-scan').classList.add('hidden');
    document.getElementById('stop-scan').classList.remove('hidden');
    document.getElementById('scan-indicator').classList.remove('hidden');
    document.getElementById('scanner-wrapper').classList.add('scanner-active');

    html5QrCode = new Html5Qrcode('scanner-container');

    // Вычисляем размер сканера от контейнера, а не от окна
    const container = document.getElementById('scanner-container');
    const containerSize = Math.min(container.clientWidth, container.clientHeight);
    const qrBoxSize = Math.floor(containerSize * 0.7);

    // Пробуем сначала rear камеру, потом user (для вебок десктопа)
    const cameraConfig = isMobile()
        ? { facingMode: 'environment' }  // задняя камера на мобильном
        : { facingMode: 'user' };        // веб-камера на десктопе

    html5QrCode.start(
        cameraConfig,
        {
            fps: 10,
            qrbox: { width: qrBoxSize, height: qrBoxSize },
            aspectRatio: 1,
            disableFlip: false
        },
        onScanSuccess,
        onScanError
    )
    .catch(err => {
        console.error('Scanner error:', err);
        resetScannerUI();
        alert('Не удалось запустить камеру. Проверьте разрешения браузера для камеры.');
    });
}

function resetScannerUI() {
    isScanning = false;
    document.getElementById('start-scan').classList.remove('hidden');
    document.getElementById('stop-scan').classList.add('hidden');
    document.getElementById('scan-indicator').classList.add('hidden');
    document.getElementById('scanner-wrapper').classList.remove('scanner-active');
}

function stopScanning() {
    if (!html5QrCode) return;
    return html5QrCode.stop()
        .then(() => html5QrCode.clear())
        .finally(resetScannerUI);
}

document.getElementById('start-scan').addEventListener('click', startScanning);
document.getElementById('stop-scan').addEventListener('click', stopScanning);

async function onScanSuccess(decodedText) {
    const now = Date.now();

    // Защита от повторного сканирования того же кода (300ms)
    if (decodedText === lastScannedCode && now - lastScanTime < SCAN_COOLDOWN) {
        return;
    }

    if (now - lastScanTime < SCAN_COOLDOWN || scanInFlight) return;

    lastScanTime = now;
    lastScannedCode = decodedText;
    scanInFlight = true;

    // Тихий нежный звук
    playBeep();
    if (navigator.vibrate) navigator.vibrate(50);

    // Визуальный эффект
    document.getElementById('scanner-wrapper').classList.add('scanner-success');
    setTimeout(() => {
        document.getElementById('scanner-wrapper').classList.remove('scanner-success');
    }, 150);

    // Останавливаем камеру и сразу показываем результат
    try {
        await stopScanning();
        // Очищаем память сразу после сканирования
        lastScannedCode = '';
        scanInFlight = false;
        await fetchProductInfo(decodedText);
    } catch (e) {
        console.error('Processing error:', e);
        scanInFlight = false;
        lastScannedCode = '';
    }
}

function onScanError(error) {}

async function fetchProductInfo(qrData) {
    console.log('=== fetchProductInfo START ===');
    console.log('qrData:', qrData);
    
    try {
        // Принудительно используем HTTPS для API endpoint
        const apiUrl = '{{ route('admin.qr.process') }}'.replace('http://', 'https://');
        
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_data: qrData })
        });

        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            displayProduct(data);
        } else {
            console.log('Product not found. Message:', data.message);
            showNotFound(qrData);
        }
    } catch (err) {
        console.error('Fetch error:', err);
        showNotFound(qrData);
    }
}

async function lookupManualArticle() {
    const article = document.getElementById('manual-article').value.trim();
    if (!article) return;

    if (isScanning) await stopScanning();
    await fetchProductInfo(article);
}

function displayProduct(data) {
    const p = data.product;
    const s = data.summary;

    // На мобильных - сразу открываем на весь экран
    if (isMobile()) {
        const content = document.getElementById('mobile-result-content');
        content.innerHTML = buildProductHTML(p, s, data);

        const overlay = document.getElementById('mobile-result-overlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('mobile-overlay-visible');
        overlay.style.display = 'block';

        // Мгновенное открытие без задержки
        document.getElementById('mobile-drawer').classList.remove('translate-y-full');
    } else {
        // На десктопе показываем в правой колонке
        document.getElementById('result-placeholder').classList.add('hidden');
        document.getElementById('scan-result').classList.remove('hidden');
        
        // Заполняем данные
        document.getElementById('product-name').textContent = p.name;
        document.getElementById('product-article').textContent = p.article;
        document.getElementById('product-retail-price').textContent = formatPrice(p.retail_price);
        document.getElementById('product-profit').textContent = formatPrice((p.retail_price || 0) - (p.supplier_price || 0));
        document.getElementById('total-stock').textContent = s.total_stock + ' шт';
        document.getElementById('showcase-stock').textContent = s.shop_stock + ' шт';
        document.getElementById('warehouse-stock').textContent = s.warehouse_stock + ' шт';
        document.getElementById('edit-product-btn').href = '/admin/products/edit/' + p.id;
        
        if (p.photo) {
            document.getElementById('product-photo').src = p.photo;
        }
        
        // Бейджи
        const badgesContainer = document.getElementById('badges-container');
        badgesContainer.innerHTML = `
            <span class="px-3 py-1 bg-success/20 text-success text-[10px] font-black uppercase rounded-lg border border-success/30">
                ${data.is_known_qr ? '✓ Наш QR' : '⚠ Товар из базы'}
            </span>
            <span class="px-3 py-1 bg-white/10 text-white/70 text-[10px] font-bold uppercase rounded-lg">
                ${getFoundByText(data.found_by)}
            </span>
        `;
    }
}

function buildProductHTML(p, s, data) {
    const badges = data ? `
        <div class="flex gap-2 mb-3">
            <span class="px-3 py-1 bg-success/20 text-success text-[10px] font-black uppercase rounded-lg border border-success/30">
                ${data.is_known_qr ? '✓ Наш QR' : '⚠ Товар из базы'}
            </span>
            <span class="px-3 py-1 bg-white/10 text-white/70 text-[10px] font-bold uppercase rounded-lg">
                ${getFoundByText(data.found_by)}
            </span>
        </div>
    ` : '';

    const variantsHTML = p.variants && p.variants.length > 0 ? `
        <div class="glass p-4 rounded-xl mb-4">
            <div class="text-[10px] text-muted uppercase font-bold mb-2">Варианты (${p.variants.length})</div>
            ${p.variants.slice(0, 3).map(v => `
                <div class="flex justify-between items-center py-2 border-b border-white/5 last:border-0">
                    <div class="text-xs text-white">
                        ${[v.size, v.color].filter(Boolean).join(' • ') || '—'}
                    </div>
                    <div class="text-xs font-bold text-accent">${v.total_quantity} шт</div>
                </div>
            `).join('')}
            ${p.variants.length > 3 ? `<div class="text-[10px] text-muted text-center mt-2">+ ещё ${p.variants.length - 3}</div>` : ''}
        </div>
    ` : '';

    // Информация о товаре (страна, материал, бренд)
    const productInfo = [
        (p.brand && p.brand !== 'null') ? `<div class="text-xs"><span class="text-[9px] uppercase text-muted">Бренд:</span> ${p.brand}</div>` : '',
        (p.material && p.material !== 'null') ? `<div class="text-xs"><span class="text-[9px] uppercase text-muted">Материал:</span> ${p.material}</div>` : '',
        (p.country && p.country !== 'null') ? `<div class="text-xs"><span class="text-[9px] uppercase text-muted">Страна:</span> ${p.country}</div>` : '',
        (p.category && p.category !== '—') ? `<div class="text-xs"><span class="text-[9px] uppercase text-muted">Категория:</span> ${p.category}</div>` : ''
    ].filter(Boolean).join('');

    return `
        <div class="space-y-4 pb-8">
            ${badges}

            <div class="text-center">
                <img src="${p.photo || 'data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\" viewBox=\"0 0 200 200\"%3E%3Crect fill=\"%23e2e8f0\" width=\"200\" height=\"200\"/%3E%3Ctext fill=\"%2394a3b8\" font-family=\"Arial\" font-size=\"14\" x=\"50%25\" y=\"50%25\" text-anchor=\"middle\" dominant-baseline=\"middle\"%3EНет фото%3C/text%3E%3C/svg%3E'}"
                     class="w-40 h-40 object-cover rounded-2xl mx-auto mb-3 border-2 border-white/10">
                <h3 class="text-lg font-black text-white mb-1">${p.name}</h3>
                <p class="text-xs text-muted">Артикул: <span class="font-mono text-accent">${p.article}</span></p>
            </div>

            ${productInfo ? `<div class="glass p-3 rounded-xl mb-3">${productInfo}</div>` : ''}

            <div class="grid grid-cols-2 gap-2">
                <div class="glass p-3 rounded-xl text-center">
                    <div class="text-[9px] text-muted uppercase">Розница</div>
                    <div class="text-sm font-black text-success">${formatPrice(p.retail_price)}</div>
                </div>
                <div class="glass p-3 rounded-xl text-center">
                    <div class="text-[9px] text-muted uppercase">Закупка</div>
                    <div class="text-sm font-black text-warning">${formatPrice(p.supplier_price || p.wholesale_price || 0)}</div>
                </div>
            </div>

            <div class="glass p-4 rounded-xl">
                <div class="text-[9px] text-muted uppercase mb-2">Остатки</div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-muted">Всего:</span>
                    <span class="text-lg font-black text-white">${s.total_stock || 0} шт</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="glass p-2 rounded-lg text-center">
                        <div class="text-[9px] text-muted">Витрина</div>
                        <div class="text-sm font-bold text-success">${s.shop_stock || 0}</div>
                    </div>
                    <div class="glass p-2 rounded-lg text-center">
                        <div class="text-[9px] text-muted">Склад</div>
                        <div class="text-sm font-bold text-warning">${s.warehouse_stock || 0}</div>
                    </div>
                </div>
            </div>

            ${variantsHTML}

            <a href="/admin/products/edit/${p.id}" class="block w-full bg-accent hover:bg-accent-hover text-bg text-center py-4 rounded-2xl font-black transition-all">
                <i class="bi bi-pencil-square"></i> Изменить товар
            </a>

            <button onclick="closeMobileResult()" class="w-full glass py-4 rounded-2xl font-bold text-white transition-all">
                <i class="bi bi-camera"></i> Сканировать снова
            </button>
        </div>
    `;
}

function getFoundByText(foundBy) {
    const texts = {
        'article': '📝 Артикул',
        'barcode': '📊 Штрих-код',
        'json_article': '📱 QR (артикул)',
        'json_barcode': '📱 QR (штрих-код)'
    };
    return texts[foundBy] || '🔍 Поиск';
}

function formatPrice(price) {
    const num = parseFloat(price) || 0;
    return num.toFixed(2) + ' сом';
}

function showNotFound(qrData) {
    const content = document.getElementById('mobile-result-content');
    content.innerHTML = `
        <div class="text-center py-12">
            <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-muted/30 mx-auto mb-4">
                <i class="bi bi-x-circle text-5xl"></i>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Товар не найден</h3>
            <p class="text-sm text-muted mb-4">
                Отсканированные данные:<br>
                <code class="text-xs bg-white/5 px-2 py-1 rounded mt-1 block">${qrData}</code>
            </p>
            <button onclick="closeMobileResult(); resetScannerUI(); startScanning();"
                    class="bg-accent hover:bg-accent-hover text-bg px-6 py-3 rounded-xl font-bold w-full">
                <i class="bi bi-camera"></i> Сканировать снова
            </button>
        </div>
    `;
    const overlay = document.getElementById('mobile-result-overlay');
    overlay.classList.remove('hidden');
    overlay.classList.add('mobile-overlay-visible');
    overlay.style.display = 'block';
    setTimeout(() => document.getElementById('mobile-drawer').classList.remove('translate-y-full'), 10);
}

function clearResult() {
    document.getElementById('scan-result').classList.add('hidden');
    document.getElementById('result-placeholder').classList.remove('hidden');
    resetScannerUI();
    startScanning();
}

function closeMobileResult() {
    const drawer = document.getElementById('mobile-drawer');
    const overlay = document.getElementById('mobile-result-overlay');
    
    // Закрываем drawer
    drawer.classList.add('translate-y-full');
    
    setTimeout(() => {
        overlay.style.display = 'none';
        overlay.classList.remove('mobile-overlay-visible');
        overlay.classList.add('hidden');

        // Сбрасываем всё
        lastScannedCode = '';
        scanInFlight = false;
        isScanning = false;
        
        // Запускаем камеру сразу
        startScanning();
    }, 250);
}

function playBeep() {
    // Нежный тихий звук через Web Audio API
    try {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        // Мягкий сигнал (800Hz, 100ms) - синусоида для нежного звука
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        // Тихая громкость (0.3 = 30%)
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
    } catch (e) {
        // Fallback на обычный аудио элемент
        const audio = document.getElementById('scan-sound');
        if (audio) {
            audio.volume = 0.3;
            audio.play().catch(() => {});
        }
    }
}
</script>
@endsection
