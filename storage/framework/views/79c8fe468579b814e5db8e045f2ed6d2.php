<?php $__env->startSection('page-title', 'Сканер товаров'); ?>
<?php $__env->startSection('page-subtitle', 'Мгновенный поиск по QR-коду и штрих-коду'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ════════════════════════════════════════════
   MOBILE: fullscreen immersive scanner
   Скрываем ВСЁ кроме сканера
════════════════════════════════════════════ */
@media (max-width: 768px) {
    body {
        overflow: hidden !important;
        position: fixed !important;
        width: 100% !important;
        height: 100% !important;
        padding: 0 !important; /* override layout padding */
    }
    /* Hide header, sidebar, bottom nav, page-title area */
    .mobile-header,
    .mobile-bottom-nav,
    #sidebar,
    #mobile-overlay,
    .page-title-mobile,
    .page-actions-mobile { display: none !important; }

    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .main-content > .max-w-7xl {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    /* The page title div is already hidden via .page-title-mobile */

    /* Scanner goes edge-to-edge */
    .qr-scan-page { max-width: 100% !important; padding: 0 !important; }
    .qr-scan-page > .grid { gap: 0 !important; }

    .scanner-col {
        grid-column: span 12 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

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
    }
    #scanner-container video,
    #scanner-container .__scanner video {
        border-radius: 0 !important;
    }
    .scanner-overlay { border-radius: 0 !important; }

    /* Show mobile-only controls */
    #mobile-back-btn,
    #mobile-flash-btn { display: flex !important; }

    #scan-frame { width: 220px !important; height: 220px !important; }
    .hint-card  { display: none !important; }

    /* Manual input and buttons positioning */
    .manual-input-wrap  { bottom: 100px !important; left: 16px !important; right: 16px !important; }
    .scanner-btns-wrap  { bottom: 16px !important; padding-bottom: env(safe-area-inset-bottom, 0px) !important; }
}

/* ── Hide mobile-specific elements on desktop ── */
@media (min-width: 769px) {
    #mobile-result-overlay { display: none !important; }
    #mobile-back-btn  { display: none !important; }
    #mobile-flash-btn { display: none !important; }
}

/* ════════════════════════════════════════════
   Scanner UI
════════════════════════════════════════════ */
@keyframes scan {
    0%   { top: 0;    opacity: 0; }
    10%  { opacity: 1; }
    90%  { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}
@keyframes scan-success {
    0%   { transform: scale(1); }
    50%  { transform: scale(1.03); }
    100% { transform: scale(1); }
}
@keyframes pulse-ring {
    0%,100% { box-shadow: 0 0 0 0 rgba(168,85,247,.4); }
    50%      { box-shadow: 0 0 0 18px rgba(168,85,247,0); }
}

.scanner-active #scanner-container { animation: pulse-ring 2s infinite; }
.scanner-success { animation: scan-success 0.25s ease-in-out; }

/* scanner-container always fills its parent */
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
#scanner-container .__scanner video {
    width: 100% !important; height: 100% !important;
    min-height: 100% !important; min-width: 100% !important;
    object-fit: cover;
    border-radius: 2rem;
    position: absolute !important;
    top: 0 !important; left: 0 !important;
    z-index: 10 !important;
}
#scanner-container .__scanner {
    width: 100% !important; height: 100% !important;
    position: absolute !important; inset: 0 !important;
    background: #000 !important;
}
.scanner-overlay { z-index: 20 !important; }

@media (min-width: 769px) {
    #scanner-wrapper  { min-height: 480px !important; }
    #scanner-container { height: 100% !important; min-height: 480px !important; }
}

/* Glass card inside scanner page */
.scan-glass {
    background: rgba(15,23,42,0.75);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}

/* Mobile drawer */
#mobile-drawer {
    background: rgba(10,16,35,0.97);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border-color: rgba(168,85,247,0.18);
}
#mobile-result-overlay.active { display: block !important; }
#mobile-result-overlay.active #mobile-drawer { transform: translateY(0) !important; }
#mobile-backdrop { opacity: 0; transition: opacity 0.3s ease; }
#mobile-result-overlay.active #mobile-backdrop { opacity: 1; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto qr-scan-page">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        
        <div class="lg:col-span-7 space-y-5 scanner-col">
            <div id="scanner-wrapper"
                 class="scan-glass overflow-visible rounded-[2rem] relative aspect-[4/5] md:aspect-square bg-black shadow-2xl">

                
                <div id="scanner-container" class="absolute inset-0 rounded-[2rem] overflow-hidden"></div>

                
                <div class="scanner-overlay absolute inset-0 pointer-events-none bg-gradient-to-t from-black/60 via-transparent to-black/30 rounded-[2rem]"></div>

                
                <div class="scanner-overlay absolute inset-0 flex items-center justify-center pointer-events-none rounded-[2rem]">
                    <div id="scan-frame" class="w-64 h-64 md:w-80 md:h-80 border-2 border-white/20 rounded-[2rem] relative">
                        <div class="absolute -top-1 -left-1 w-12 h-12 border-t-4 border-l-4 border-accent rounded-tl-3xl"></div>
                        <div class="absolute -top-1 -right-1 w-12 h-12 border-t-4 border-r-4 border-accent rounded-tr-3xl"></div>
                        <div class="absolute -bottom-1 -left-1 w-12 h-12 border-b-4 border-l-4 border-accent rounded-bl-3xl"></div>
                        <div class="absolute -bottom-1 -right-1 w-12 h-12 border-b-4 border-r-4 border-accent rounded-br-3xl"></div>
                        <div class="absolute left-4 right-4 h-0.5 bg-accent/60 shadow-[0_0_12px_rgba(168,85,247,0.9)] animate-[scan_2s_ease-in-out_infinite] top-0 opacity-0"></div>
                    </div>
                </div>

                
                <button id="mobile-back-btn"
                    onclick="window.location.href='<?php echo e(route('owner.dashboard')); ?>'"
                    class="absolute top-4 left-4 w-11 h-11 rounded-full bg-black/50 backdrop-blur-md flex items-center justify-center text-white border border-white/10 active:scale-90 transition-transform"
                    style="z-index:35; display:none;">
                    <i class="bi bi-chevron-left text-lg"></i>
                </button>

                
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

                
                <div class="absolute manual-input-wrap left-4 right-4 md:left-6 md:right-6" style="z-index:30; bottom:96px;">
                    <div class="scan-glass p-2 rounded-2xl flex items-center gap-2">
                        <input type="text" id="manual-article" placeholder="Введите артикул..."
                            class="flex-1 bg-transparent border-none text-white placeholder-white/40 px-4 py-2 focus:ring-0 text-sm"
                            inputmode="text" autocomplete="off" autocorrect="off" spellcheck="false"
                            onkeypress="if(event.key==='Enter') lookupManualArticle()">
                        <button onclick="lookupManualArticle()"
                            class="w-10 h-10 rounded-xl bg-accent text-white flex items-center justify-center hover:bg-accent-hover active:scale-90 transition-all">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                
                <div class="absolute scanner-btns-wrap left-0 right-0 flex justify-center gap-3 px-4 md:px-6" style="z-index:30; bottom:16px;">
                    <button id="start-scan"
                        class="flex-1 scan-glass py-4 rounded-2xl flex items-center justify-center gap-3 font-bold text-white hover:bg-white/10 active:scale-95 transition-all">
                        <i class="bi bi-camera-fill text-xl text-accent"></i> Запустить камеру
                    </button>
                    <button id="stop-scan"
                        class="hidden flex-1 scan-glass py-4 rounded-2xl flex items-center justify-center gap-3 font-bold text-danger hover:bg-danger/10 active:scale-95 transition-all border-danger/20">
                        <i class="bi bi-stop-circle-fill text-xl"></i> Остановить
                    </button>
                </div>
            </div>

            
            <div class="scan-glass p-5 rounded-3xl flex items-center gap-4 hint-card hidden md:flex">
                <div class="w-12 h-12 rounded-2xl bg-accent/10 flex items-center justify-center text-accent flex-shrink-0">
                    <i class="bi bi-info-circle text-xl"></i>
                </div>
                <p class="text-xs text-muted leading-relaxed">
                    Наведите камеру на QR-код или штрих-код товара. Система автоматически распознает его и покажет информацию о цене, остатках и вариантах.
                </p>
            </div>
        </div>

        
        <div class="lg:col-span-5 hidden lg:block">
            <div id="result-placeholder"
                 class="scan-glass p-12 rounded-[2rem] text-center border-dashed border-white/10 flex flex-col items-center justify-center min-h-[420px]">
                <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-muted/30 mb-5">
                    <i class="bi bi-qr-code text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Ожидание сканирования</h3>
                <p class="text-muted text-sm max-w-[200px] mx-auto leading-relaxed">Данные товара появятся здесь после считывания</p>
            </div>

            <div id="scan-result" class="hidden space-y-4">
                
                <div class="scan-glass rounded-[2rem] overflow-hidden shadow-2xl">
                    <div class="relative h-44 bg-white/5 flex items-center justify-center">
                        <img id="product-photo" src="" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-bg via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4 flex gap-2" id="badges-container"></div>
                    </div>
                    <div class="p-6">
                        <h2 id="product-name" class="text-2xl font-black text-white leading-tight mb-1 truncate"></h2>
                        <div class="flex items-center gap-2 text-muted text-xs mb-5">
                            <span class="font-bold text-accent uppercase tracking-tight">Артикул:</span>
                            <span id="product-article" class="font-mono"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-1">Розница</p>
                                <p id="product-retail-price" class="text-xl font-black text-success"></p>
                            </div>
                            <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-1">Прибыль</p>
                                <p id="product-profit" class="text-xl font-black text-accent"></p>
                            </div>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-between px-1">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-box-seam text-muted"></i>
                                    <span class="text-sm font-bold">Общий остаток</span>
                                </div>
                                <span id="total-stock" class="text-lg font-black text-white"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                                    <span class="text-[10px] font-bold text-muted uppercase">Витрина</span>
                                    <span id="showcase-stock" class="font-bold text-white text-sm"></span>
                                </div>
                                <div class="flex justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                                    <span class="text-[10px] font-bold text-muted uppercase">Склад</span>
                                    <span id="warehouse-stock" class="font-bold text-white text-sm"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <a id="edit-product-btn" href="#"
                               class="flex-1 bg-accent hover:bg-accent-hover text-white py-4 rounded-2xl flex items-center justify-center gap-2 font-black transition-all active:scale-95">
                                <i class="bi bi-pencil-square"></i> Изменить
                            </a>
                            <button onclick="clearResult()"
                                class="w-14 h-14 scan-glass rounded-2xl flex items-center justify-center text-muted hover:text-white transition-colors">
                                <i class="bi bi-arrow-clockwise text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="scan-glass p-5 rounded-[2rem]">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="bi bi-layers text-accent"></i> Варианты и штрих-коды
                    </h3>
                    <div id="variants-list" class="space-y-2"></div>
                </div>
            </div>
        </div>

    </div>
</div>


<div id="mobile-result-overlay" class="fixed inset-0" style="display:none; z-index:9999;">
    <div id="mobile-backdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeMobileResult()"></div>
    <div id="mobile-drawer"
         class="absolute bottom-0 left-0 right-0 rounded-t-[2rem] border-t border-white/10 overflow-hidden transform translate-y-full transition-transform duration-300 ease-out"
         style="z-index:10000; max-height:92dvh;">
        <div id="drawer-handle" class="sticky top-0 pt-3 pb-2 flex justify-center cursor-grab" style="background:inherit; z-index:1;">
            <div class="w-10 h-1 bg-white/30 rounded-full"></div>
        </div>
        <div id="mobile-result-content"
             class="px-5 pb-8 overflow-y-auto"
             style="max-height:calc(92dvh - 36px); -webkit-overflow-scrolling:touch;"></div>
    </div>
</div>

<audio id="scan-sound" preload="auto">
    <source src="data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA" type="audio/mpeg">
</audio>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function () {
    'use strict';

    // ── State ──────────────────────────────────────────────────────────
    let html5QrCode    = null;
    let isScanning     = false;
    let isStopping     = false;
    let isClosingDrawer = false;
    let lastScanTime   = 0;
    let lastScannedCode = '';
    let scanInFlight   = false;
    let audioCtx       = null;
    let flashOn        = false;
    let scanCount      = 0;

    const COOLDOWN      = 500;   // ms between scans
    const MAX_SCAN_CNT  = 5000;  // recreate instance after N scans

    // preserve original desktop result HTML for reset
    const origResultHTML = (document.getElementById('scan-result') || {}).innerHTML || '';

    const isMobile = () => window.matchMedia('(max-width: 768px)').matches;

    // ── Camera ────────────────────────────────────────────────────────
    async function startScanning() {
        if (isScanning || isStopping) return;
        isScanning   = true;
        scanInFlight = false;

        document.getElementById('start-scan').classList.add('hidden');
        document.getElementById('stop-scan').classList.remove('hidden');
        document.getElementById('scan-indicator').classList.remove('hidden');
        document.getElementById('scanner-wrapper').classList.add('scanner-active');

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('scanner-container');
        }

        const container = document.getElementById('scanner-container');
        const sz = Math.max(150, Math.floor(Math.min(container.clientWidth, container.clientHeight) * 0.65));

        try {
            await html5QrCode.start(
                { facingMode: isMobile() ? 'environment' : 'user' },
                {
                    fps: 10,
                    qrbox: { width: sz, height: sz },
                    aspectRatio: isMobile() ? (window.innerHeight / window.innerWidth) : 1,
                    disableFlip: false
                },
                onScanSuccess,
                () => {} // ignore frame errors
            );
        } catch (err) {
            console.error('Camera error:', err);
            isScanning = false;
            resetUI();
            try { if (html5QrCode) await html5QrCode.clear(); } catch(e) {}
            html5QrCode = null;
            alert('Не удалось запустить камеру. Проверьте разрешения.');
        }
    }

    function resetUI() {
        isScanning  = false;
        isStopping  = false;
        flashOn     = false;
        updateFlashIcon();
        document.getElementById('start-scan').classList.remove('hidden');
        document.getElementById('stop-scan').classList.add('hidden');
        document.getElementById('scan-indicator').classList.add('hidden');
        document.getElementById('scanner-wrapper').classList.remove('scanner-active');
    }

    async function stopScanning() {
        if (!html5QrCode || !isScanning || isStopping) return;
        isStopping = true;
        try { await html5QrCode.stop(); } catch(e) { /* already stopped */ }
        resetUI();
    }

    document.getElementById('start-scan').addEventListener('click', startScanning);
    document.getElementById('stop-scan').addEventListener('click', stopScanning);

    // ── Flash ──────────────────────────────────────────────────────────
    async function toggleFlash() {
        if (!html5QrCode || !isScanning) return;
        try {
            const caps = html5QrCode.getRunningTrackCameraCapabilities();
            if (caps && caps.torchFeature && caps.torchFeature().isSupported()) {
                flashOn = !flashOn;
                await caps.torchFeature().apply(flashOn);
                updateFlashIcon();
            }
        } catch(e) {}
    }
    function updateFlashIcon() {
        const ic = document.getElementById('flash-icon');
        if (ic) ic.className = flashOn
            ? 'bi bi-lightning-fill text-warning text-lg'
            : 'bi bi-lightning text-lg';
    }

    // ── Scan callback ──────────────────────────────────────────────────
    async function onScanSuccess(decoded) {
        const now = Date.now();
        if (now - lastScanTime < COOLDOWN) return;
        if (decoded === lastScannedCode && now - lastScanTime < 2000) return;
        if (scanInFlight) return;

        lastScanTime    = now;
        lastScannedCode = decoded;
        scanInFlight    = true;
        scanCount++;

        beep();
        if (navigator.vibrate) navigator.vibrate([50, 30, 50]);

        const wrap = document.getElementById('scanner-wrapper');
        wrap.classList.add('scanner-success');
        setTimeout(() => wrap.classList.remove('scanner-success'), 200);

        try {
            await stopScanning();
            await fetchProduct(decoded);
        } catch(e) {
            showNotFound(decoded);
        } finally {
            scanInFlight    = false;
            lastScannedCode = '';
        }

        if (scanCount % 100 === 0) console.log(`QR scans: ${scanCount}`);
        if (scanCount >= MAX_SCAN_CNT) {
            try { if (html5QrCode) await html5QrCode.clear(); } catch(e) {}
            html5QrCode = null;
            scanCount   = 0;
        }
    }

    // ── API ────────────────────────────────────────────────────────────
    async function fetchProduct(qrData) {
        const ctrl = new AbortController();
        const tid  = setTimeout(() => ctrl.abort(), 10000);

        try {
            const res = await fetch('<?php echo e(route("owner.scan-product.process")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_data: qrData }),
                signal: ctrl.signal
            });
            clearTimeout(tid);

            if (res.status === 419) { alert('Сессия истекла. Страница перезагрузится.'); window.location.reload(); return; }
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            data.success ? displayProduct(data) : showNotFound(qrData);
        } catch(e) {
            clearTimeout(tid);
            e.name === 'AbortError' ? showError('Таймаут. Проверьте соединение.') : showNotFound(qrData);
        }
    }

    async function lookupManualArticle() {
        const input   = document.getElementById('manual-article');
        const article = input.value.trim();
        if (!article) return;
        input.blur();
        if (isScanning) await stopScanning();
        scanInFlight = true;
        try { await fetchProduct(article); } finally { scanInFlight = false; }
    }

    // ── Display ────────────────────────────────────────────────────────
    function displayProduct(data) {
        const p = data.product, s = data.summary;

        if (isMobile()) {
            showDrawer(buildMobileHTML(p, s, data));
            return;
        }

        // Desktop
        const resultEl = document.getElementById('scan-result');
        if (!document.getElementById('product-name')) resultEl.innerHTML = origResultHTML;

        document.getElementById('result-placeholder').classList.add('hidden');
        resultEl.classList.remove('hidden');

        document.getElementById('product-name').textContent = p.name;
        document.getElementById('product-article').textContent = p.article;
        document.getElementById('product-retail-price').textContent = fmt(p.retail_price);
        document.getElementById('product-profit').textContent = fmt((p.retail_price || 0) - (p.supplier_price || 0));
        document.getElementById('total-stock').textContent = (s.total_stock || 0) + ' шт';
        document.getElementById('showcase-stock').textContent = (s.shop_stock || 0) + ' шт';
        document.getElementById('warehouse-stock').textContent = (s.warehouse_stock || 0) + ' шт';
        document.getElementById('edit-product-btn').href = '/owner/products/' + parseInt(p.id) + '/edit';

        const photo = document.getElementById('product-photo');
        photo.src = p.photo || ''; photo.style.display = p.photo ? '' : 'none';

        document.getElementById('badges-container').innerHTML = `
            <span class="px-3 py-1 bg-success/20 text-success text-[10px] font-black uppercase rounded-lg border border-success/30">
                ${data.is_known_qr ? '✓ Наш QR' : '⚠ Из базы'}
            </span>`;

        document.getElementById('variants-list').innerHTML = (data.variants || []).map(v => `
            <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                <div>
                    <span class="text-sm font-bold text-white">${esc([v.size, v.color].filter(Boolean).join(' • ') || '—')}</span>
                    ${v.barcode ? `<span class="text-[10px] text-muted ml-2 font-mono">${esc(v.barcode)}</span>` : ''}
                </div>
                <span class="text-sm font-black text-accent">${parseInt(v.total_quantity) || 0} шт</span>
            </div>`).join('');
    }

    function buildMobileHTML(p, s, data) {
        const variantsHTML = (p.variants && p.variants.length) ? `
            <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                <div class="text-[10px] text-muted uppercase font-bold mb-3 flex items-center gap-2">
                    <i class="bi bi-layers text-accent"></i> Варианты (${p.variants.length})
                </div>
                <div class="space-y-2">
                    ${p.variants.map(v => `
                        <div class="flex justify-between items-center py-2 px-3 rounded-xl bg-white/5">
                            <div>
                                <div class="text-sm text-white font-medium">${esc([v.size,v.color].filter(Boolean).join(' • ')||'—')}</div>
                                ${v.barcode ? `<div class="text-[10px] text-muted font-mono">${esc(v.barcode)}</div>` : ''}
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-black text-accent">${parseInt(v.total_quantity)||0} шт</div>
                                <div class="text-[9px] text-muted">${parseInt(v.showcase_quantity)||0}📍 ${parseInt(v.warehouse_quantity)||0}📦</div>
                            </div>
                        </div>`).join('')}
                </div>
            </div>` : '';

        return `<div class="space-y-4 pb-4">
            <div class="flex gap-2 mb-2 flex-wrap">
                <span class="px-3 py-1.5 bg-success/20 text-success text-[10px] font-black uppercase rounded-xl border border-success/30">
                    ${data.is_known_qr ? '✓ Наш QR' : '⚠ Из базы'}
                </span>
            </div>
            <div class="flex items-start gap-4">
                <img src="${p.photo || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%231e293b%22 width=%22100%22 height=%22100%22/%3E%3C/svg%3E'}"
                     class="w-24 h-24 object-cover rounded-2xl border border-white/10 flex-shrink-0"
                     onerror="this.style.display='none'">
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-black text-white leading-tight mb-1">${esc(p.name)}</h3>
                    <p class="text-xs text-muted mb-2">Арт: <span class="font-mono text-accent">${esc(p.article)}</span></p>
                    ${p.brand ? `<span class="text-xs bg-white/5 px-2 py-0.5 rounded-lg">${esc(p.brand)}</span>` : ''}
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div class="bg-white/5 rounded-2xl p-3 text-center border border-white/5">
                    <div class="text-[9px] text-muted uppercase font-bold">Розница</div>
                    <div class="text-sm font-black text-success mt-1">${fmt(p.retail_price)}</div>
                </div>
                <div class="bg-white/5 rounded-2xl p-3 text-center border border-white/5">
                    <div class="text-[9px] text-muted uppercase font-bold">Закупка</div>
                    <div class="text-sm font-black text-warning mt-1">${fmt(p.supplier_price||0)}</div>
                </div>
                <div class="bg-white/5 rounded-2xl p-3 text-center border border-white/5">
                    <div class="text-[9px] text-muted uppercase font-bold">Прибыль</div>
                    <div class="text-sm font-black text-accent mt-1">${fmt((p.retail_price||0)-(p.supplier_price||0))}</div>
                </div>
            </div>

            <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs text-muted font-bold uppercase flex items-center gap-2">
                        <i class="bi bi-box-seam text-accent"></i> Остатки
                    </span>
                    <span class="text-xl font-black text-white">${parseInt(s.total_stock)||0}<span class="text-xs text-muted font-normal ml-1">шт</span></span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-white/5 rounded-xl p-2">
                        <div class="text-[9px] text-muted">📍 Витрина</div>
                        <div class="text-sm font-bold text-success">${parseInt(s.shop_stock)||0}</div>
                    </div>
                    <div class="bg-white/5 rounded-xl p-2">
                        <div class="text-[9px] text-muted">📦 Склад</div>
                        <div class="text-sm font-bold text-warning">${parseInt(s.warehouse_stock)||0}</div>
                    </div>
                    <div class="bg-white/5 rounded-xl p-2">
                        <div class="text-[9px] text-muted">🛒 Продано</div>
                        <div class="text-sm font-bold text-muted">${parseInt(s.sold)||0}</div>
                    </div>
                </div>
            </div>

            ${variantsHTML}

            <div class="grid grid-cols-2 gap-3 pt-1">
                <a href="/owner/products/${parseInt(p.id)}/edit"
                   class="bg-accent hover:bg-accent-hover text-white text-center py-4 rounded-2xl font-black flex items-center justify-center gap-2 active:scale-95 transition-all">
                    <i class="bi bi-pencil-square"></i> Изменить
                </a>
                <button onclick="window._qr.closeDrawer()"
                    class="bg-white/10 text-white py-4 rounded-2xl font-bold flex items-center justify-center gap-2 active:scale-95 transition-all border border-white/10">
                    <i class="bi bi-qr-code-scan"></i> Ещё скан
                </button>
            </div>
        </div>`;
    }

    // ── Not found / error ──────────────────────────────────────────────
    function showNotFound(qrData) {
        const html = `
            <div class="text-center py-8">
                <div class="w-20 h-20 rounded-full bg-danger/10 flex items-center justify-center mx-auto mb-4 border border-danger/20">
                    <i class="bi bi-x-circle text-4xl text-danger"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Товар не найден</h3>
                <p class="text-xs text-muted mb-2">Отсканировано:</p>
                <code class="text-xs bg-white/5 px-3 py-2 rounded-xl block mb-6 text-accent font-mono break-all">${esc(qrData)}</code>
                <button onclick="window._qr.closeDrawer(); setTimeout(window._qr.start, 300)"
                        class="w-full bg-accent text-white py-4 rounded-2xl font-black active:scale-95 transition-all">
                    <i class="bi bi-qr-code-scan"></i> Сканировать снова
                </button>
            </div>`;
        isMobile() ? showDrawer(html) : showDesktopMsg(html);
    }

    function showError(msg) {
        const html = `
            <div class="text-center py-8">
                <div class="w-20 h-20 rounded-full bg-warning/10 flex items-center justify-center mx-auto mb-4 border border-warning/20">
                    <i class="bi bi-wifi-off text-4xl text-warning"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Ошибка</h3>
                <p class="text-sm text-muted mb-6">${esc(msg)}</p>
                <button onclick="window._qr.closeDrawer(); setTimeout(window._qr.start, 300)"
                        class="w-full bg-accent text-white py-4 rounded-2xl font-black active:scale-95 transition-all">
                    <i class="bi bi-arrow-clockwise"></i> Попробовать снова
                </button>
            </div>`;
        isMobile() ? showDrawer(html) : showDesktopMsg(html);
    }

    function showDesktopMsg(html) {
        document.getElementById('result-placeholder').classList.add('hidden');
        const el = document.getElementById('scan-result');
        el.classList.remove('hidden');
        el.innerHTML = `<div class="scan-glass rounded-[2rem] p-8">${html}</div>`;
    }

    // ── Mobile drawer ──────────────────────────────────────────────────
    function showDrawer(html) {
        const content = document.getElementById('mobile-result-content');
        content.innerHTML = '';
        content.innerHTML = html;
        content.scrollTop = 0;

        const overlay = document.getElementById('mobile-result-overlay');
        const drawer  = document.getElementById('mobile-drawer');
        isClosingDrawer = false;

        overlay.style.display = 'block';
        void overlay.offsetHeight;
        requestAnimationFrame(() => {
            overlay.classList.add('active');
            drawer.style.transform = 'translateY(0)';
        });
    }

    function closeDrawer() {
        if (isClosingDrawer) return;
        isClosingDrawer = true;

        const overlay = document.getElementById('mobile-result-overlay');
        const drawer  = document.getElementById('mobile-drawer');
        drawer.style.transform = 'translateY(100%)';

        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.classList.remove('active');
            document.getElementById('mobile-result-content').innerHTML = '';
            lastScannedCode = '';
            scanInFlight    = false;
            isScanning      = false;
            isStopping      = false;
            isClosingDrawer = false;
            startScanning();
        }, 300);
    }

    // Swipe-to-close drawer
    (function () {
        let sy = 0, cy = 0, drag = false;
        const drawer = document.getElementById('mobile-drawer');
        if (!drawer) return;

        drawer.addEventListener('touchstart', e => {
            if (isClosingDrawer) return;
            const sc = document.getElementById('mobile-result-content');
            if (sc && sc.scrollTop > 5 && !e.target.closest('#drawer-handle')) return;
            sy = cy = e.touches[0].clientY;
            drag = true;
            drawer.style.transition = 'none';
        }, { passive: true });

        drawer.addEventListener('touchmove', e => {
            if (!drag) return;
            cy = e.touches[0].clientY;
            const d = cy - sy;
            if (d > 0) drawer.style.transform = `translateY(${d}px)`;
        }, { passive: true });

        drawer.addEventListener('touchend', () => {
            if (!drag) return;
            drag = false;
            drawer.style.transition = 'transform 0.3s ease-out';
            if (cy - sy > 100) closeDrawer();
            else drawer.style.transform = 'translateY(0)';
        }, { passive: true });
    })();

    // ── Desktop clear ──────────────────────────────────────────────────
    function clearResult() {
        const el = document.getElementById('scan-result');
        el.innerHTML = origResultHTML;
        el.classList.add('hidden');
        document.getElementById('result-placeholder').classList.remove('hidden');
        resetUI();
        startScanning();
    }

    // ── Audio beep ─────────────────────────────────────────────────────
    function beep() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain); gain.connect(audioCtx.destination);
            osc.frequency.value = 880; osc.type = 'sine';
            gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
            osc.start(); osc.stop(audioCtx.currentTime + 0.1);
            osc.onended = () => { osc.disconnect(); gain.disconnect(); };
        } catch(e) {}
    }

    // ── Helpers ────────────────────────────────────────────────────────
    function fmt(price) {
        return (parseFloat(price) || 0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' сом';
    }
    function esc(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // ── Page lifecycle ─────────────────────────────────────────────────
    window.addEventListener('beforeunload', () => {
        if (html5QrCode && isScanning) try { html5QrCode.stop(); } catch(e) {}
        if (audioCtx) try { audioCtx.close(); } catch(e) {}
    });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && isScanning) stopScanning();
    });

    // Auto-start on mobile
    document.addEventListener('DOMContentLoaded', () => {
        if (isMobile()) setTimeout(startScanning, 400);
    });

    // Public API
    window._qr = {
        start:      startScanning,
        stop:       stopScanning,
        closeDrawer,
        clearResult,
        toggleFlash,
        lookupManualArticle
    };
    window.closeMobileResult    = closeDrawer;
    window.clearResult          = clearResult;
    window.lookupManualArticle  = lookupManualArticle;
    window.startScanning        = startScanning;
    window.toggleFlash          = toggleFlash;
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/owner/scan-product.blade.php ENDPATH**/ ?>