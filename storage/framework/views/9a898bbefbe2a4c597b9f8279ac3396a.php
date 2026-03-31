<?php $__env->startSection('page-title', 'Сканер товаров'); ?>
<?php $__env->startSection('page-subtitle', 'Мгновенный поиск товаров по QR и штрих-кодам'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ===== MOBILE: fullscreen immersive scanner ===== */
@media (max-width: 768px) {
    body {
        padding-bottom: 0 !important;
        overflow: hidden !important;
        position: fixed !important;
        width: 100% !important;
        height: 100% !important;
    }
    .sidebar { transform: translateX(-100%) !important; }
    .sidebar.show { transform: translateX(0) !important; }
    .main-content {
        margin-left: 0 !important;
        padding-top: 0 !important;
    }
    .topbar { display: none !important; }
    .safe-area-bottom,
    .md\:hidden.fixed.bottom-0 { display: none !important; }
    .main-content > div { padding: 0 !important; }
}

@media (min-width: 769px) {
    #mobile-result-overlay { display: none !important; }
    #mobile-back-btn { display: none !important; }
    #mobile-flash-btn { display: none !important; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto qr-scan-page">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Scanner Column -->
        <div class="lg:col-span-7 space-y-6 scanner-col">
            <div id="scanner-wrapper" class="glass overflow-visible rounded-[2rem] relative aspect-[4/5] md:aspect-square bg-black shadow-2xl border-white/10">
                <!-- Camera Viewport -->
                <div id="scanner-container" class="absolute inset-0 rounded-[2rem] overflow-hidden"></div>

                <!-- Overlay Gradients -->
                <div class="scanner-overlay absolute inset-0 pointer-events-none bg-gradient-to-t from-black/60 via-transparent to-black/30 rounded-[2rem]" style="opacity:1;"></div>

                <!-- Scanning Frame -->
                <div class="scanner-overlay absolute inset-0 flex items-center justify-center pointer-events-none rounded-[2rem]" style="opacity:1;">
                    <div id="scan-frame" class="w-64 h-64 md:w-80 md:h-80 border-2 border-white/20 rounded-[2rem] relative">
                        <div class="absolute -top-1 -left-1 w-12 h-12 border-t-4 border-l-4 border-accent rounded-tl-3xl"></div>
                        <div class="absolute -top-1 -right-1 w-12 h-12 border-t-4 border-r-4 border-accent rounded-tr-3xl"></div>
                        <div class="absolute -bottom-1 -left-1 w-12 h-12 border-b-4 border-l-4 border-accent rounded-bl-3xl"></div>
                        <div class="absolute -bottom-1 -right-1 w-12 h-12 border-b-4 border-r-4 border-accent rounded-br-3xl"></div>
                        <div class="absolute left-4 right-4 h-1 bg-accent/50 shadow-[0_0_15px_rgba(56,189,248,0.8)] animate-[scan_2s_ease-in-out_infinite] top-0 opacity-0"></div>
                    </div>
                </div>

                <!-- Mobile: Back Button -->
                <button id="mobile-back-btn" onclick="window.location.href='<?php echo e(route('admin.dashboard')); ?>'"
                    class="absolute top-4 left-4 w-11 h-11 rounded-full bg-black/50 backdrop-blur-md flex items-center justify-center text-white border border-white/10 active:scale-90 transition-transform"
                    style="z-index:35; display:none;">
                    <i class="bi bi-chevron-left text-lg"></i>
                </button>

                <!-- Top-right controls (flex row — no overlap) -->
                <div class="absolute top-4 right-4 flex items-center gap-2" style="z-index:35;">
                    <button id="mobile-flash-btn" onclick="toggleFlash()"
                        class="w-11 h-11 rounded-full bg-black/50 backdrop-blur-md flex items-center justify-center text-white border border-white/10 active:scale-90 transition-transform"
                        style="display:none;">
                        <i class="bi bi-lightning text-lg" id="flash-icon"></i>
                    </button>
                    <div id="scan-indicator" class="hidden">
                        <div class="bg-success text-bg px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 shadow-lg animate-pulse">
                            <span class="w-2 h-2 bg-bg rounded-full animate-ping"></span> Активно
                        </div>
                    </div>
                </div>

                <!-- Manual Input -->
                <div class="absolute bottom-24 left-4 right-4 md:left-6 md:right-6" style="z-index:30;">
                    <div class="glass p-2 rounded-2xl flex items-center gap-2 border-white/20 shadow-xl">
                        <input type="text" id="manual-article" placeholder="Введите артикул..."
                            class="flex-1 bg-transparent border-none text-white placeholder-white/40 px-4 py-2 focus:ring-0 text-sm"
                            inputmode="text" autocomplete="off" autocorrect="off" spellcheck="false"
                            onkeypress="if(event.key==='Enter') lookupManualArticle()">
                        <button onclick="lookupManualArticle()" class="w-10 h-10 rounded-xl bg-accent text-bg flex items-center justify-center hover:scale-95 active:scale-90 transition-transform">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Scanner Controls -->
                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-3 px-4 md:px-6" style="z-index:30;">
                    <button id="start-scan" class="flex-1 glass py-4 rounded-2xl flex items-center justify-center gap-3 font-bold text-white hover:bg-white/10 active:scale-95 transition-all border-white/20">
                        <i class="bi bi-camera-fill text-xl text-accent"></i> Запустить камеру
                    </button>
                    <button id="stop-scan" class="hidden flex-1 glass py-4 rounded-2xl flex items-center justify-center gap-3 font-bold text-danger hover:bg-danger/10 active:scale-95 transition-all border-danger/20">
                        <i class="bi bi-stop-circle-fill text-xl"></i> Остановить
                    </button>
                </div>
            </div>

            <!-- Hint Card (desktop only) -->
            <div class="glass p-6 rounded-3xl border-white/5 flex items-center gap-4 hidden md:flex hint-card">
                <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-accent">
                    <i class="bi bi-info-circle text-xl"></i>
                </div>
                <p class="text-xs text-muted leading-relaxed">
                    Наведите камеру на QR-код или штрих-код товара. Система автоматически распознает его и отобразит информацию о цене, остатках и характеристиках.
                </p>
            </div>
        </div>

        <!-- Result Column (desktop) -->
        <div class="lg:col-span-5 hidden lg:block">
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
                        <div class="absolute top-4 left-4 flex gap-2" id="badges-container"></div>
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
                    <div id="variants-list" class="space-y-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Bottom-Sheet Result Overlay -->
<div id="mobile-result-overlay" class="fixed inset-0" style="display:none; z-index:9999;">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeMobileResult()" id="mobile-backdrop"></div>
    <div id="mobile-drawer"
         class="absolute bottom-0 left-0 right-0 rounded-t-[2rem] border-t border-white/10 overflow-hidden transform translate-y-full transition-transform duration-300 ease-out"
         style="z-index:10000; max-height:92vh;">
        <div class="sticky top-0 pt-3 pb-2 flex justify-center cursor-grab" id="drawer-handle" style="background:inherit; z-index:1;">
            <div class="w-10 h-1 bg-white/30 rounded-full"></div>
        </div>
        <div id="mobile-result-content" class="px-5 pb-8 overflow-y-auto" style="max-height:calc(92vh - 36px); -webkit-overflow-scrolling:touch;"></div>
    </div>
</div>

<style>
@keyframes scan {
    0% { top: 0; opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}
@keyframes scan-success {
    0% { transform: scale(1); }
    50% { transform: scale(1.03); }
    100% { transform: scale(1); }
}
@keyframes scanning-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.4); }
    50% { box-shadow: 0 0 0 20px rgba(56, 189, 248, 0); }
}
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.glass {
    background: rgba(30, 41, 59, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
}

.scanner-active #scanner-container { animation: scanning-pulse 2s infinite; }
.scanner-success { animation: scan-success 0.3s ease-in-out; }

#scanner-container {
    background: #000;
    border-radius: 2rem;
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    overflow: hidden;
    z-index: 0 !important;
}
#scanner-container video,
#scanner-container .__scanner video,
#scanner-container video#__scan_video {
    width: 100% !important; height: 100% !important;
    min-height: 100% !important; min-width: 100% !important;
    object-fit: cover;
    border-radius: 2rem;
    position: absolute !important;
    top: 0 !important; left: 0 !important;
    z-index: 10 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}
#scanner-container .__scanner {
    width: 100% !important; height: 100% !important;
    position: absolute !important;
    inset: 0 !important;
    z-index: 0 !important;
    background: #000 !important;
}
.scanner-overlay { z-index: 20 !important; }

@media (min-width: 769px) {
    #scanner-wrapper { min-height: 500px !important; }
    #scanner-container { height: 100% !important; min-height: 500px !important; }
}

@media (max-width: 768px) {
    .qr-scan-page { max-width: 100% !important; padding: 0 !important; }
    .qr-scan-page > .grid { gap: 0 !important; }

    .scanner-col {
        grid-column: span 12 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .scanner-col > .space-y-6 { gap: 0 !important; }

    #scanner-wrapper {
        height: 100dvh !important;
        width: 100vw !important;
        border-radius: 0 !important;
        aspect-ratio: unset !important;
        margin: 0 !important;
    }
    #scanner-container {
        height: 100dvh !important;
        width: 100vw !important;
        border-radius: 0 !important;
        min-height: unset !important;
    }
    #scanner-container video,
    #scanner-container .__scanner video,
    #scanner-container video#__scan_video {
        border-radius: 0 !important;
    }
    .scanner-overlay { border-radius: 0 !important; }

    #mobile-back-btn,
    #mobile-flash-btn { display: flex !important; }

    #scan-frame { width: 220px !important; height: 220px !important; }

    .absolute.bottom-24 { bottom: 100px !important; left: 16px !important; right: 16px !important; }
    .absolute.bottom-4 {
        bottom: 16px !important;
        padding-bottom: env(safe-area-inset-bottom, 0px) !important;
    }

    .hint-card { display: none !important; }
}

#mobile-drawer {
    background: rgba(15, 23, 42, 0.97);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-color: rgba(56, 189, 248, 0.15);
}
#mobile-result-overlay.active { display: block !important; }
#mobile-result-overlay.active #mobile-drawer { transform: translateY(0) !important; }
#mobile-backdrop { opacity: 0; transition: opacity 0.3s ease; }
#mobile-result-overlay.active #mobile-backdrop { opacity: 1; }
</style>

<audio id="scan-sound" preload="auto" volume="1.0">
    <source src="data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAADhAC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7////////////////////////////////////////////////////////////AAAAAExhdmM1OC4xMzQAAAAAAAAAAAAAAAAkAAAAAAAAA4S/T4LKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/7kGQAD/AAAGkAAAAIAAANIAAAAQAAAaQAAAAgAAA0gAAABExBTUUzLjEwMFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV//uQZAoP8AAAaQAAAAgAAA0gAAABAAABpAAAACAAADSAAAAEVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV" type="audio/mpeg">
</audio>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
    'use strict';

    // ===================== STATE =====================
    let html5QrCode = null;       // Единственный экземпляр сканера (переиспользуется)
    let isScanning = false;
    let isStopping = false;       // Защита от двойного stop()
    let isClosingDrawer = false;  // Защита от двойного close drawer
    let lastScanTime = 0;
    let lastScannedCode = '';
    let scanInFlight = false;
    let audioContext = null;
    let flashOn = false;
    let scanCount = 0;            // Счётчик для мониторинга утечек

    const SCAN_COOLDOWN = 500;    // 500ms между сканами (защита от дребезга)
    const MAX_SCAN_COUNT = 5000;  // Принудительный перезапуск после N сканов

    // Сохраняем оригинальный HTML десктопного scan-result для восстановления
    const desktopScanResultOriginalHTML = document.getElementById('scan-result')
        ? document.getElementById('scan-result').innerHTML
        : '';

    function isMobile() {
        return window.matchMedia('(max-width: 768px)').matches;
    }

    // ===================== CAMERA =====================
    async function startScanning() {
        if (isScanning || isStopping) return;
        isScanning = true;
        scanInFlight = false;

        document.getElementById('start-scan').classList.add('hidden');
        document.getElementById('stop-scan').classList.remove('hidden');
        document.getElementById('scan-indicator').classList.remove('hidden');
        document.getElementById('scanner-wrapper').classList.add('scanner-active');

        // Переиспользуем экземпляр или создаём новый
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('scanner-container');
        }

        const container = document.getElementById('scanner-container');
        const containerSize = Math.min(container.clientWidth, container.clientHeight);
        const qrBoxSize = Math.max(150, Math.floor(containerSize * 0.65));

        const cameraConfig = isMobile()
            ? { facingMode: 'environment' }
            : { facingMode: 'user' };

        try {
            await html5QrCode.start(
                cameraConfig,
                {
                    fps: 10,
                    qrbox: { width: qrBoxSize, height: qrBoxSize },
                    aspectRatio: isMobile() ? window.innerHeight / window.innerWidth : 1,
                    disableFlip: false
                },
                onScanSuccess,
                onScanError
            );
        } catch (err) {
            console.error('Scanner start error:', err);
            isScanning = false;
            resetScannerUI();

            // Если камера занята или ошибка — пересоздаём экземпляр
            try {
                if (html5QrCode) {
                    await html5QrCode.clear();
                }
            } catch(e) {}
            html5QrCode = null;

            alert('Не удалось запустить камеру. Проверьте разрешения.');
        }
    }

    function resetScannerUI() {
        isScanning = false;
        isStopping = false;
        flashOn = false;
        updateFlashIcon();
        document.getElementById('start-scan').classList.remove('hidden');
        document.getElementById('stop-scan').classList.add('hidden');
        document.getElementById('scan-indicator').classList.add('hidden');
        document.getElementById('scanner-wrapper').classList.remove('scanner-active');
    }

    async function stopScanning() {
        if (!html5QrCode || !isScanning || isStopping) return;
        isStopping = true;

        try {
            await html5QrCode.stop();
        } catch (e) {
            // Камера уже остановлена — игнорируем
            console.warn('Stop warning:', e.message || e);
        }

        // НЕ вызываем clear() — переиспользуем экземпляр
        // clear() уничтожает внутренний контейнер и создаёт утечку DOM при пересоздании
        resetScannerUI();
    }

    document.getElementById('start-scan').addEventListener('click', startScanning);
    document.getElementById('stop-scan').addEventListener('click', stopScanning);

    // ===================== FLASH =====================
    async function toggleFlash() {
        if (!html5QrCode || !isScanning) return;
        try {
            const caps = html5QrCode.getRunningTrackCameraCapabilities();
            if (caps && caps.torchFeature && caps.torchFeature().isSupported()) {
                flashOn = !flashOn;
                await caps.torchFeature().apply(flashOn);
                updateFlashIcon();
            }
        } catch (e) {
            console.log('Flash not available');
        }
    }

    function updateFlashIcon() {
        const icon = document.getElementById('flash-icon');
        if (!icon) return;
        icon.className = flashOn ? 'bi bi-lightning-fill text-warning text-lg' : 'bi bi-lightning text-lg';
    }

    // ===================== SCAN HANDLER =====================
    async function onScanSuccess(decodedText) {
        const now = Date.now();

        // Дедупликация: тот же код ИЛИ слишком быстро
        if (now - lastScanTime < SCAN_COOLDOWN) return;
        if (decodedText === lastScannedCode && now - lastScanTime < 2000) return;
        if (scanInFlight) return;

        lastScanTime = now;
        lastScannedCode = decodedText;
        scanInFlight = true;
        scanCount++;

        playBeep();
        if (navigator.vibrate) navigator.vibrate([50, 30, 50]);

        // Визуальный пульс
        const wrapper = document.getElementById('scanner-wrapper');
        wrapper.classList.add('scanner-success');
        setTimeout(() => wrapper.classList.remove('scanner-success'), 150);

        try {
            await stopScanning();
            await fetchProductInfo(decodedText);
        } catch (e) {
            console.error('Processing error:', e);
            showNotFound(decodedText);
        } finally {
            scanInFlight = false;
            lastScannedCode = '';
        }

        // Периодическая очистка для долгих сессий
        if (scanCount % 100 === 0) {
            cleanupMemory();
        }

        // Принудительный перезапуск при очень долгих сессиях
        if (scanCount >= MAX_SCAN_COUNT) {
            console.log('Max scan count reached, recreating scanner instance');
            try { if (html5QrCode) await html5QrCode.clear(); } catch(e) {}
            html5QrCode = null;
            scanCount = 0;
        }
    }

    function onScanError(error) {
        // Игнорируем — вызывается на каждый кадр без QR
    }

    // ===================== API =====================
    async function fetchProductInfo(qrData) {
        const apiUrl = '<?php echo e(route("admin.qr.process")); ?>'.replace('http://', 'https://');

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 10000); // 10s таймаут

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_data: qrData }),
                signal: controller.signal
            });

            clearTimeout(timeout);

            if (!response.ok) {
                // Сервер вернул ошибку (500, 419 и т.д.)
                if (response.status === 419) {
                    // CSRF токен истёк — перезагрузить страницу
                    alert('Сессия истекла. Страница будет перезагружена.');
                    window.location.reload();
                    return;
                }
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                displayProduct(data);
            } else {
                showNotFound(qrData);
            }
        } catch (err) {
            clearTimeout(timeout);
            if (err.name === 'AbortError') {
                console.error('Request timeout');
                showError('Превышено время ожидания. Проверьте интернет.');
            } else {
                console.error('Fetch error:', err);
                showNotFound(qrData);
            }
        }
    }

    async function lookupManualArticle() {
        const input = document.getElementById('manual-article');
        const article = input.value.trim();
        if (!article) return;
        input.blur();
        if (isScanning) await stopScanning();
        scanInFlight = true;
        try {
            await fetchProductInfo(article);
        } finally {
            scanInFlight = false;
        }
    }

    // ===================== DISPLAY =====================
    function displayProduct(data) {
        const p = data.product;
        const s = data.summary;

        if (isMobile()) {
            showMobileDrawer(buildProductHTML(p, s, data));
        } else {
            // Desktop: восстанавливаем структуру если она была заменена showNotFound
            const scanResult = document.getElementById('scan-result');
            if (!document.getElementById('product-name')) {
                scanResult.innerHTML = desktopScanResultOriginalHTML;
            }

            document.getElementById('result-placeholder').classList.add('hidden');
            scanResult.classList.remove('hidden');

            document.getElementById('product-name').textContent = p.name;
            document.getElementById('product-article').textContent = p.article;
            document.getElementById('product-retail-price').textContent = formatPrice(p.retail_price);
            document.getElementById('product-profit').textContent = formatPrice((p.retail_price || 0) - (p.supplier_price || 0));
            document.getElementById('total-stock').textContent = s.total_stock + ' шт';
            document.getElementById('showcase-stock').textContent = s.shop_stock + ' шт';
            document.getElementById('warehouse-stock').textContent = s.warehouse_stock + ' шт';
            document.getElementById('edit-product-btn').href = '/admin/products/edit/' + p.id;

            const photo = document.getElementById('product-photo');
            photo.src = p.photo || '';
            photo.style.display = p.photo ? '' : 'none';

            const badgesContainer = document.getElementById('badges-container');
            badgesContainer.innerHTML = `
                <span class="px-3 py-1 bg-success/20 text-success text-[10px] font-black uppercase rounded-lg border border-success/30">
                    ${data.is_known_qr ? '✓ Наш QR' : '⚠ Товар из базы'}
                </span>
                <span class="px-3 py-1 bg-white/10 text-white/70 text-[10px] font-bold uppercase rounded-lg">
                    ${getFoundByText(data.found_by)}
                </span>
            `;

            // Variants
            const variantsList = document.getElementById('variants-list');
            variantsList.innerHTML = (data.variants || []).map(v => `
                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                    <div>
                        <span class="text-sm font-bold text-white">${escapeHtml([v.size, v.color].filter(Boolean).join(' • ') || '—')}</span>
                        ${v.barcode ? `<span class="text-[10px] text-muted ml-2 font-mono">${escapeHtml(v.barcode)}</span>` : ''}
                    </div>
                    <span class="text-sm font-black text-accent">${parseInt(v.total_quantity) || 0} шт</span>
                </div>
            `).join('');
        }
    }

    function buildProductHTML(p, s, data) {
        const badges = data ? `
            <div class="flex gap-2 mb-4 flex-wrap">
                <span class="px-3 py-1.5 bg-success/20 text-success text-[10px] font-black uppercase rounded-xl border border-success/30">
                    ${data.is_known_qr ? '✓ Наш QR' : '⚠ Из базы'}
                </span>
                <span class="px-3 py-1.5 bg-white/10 text-white/70 text-[10px] font-bold uppercase rounded-xl">
                    ${getFoundByText(data.found_by)}
                </span>
            </div>
        ` : '';

        const variantsHTML = p.variants && p.variants.length > 0 ? `
            <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                <div class="text-[10px] text-muted uppercase font-bold mb-3 flex items-center gap-2">
                    <i class="bi bi-layers text-accent"></i> Варианты (${p.variants.length})
                </div>
                <div class="space-y-2">
                    ${p.variants.map(v => `
                        <div class="flex justify-between items-center py-2.5 px-3 rounded-xl bg-white/5 border border-white/5">
                            <div>
                                <span class="text-sm text-white font-medium">${escapeHtml([v.size, v.color].filter(Boolean).join(' • ') || '—')}</span>
                                ${v.barcode ? `<div class="text-[10px] text-muted font-mono mt-0.5">${escapeHtml(v.barcode)}</div>` : ''}
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-black text-accent">${parseInt(v.total_quantity) || 0} шт</div>
                                <div class="text-[9px] text-muted">${parseInt(v.showcase_quantity) || 0}📍 ${parseInt(v.warehouse_quantity) || 0}📦</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        ` : '';

        const productInfo = [
            (p.brand && p.brand !== 'null') ? `<span class="inline-flex items-center gap-1 px-2 py-1 bg-white/5 rounded-lg text-xs"><i class="bi bi-star text-accent text-[10px]"></i> ${escapeHtml(p.brand)}</span>` : '',
            (p.category && p.category !== '—') ? `<span class="inline-flex items-center gap-1 px-2 py-1 bg-white/5 rounded-lg text-xs"><i class="bi bi-tag text-accent text-[10px]"></i> ${escapeHtml(p.category)}</span>` : ''
        ].filter(Boolean).join(' ');

        return `
            <div class="space-y-4 pb-4">
                ${badges}
                <div class="flex items-start gap-4">
                    <img src="${p.photo || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22 viewBox=%220 0 200 200%22%3E%3Crect fill=%22%231e293b%22 width=%22200%22 height=%22200%22/%3E%3Ctext fill=%22%2394a3b8%22 font-family=%22Arial%22 font-size=%2214%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EНет фото%3C/text%3E%3C/svg%3E'}"
                         class="w-24 h-24 object-cover rounded-2xl border border-white/10 flex-shrink-0"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect fill=%22%231e293b%22 width=%22200%22 height=%22200%22/%3E%3C/svg%3E'">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-black text-white leading-tight mb-1">${escapeHtml(p.name)}</h3>
                        <p class="text-xs text-muted mb-2">Артикул: <span class="font-mono text-accent">${escapeHtml(p.article)}</span></p>
                        ${productInfo ? `<div class="flex flex-wrap gap-1">${productInfo}</div>` : ''}
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-white/5 rounded-2xl p-3 text-center border border-white/5">
                        <div class="text-[9px] text-muted uppercase font-bold">Розница</div>
                        <div class="text-base font-black text-success mt-1">${formatPrice(p.retail_price)}</div>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-3 text-center border border-white/5">
                        <div class="text-[9px] text-muted uppercase font-bold">Закупка</div>
                        <div class="text-base font-black text-warning mt-1">${formatPrice(p.supplier_price || 0)}</div>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-3 text-center border border-white/5">
                        <div class="text-[9px] text-muted uppercase font-bold">Прибыль</div>
                        <div class="text-base font-black text-accent mt-1">${formatPrice((p.retail_price || 0) - (p.supplier_price || 0))}</div>
                    </div>
                </div>

                <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs text-muted font-bold uppercase flex items-center gap-2">
                            <i class="bi bi-box-seam text-accent"></i> Остатки
                        </span>
                        <span class="text-xl font-black text-white">${parseInt(s.total_stock) || 0} <span class="text-xs text-muted font-normal">шт</span></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-white/5 rounded-xl p-2.5 text-center">
                            <div class="text-[9px] text-muted">📍 Витрина</div>
                            <div class="text-sm font-bold text-success">${parseInt(s.shop_stock) || 0}</div>
                        </div>
                        <div class="bg-white/5 rounded-xl p-2.5 text-center">
                            <div class="text-[9px] text-muted">📦 Склад</div>
                            <div class="text-sm font-bold text-warning">${parseInt(s.warehouse_stock) || 0}</div>
                        </div>
                        <div class="bg-white/5 rounded-xl p-2.5 text-center">
                            <div class="text-[9px] text-muted">🛒 Продано</div>
                            <div class="text-sm font-bold text-muted">${parseInt(s.sold) || 0}</div>
                        </div>
                    </div>
                </div>

                ${variantsHTML}

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <a href="/admin/products/edit/${parseInt(p.id)}"
                       class="bg-accent hover:bg-accent-hover text-bg text-center py-4 rounded-2xl font-black transition-all flex items-center justify-center gap-2 active:scale-95">
                        <i class="bi bi-pencil-square"></i> Изменить
                    </a>
                    <button onclick="window._scanner.closeMobileResult()"
                        class="bg-white/10 text-white text-center py-4 rounded-2xl font-bold transition-all flex items-center justify-center gap-2 active:scale-95 border border-white/10">
                        <i class="bi bi-qr-code-scan"></i> Ещё скан
                    </button>
                </div>
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
        return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' сом';
    }

    // XSS-защита для данных из БД
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ===================== NOT FOUND =====================
    function showNotFound(qrData) {
        const safeQrData = escapeHtml(qrData);
        const html = `
            <div class="text-center py-8">
                <div class="w-20 h-20 rounded-full bg-danger/10 flex items-center justify-center mx-auto mb-4 border border-danger/20">
                    <i class="bi bi-x-circle text-4xl text-danger"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Товар не найден</h3>
                <p class="text-sm text-muted mb-2">Отсканированные данные:</p>
                <code class="text-xs bg-white/5 px-3 py-2 rounded-xl block mb-6 text-accent font-mono break-all">${safeQrData}</code>
                <button onclick="window._scanner.closeMobileResult(); setTimeout(window._scanner.startScanning, 300);"
                        class="w-full bg-accent hover:bg-accent-hover text-bg py-4 rounded-2xl font-black active:scale-95 transition-all">
                    <i class="bi bi-qr-code-scan"></i> Сканировать снова
                </button>
            </div>
        `;

        if (isMobile()) {
            showMobileDrawer(html);
        } else {
            document.getElementById('result-placeholder').classList.add('hidden');
            const scanResult = document.getElementById('scan-result');
            scanResult.classList.remove('hidden');
            scanResult.innerHTML = `<div class="glass rounded-[2rem] p-8">${html}</div>`;
        }
    }

    function showError(message) {
        const html = `
            <div class="text-center py-8">
                <div class="w-20 h-20 rounded-full bg-warning/10 flex items-center justify-center mx-auto mb-4 border border-warning/20">
                    <i class="bi bi-wifi-off text-4xl text-warning"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Ошибка</h3>
                <p class="text-sm text-muted mb-6">${escapeHtml(message)}</p>
                <button onclick="window._scanner.closeMobileResult(); setTimeout(window._scanner.startScanning, 300);"
                        class="w-full bg-accent hover:bg-accent-hover text-bg py-4 rounded-2xl font-black active:scale-95 transition-all">
                    <i class="bi bi-arrow-clockwise"></i> Попробовать снова
                </button>
            </div>
        `;

        if (isMobile()) {
            showMobileDrawer(html);
        } else {
            document.getElementById('result-placeholder').classList.add('hidden');
            const scanResult = document.getElementById('scan-result');
            scanResult.classList.remove('hidden');
            scanResult.innerHTML = `<div class="glass rounded-[2rem] p-8">${html}</div>`;
        }
    }

    // ===================== MOBILE DRAWER =====================
    function showMobileDrawer(contentHTML) {
        const content = document.getElementById('mobile-result-content');
        // Очищаем старый контент перед вставкой (предотвращаем утечку DOM)
        content.innerHTML = '';
        content.innerHTML = contentHTML;
        // Прокручиваем контент наверх
        content.scrollTop = 0;

        const overlay = document.getElementById('mobile-result-overlay');
        const drawer = document.getElementById('mobile-drawer');

        isClosingDrawer = false;
        overlay.style.display = 'block';
        void overlay.offsetHeight; // Force reflow

        requestAnimationFrame(() => {
            overlay.classList.add('active');
            drawer.style.transform = 'translateY(0)';
        });
    }

    function closeMobileResult() {
        if (isClosingDrawer) return;
        isClosingDrawer = true;

        const overlay = document.getElementById('mobile-result-overlay');
        const drawer = document.getElementById('mobile-drawer');

        drawer.style.transform = 'translateY(100%)';

        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.classList.remove('active');

            // Очищаем DOM контент для экономии памяти
            document.getElementById('mobile-result-content').innerHTML = '';

            // Reset state
            lastScannedCode = '';
            scanInFlight = false;
            isScanning = false;
            isStopping = false;
            isClosingDrawer = false;

            // Restart camera
            startScanning();
        }, 300);
    }

    // ===== Swipe-to-dismiss drawer =====
    (function initDrawerSwipe() {
        let startY = 0, currentY = 0, isDragging = false;
        const drawer = document.getElementById('mobile-drawer');
        if (!drawer) return;

        drawer.addEventListener('touchstart', (e) => {
            if (isClosingDrawer) return;
            const scrollable = document.getElementById('mobile-result-content');
            if (scrollable && scrollable.scrollTop > 5 && !e.target.closest('#drawer-handle')) return;
            startY = e.touches[0].clientY;
            currentY = startY;
            isDragging = true;
            drawer.style.transition = 'none';
        }, { passive: true });

        drawer.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            currentY = e.touches[0].clientY;
            const diff = currentY - startY;
            if (diff > 0) {
                drawer.style.transform = `translateY(${diff}px)`;
            }
        }, { passive: true });

        drawer.addEventListener('touchend', () => {
            if (!isDragging) return;
            isDragging = false;
            drawer.style.transition = 'transform 0.3s ease-out';
            const diff = currentY - startY;
            if (diff > 120) {
                closeMobileResult();
            } else {
                drawer.style.transform = 'translateY(0)';
            }
            startY = 0;
            currentY = 0;
        }, { passive: true });
    })();

    // ===================== DESKTOP =====================
    function clearResult() {
        // Восстанавливаем оригинальную структуру
        const scanResult = document.getElementById('scan-result');
        scanResult.innerHTML = desktopScanResultOriginalHTML;
        scanResult.classList.add('hidden');
        document.getElementById('result-placeholder').classList.remove('hidden');
        resetScannerUI();
        startScanning();
    }

    // ===================== AUDIO =====================
    function playBeep() {
        try {
            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }

            // Если контекст suspended (iOS policy), resume
            if (audioContext.state === 'suspended') {
                audioContext.resume();
            }

            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            oscillator.frequency.value = 880;
            oscillator.type = 'sine';
            gainNode.gain.setValueAtTime(0.25, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.12);
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.12);

            // Отключаем ноды после воспроизведения (предотвращаем утечку)
            oscillator.onended = () => {
                oscillator.disconnect();
                gainNode.disconnect();
            };
        } catch (e) {
            const audio = document.getElementById('scan-sound');
            if (audio) { audio.volume = 0.3; audio.play().catch(() => {}); }
        }
    }

    // ===================== MEMORY CLEANUP =====================
    function cleanupMemory() {
        // Принудительная очистка отсоединённых DOM-узлов
        if (window.gc) {
            window.gc();
        }
        console.log(`Scanner: ${scanCount} scans completed, memory cleanup`);
    }

    // ===================== PAGE LIFECYCLE =====================
    // Останавливаем камеру при уходе со страницы
    window.addEventListener('beforeunload', () => {
        if (html5QrCode && isScanning) {
            try { html5QrCode.stop(); } catch(e) {}
        }
        if (audioContext) {
            try { audioContext.close(); } catch(e) {}
        }
    });

    // Останавливаем камеру при свёртывании вкладки (экономия батареи)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && isScanning) {
            stopScanning();
        }
    });

    // ===================== INITIALIZATION =====================
    document.addEventListener('DOMContentLoaded', () => {
        if (isMobile()) {
            setTimeout(startScanning, 500);
        }
    });

    // Expose needed functions globally for onclick handlers
    window._scanner = {
        startScanning,
        stopScanning,
        closeMobileResult,
        clearResult,
        toggleFlash,
        lookupManualArticle
    };

    // Backward-compatible global references for inline handlers
    window.closeMobileResult = closeMobileResult;
    window.clearResult = clearResult;
    window.lookupManualArticle = lookupManualArticle;
    window.startScanning = startScanning;
    window.toggleFlash = toggleFlash;

})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/qr/scan.blade.php ENDPATH**/ ?>