@extends('layouts.admin')

@section('page-title', 'Сканер товаров')
@section('page-subtitle', 'Мгновенный поиск товаров по QR и штрих-кодам')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Scanner Column -->
        <div class="lg:col-span-7 space-y-6">
            <div class="glass overflow-hidden rounded-[2rem] relative aspect-[4/5] md:aspect-square bg-black shadow-2xl border-white/10">
                <!-- Camera Viewport -->
                <div id="scanner-container" class="absolute inset-0"></div>
                
                <!-- Overlay Gradients -->
                <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/60 via-transparent to-black/30"></div>
                
                <!-- Scanning Frame -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
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
                <div class="absolute bottom-24 left-6 right-6 z-10">
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
                <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-4 z-10 px-6">
                    <button id="start-scan" class="flex-1 glass py-4 rounded-2xl flex items-center justify-center gap-3 font-bold text-white hover:bg-white/10 transition-all border-white/20">
                        <i class="bi bi-camera-fill text-xl text-accent"></i> Запустить камеру
                    </button>
                    <button id="stop-scan" class="hidden flex-1 glass py-4 rounded-2xl flex items-center justify-center gap-3 font-bold text-danger hover:bg-danger/10 transition-all border-danger/20">
                        <i class="bi bi-stop-circle-fill text-xl"></i> Остановить
                    </button>
                </div>

                <!-- Status Badge -->
                <div id="scan-indicator" class="hidden absolute top-6 right-6 z-10">
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
<div id="mobile-result-overlay" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeMobileResult()"></div>
    <div class="absolute bottom-0 left-0 right-0 glass rounded-t-[2.5rem] border-t border-white/20 p-6 transform translate-y-full transition-transform duration-300 ease-out max-h-[90vh] overflow-y-auto" id="mobile-drawer">
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
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Mobile Adaptation */
@media (max-width: 768px) {
    body { padding-bottom: 0 !important; }
    .sidebar { display: none !important; }
    .bottom-nav { display: none !important; }
    .main-content { margin-left: 0 !important; padding-top: 0 !important; }
    
    /* Hide page header on mobile */
    .page-header { display: none !important; }
    
    /* Full width container */
    .max-w-5xl { max-width: 100% !important; padding: 0 !important; }
    .grid { gap: 0 !important; }
    
    /* Scanner full screen */
    .lg\\:col-span-7 { grid-column: span 12 !important; }
    .lg\\:col-span-5 { display: none !important; }
    
    /* Result overlay for mobile */
    #mobile-result-overlay { display: block !important; }
    
    /* Camera container full viewport */
    #scanner-container {
        height: 100dvh !important;
        width: 100% !important;
    }
    
    /* Manual input positioned at bottom */
    .absolute.bottom-24 { bottom: 80px !important; }
    
    /* Scanner controls at very bottom */
    .absolute.bottom-6 { bottom: 16px !important; }
}

/* Desktop hides mobile overlay */
@media (min-width: 769px) {
    #mobile-result-overlay { display: none !important; }
}
</style>

<audio id="scan-sound" preload="auto">
    <source src="data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU" type="audio/wav">
</audio>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let isScanning = false;
let lastScanTime = 0;
let lastScannedCode = '';
const SCAN_COOLDOWN = 1500; // Увеличено до 1.5 сек
let scanInFlight = false;

function isMobile() {
    return window.matchMedia('(max-width: 1024px)').matches;
}

function startScanning() {
    if (isScanning) return;
    isScanning = true;
    scanInFlight = false;

    document.getElementById('start-scan').classList.add('hidden');
    document.getElementById('stop-scan').classList.remove('hidden');
    document.getElementById('scan-indicator').classList.remove('hidden');

    html5QrCode = new Html5Qrcode('scanner-container');

    const qrBoxSize = Math.floor(Math.min(window.innerWidth, window.innerHeight) * 0.7);

    html5QrCode.start(
        { facingMode: 'environment' },
        {
            fps: 24,
            qrbox: { width: qrBoxSize, height: qrBoxSize },
            aspectRatio: 1,
            disableFlip: false
        },
        onScanSuccess,
        onScanError
    ).catch(err => {
        console.error('Scanner error:', err);
        resetScannerUI();
        alert('Не удалось получить доступ к камере');
    });
}

function resetScannerUI() {
    isScanning = false;
    document.getElementById('start-scan').classList.remove('hidden');
    document.getElementById('stop-scan').classList.add('hidden');
    document.getElementById('scan-indicator').classList.add('hidden');
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
    
    // Защита от повторного сканирования того же кода
    if (decodedText === lastScannedCode && now - lastScanTime < 3000) {
        return; // Игнорируем тот же код в течение 3 секунд
    }
    
    if (now - lastScanTime < SCAN_COOLDOWN || scanInFlight) return;

    lastScanTime = now;
    lastScannedCode = decodedText;
    scanInFlight = true;

    playBeep();
    if (navigator.vibrate) navigator.vibrate(100);

    try {
        await stopScanning();
        await fetchProductInfo(decodedText);
    } catch (e) {
        console.error(e);
    } finally {
        scanInFlight = false;
        // Очищаем последний код через 5 секунд
        setTimeout(() => { lastScannedCode = ''; }, 5000);
    }
}

function onScanError(error) {}

async function fetchProductInfo(qrData) {
    try {
        const response = await fetch('{{ route('admin.scan-product.process') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qr_data: qrData })
        });

        const data = await response.json();
        if (data.success) {
            displayProduct(data);
        } else {
            showNotFound(qrData);
        }
    } catch (err) {
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

    if (isMobile()) {
        const content = document.getElementById('mobile-result-content');
        // Construct mobile content (similar to desktop but optimized)
        openMobileResult(renderMobileResult(data));
    } else {
        document.getElementById('result-placeholder').classList.add('hidden');
        document.getElementById('scan-result').classList.remove('hidden');
        
        document.getElementById('product-photo').src = p.photo || '';
        document.getElementById('product-name').textContent = p.name;
        document.getElementById('product-article').textContent = p.article;
        document.getElementById('product-retail-price').textContent = (p.retail_price || 0) + ' сом';
        
        const profit = (p.retail_price || 0) - (p.supplier_price || 0);
        document.getElementById('product-profit').textContent = '+' + profit.toFixed(0) + ' сом';
        
        document.getElementById('total-stock').textContent = s.total_stock;
        document.getElementById('showcase-stock').textContent = s.shop_stock;
        document.getElementById('warehouse-stock').textContent = s.warehouse_stock;

        document.getElementById('edit-product-btn').href = '{{ url('admin/products') }}/' + p.id + '/edit';

        const badges = document.getElementById('badges-container');
        badges.innerHTML = `
            <span class="px-3 py-1 bg-success text-bg text-[10px] font-black uppercase rounded-lg shadow-lg">
                ${data.is_known_qr ? 'NURMAN QR' : 'FOUND'}
            </span>
            <span class="px-3 py-1 bg-accent text-bg text-[10px] font-black uppercase rounded-lg shadow-lg">
                ${p.category?.name || 'STOCK'}
            </span>
        `;

        const variantsList = document.getElementById('variants-list');
        variantsList.innerHTML = p.variants.map(v => `
            <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-white">${v.size || '—'} / ${v.color || '—'}</span>
                    <span class="text-[10px] font-mono text-muted">${v.barcode || 'NO BARCODE'}</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[10px] font-bold uppercase tracking-tighter">
                    <div class="text-accent">Σ ${v.total_quantity}</div>
                    <div class="text-success">V ${v.showcase_quantity}</div>
                    <div class="text-warning">S ${v.warehouse_quantity}</div>
                </div>
            </div>
        `).join('') || '<p class="text-center text-muted text-xs py-4">Нет вариантов</p>';
    }
}

function renderMobileResult(data) {
    const p = data.product;
    const s = data.summary;
    const profit = (p.retail_price || 0) - (p.supplier_price || 0);

    return `
        <div class="flex gap-4 mb-6">
            <img src="${p.photo}" class="w-24 h-24 rounded-2xl object-cover bg-white/5">
            <div class="flex-1 min-w-0">
                <h3 class="text-xl font-black text-white truncate">${p.name}</h3>
                <p class="text-muted text-xs mb-3 font-mono">${p.article}</p>
                <div class="flex gap-2">
                    <span class="px-2 py-0.5 bg-success/10 text-success text-[10px] font-bold rounded-md">
                        ${data.is_known_qr ? 'NURMAN' : 'MATCH'}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-white/5 p-4 rounded-2xl border border-white/5 text-center">
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-1">Розница</p>
                <p class="text-xl font-black text-success">${p.retail_price} сом</p>
            </div>
            <div class="bg-white/5 p-4 rounded-2xl border border-white/5 text-center">
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-1">Выгода</p>
                <p class="text-xl font-black text-accent">+${profit.toFixed(0)} сом</p>
            </div>
        </div>

        <div class="bg-white/5 p-5 rounded-2xl border border-white/5 mb-8">
            <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-black text-white uppercase tracking-widest">Остатки по складам</span>
                <span class="text-xl font-black text-white">${s.total_stock}</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-muted font-bold">Витрина</span>
                    <span class="text-success font-black">${s.shop_stock}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-muted font-bold">Склад</span>
                    <span class="text-warning font-black">${s.warehouse_stock}</span>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button onclick="scanAgainMobile()" class="flex-1 bg-accent text-bg py-4 rounded-2xl font-black shadow-glow">
                СКАНИРОВАТЬ ЕЩЁ
            </button>
            <a href="{{ url('owner/products') }}/${p.id}/edit" class="w-14 h-14 glass rounded-2xl flex items-center justify-center text-white">
                <i class="bi bi-pencil-square text-xl"></i>
            </a>
        </div>
    `;
}

function openMobileResult(html) {
    const overlay = document.getElementById('mobile-result-overlay');
    const drawer = document.getElementById('mobile-drawer');
    document.getElementById('mobile-result-content').innerHTML = html;
    overlay.classList.remove('hidden');
    setTimeout(() => drawer.classList.remove('translate-y-full'), 10);
    document.body.style.overflow = 'hidden';
}

function closeMobileResult() {
    const overlay = document.getElementById('mobile-result-overlay');
    const drawer = document.getElementById('mobile-drawer');
    drawer.classList.add('translate-y-full');
    setTimeout(() => {
        overlay.classList.add('hidden');
        // Очищаем последний код чтобы можно было сканировать снова
        lastScannedCode = '';
        document.body.style.overflow = '';
        // Небольшая задержка перед запуском камеры
        setTimeout(() => {
            if (!isScanning) startScanning();
        }, 500);
    }, 300);
}

function scanAgainMobile() {
    closeMobileResult();
    startScanning();
}

function showNotFound(qrData) {
    const html = `
        <div class="text-center py-12">
            <div class="w-20 h-20 rounded-full bg-danger/10 text-danger flex items-center justify-center mx-auto mb-6">
                <i class="bi bi-x-circle text-4xl"></i>
            </div>
            <h3 class="text-xl font-black text-white mb-2">Товар не найден</h3>
            <p class="text-muted text-sm mb-8">Код: <span class="font-mono text-white">${qrData}</span></p>
            <button onclick="isMobile() ? scanAgainMobile() : clearResult()" class="px-8 py-3 bg-white/10 text-white rounded-xl font-bold">
                Попробовать снова
            </button>
        </div>
    `;
    if (isMobile()) openMobileResult(html);
    else {
        document.getElementById('result-placeholder').innerHTML = html;
        document.getElementById('result-placeholder').classList.remove('hidden');
        document.getElementById('scan-result').classList.add('hidden');
    }
}

function clearResult() {
    document.getElementById('result-placeholder').classList.remove('hidden');
    document.getElementById('scan-result').classList.add('hidden');
    document.getElementById('manual-article').value = '';
    startScanning();
}

function playBeep() {
    const audio = document.getElementById('scan-sound');
    if (audio) audio.play().catch(() => {});
}
</script>
@endsection
