<?php $__env->startSection('page-title', 'QR-сканер'); ?>
<?php $__env->startSection('body-class', 'qr-scan-page'); ?>
<?php $__env->startSection('content'); ?>


<style>
/* Fullscreen scanner on phones */
@media (max-width: 768px) {
    body.qr-scan-page { padding-bottom: 0 !important; }
    body.qr-scan-page .bottom-nav { display: none !important; }
    body.qr-scan-page .sidebar { display: none !important; }
    body.qr-scan-page .main-content { margin-left: 0 !important; }
    body.qr-scan-page .main-content > div:first-child { display: none !important; } /* topbar */
    body.qr-scan-page .main-content > div:nth-child(2) { padding: 0 !important; }   /* page padding */

    body.qr-scan-page .qr-scan-header { display: none !important; }
    body.qr-scan-page .card { border-radius: 0 !important; border-left: 0; border-right: 0; border-top: 0; }
    body.qr-scan-page .card > div { display: block !important; } /* remove 2 columns */
    body.qr-scan-page #desktop-result-panel { display: none !important; }

    body.qr-scan-page #camera-container {
        position: fixed !important;
        inset: 0 !important;
        height: 100dvh !important;
        width: 100% !important;
        margin: 0 !important;
    }
}

/* Use dynamic viewport units where supported */
.qr-camera-fullscreen {
    height: 100dvh;
}

/* Make html5-qrcode video fill container */
#scanner-container video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

/* Result overlay (mobile) */
#mobile-result-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.96);
    z-index: 2000;
    display: none;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
</style>

<div class="qr-scan-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 24px;">
        <i class="bi bi-qr-code-scan"></i>
    </div>
    <div>
        <h2 style="font-size: 28px; font-weight: 700; margin: 0;">QR-сканер</h2>
        <p style="font-size: 14px; color: #94a3b8; margin: 4px 0 0 0;">Быстрое сканирование товаров</p>
    </div>
</div>


<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 0;">
        
        <div id="camera-container" class="qr-camera-fullscreen" style="background: #0f172a; position: relative; height: 100vh; width: 100%; display: flex; align-items: center; justify-content: center;">
            <div id="scanner-container" style="position: absolute; inset: 0; width: 100%; height: 100%;"></div>
            
            
            <div style="position: absolute; bottom: 100px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; z-index: 100;">
                <div style="background: rgba(15,23,42,0.95); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8; font-size: 13px;">
                        <i class="bi bi-keyboard"></i> Или введите артикул:
                    </label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="manual-article" class="input" placeholder="ART123456" 
                            style="flex: 1; background: rgba(148,163,184,0.1); border: 1px solid rgba(148,163,184,0.2); color: #f1f5f9; padding: 10px; border-radius: 8px; font-size: 14px;"
                            onkeypress="if(event.key==='Enter') lookupManualArticle()">
                        <button class="btn btn-primary" onclick="lookupManualArticle()" style="padding: 10px 16px;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            
            
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 280px; height: 280px; border: 3px solid rgba(56,189,248,0.8); border-radius: 16px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.7); z-index: 10;">
                <div style="position: absolute; top: -3px; left: -3px; width: 40px; height: 40px; border-top: 4px solid #10b981; border-left: 4px solid #10b981; border-top-left-radius: 16px;"></div>
                <div style="position: absolute; top: -3px; right: -3px; width: 40px; height: 40px; border-top: 4px solid #10b981; border-right: 4px solid #10b981; border-top-right-radius: 16px;"></div>
                <div style="position: absolute; bottom: -3px; left: -3px; width: 40px; height: 40px; border-bottom: 4px solid #10b981; border-left: 4px solid #10b981; border-bottom-left-radius: 16px;"></div>
                <div style="position: absolute; bottom: -3px; right: -3px; width: 40px; height: 40px; border-bottom: 4px solid #10b981; border-right: 4px solid #10b981; border-bottom-right-radius: 16px;"></div>
                
                
                <div style="position: absolute; left: 10px; right: 10px; height: 2px; background: linear-gradient(90deg, transparent, #10b981, transparent); animation: scan-line 2s ease-in-out infinite;"></div>
            </div>

            
            <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 12px; z-index: 10;">
                <button id="start-scan" class="btn btn-primary" style="padding: 14px 28px; font-size: 16px;">
                    <i class="bi bi-camera"></i> Начать сканирование
                </button>
                <button id="stop-scan" class="btn btn-danger" style="display: none; padding: 14px 28px; font-size: 16px;">
                    <i class="bi bi-stop-circle"></i> Остановить
                </button>
            </div>

            
            <div id="scan-indicator" style="position: absolute; top: 20px; right: 20px; display: none; z-index: 10;">
                <div style="background: rgba(16,185,129,0.9); color: #fff; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                    <i class="bi bi-check-circle"></i> Сканирование...
                </div>
            </div>
        </div>

        
        <div id="desktop-result-panel" style="background: #f1f5f9; padding: 24px; overflow-y: auto; max-height: 100vh; color: #0f172a;">
            <div id="no-result" style="text-align: center; padding: 60px 20px; color: #64748b;">
                <i class="bi bi-qr-code" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;"></i>
                <p style="font-size: 16px;">Наведите камеру на QR-код</p>
                <p style="font-size: 13px; margin-top: 8px;">Товар отобразится здесь автоматически</p>
            </div>

            <div id="scan-result" style="display: none;">
                <div id="known-qr-badge" style="display:none; margin-bottom: 12px;">
                    <span class="badge badge-success">✓ Наш QR</span>
                </div>
                
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <img id="product-photo" src="" alt="Товар" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 12px; border: 3px solid #38bdf8; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                </div>

                
                <div style="background: white; border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <h3 id="product-name" style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 8px 0;"></h3>
                    <div style="color: #64748b; font-size: 14px; margin-bottom: 12px;">
                        <span style="color: #38bdf8; font-weight: 600;">Артикул:</span> <span id="product-article" style="color: #0f172a; font-weight: 500;"></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <span style="font-size: 32px; font-weight: 700; color: #10b981;" id="product-price"></span>
                        <span style="color: #64748b; font-size: 14px;">розница</span>
                    </div>
                    
                    
                    <div style="border-top: 1px solid #e2e8f0; padding-top: 16px; margin-top: 16px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <div style="font-size: 12px; color: #64748b; text-transform: uppercase;">Закупочная</div>
                                <div style="font-size: 18px; font-weight: 600; color: #0f172a;" id="supplier-price">—</div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; text-transform: uppercase;">Оптовая</div>
                                <div style="font-size: 18px; font-weight: 600; color: #0f172a;" id="wholesale-price">—</div>
                            </div>
                        </div>
                        <div style="margin-top: 12px;">
                            <div style="font-size: 12px; color: #64748b; text-transform: uppercase;">Прибыль с 1 шт</div>
                            <div style="font-size: 18px; font-weight: 600; color: #10b981;" id="profit-per-item">—</div>
                        </div>
                    </div>
                </div>

                
                <div style="background: white; border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0;">📦 Остатки на складах</h4>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                        <div style="background: rgba(56,189,248,0.1); border-radius: 12px; padding: 16px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #38bdf8;" id="total-stock">0</div>
                            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; margin-top: 4px;">Всего</div>
                        </div>
                        <div style="background: rgba(16,185,129,0.1); border-radius: 12px; padding: 16px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #10b981;" id="showcase-stock">0</div>
                            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; margin-top: 4px;">Витрина</div>
                        </div>
                        <div style="background: rgba(245,158,11,0.1); border-radius: 12px; padding: 16px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #f59e0b;" id="warehouse-stock">0</div>
                            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; margin-top: 4px;">Склад</div>
                        </div>
                    </div>
                </div>

                
                <div style="background: white; border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0;">📋 Варианты товара</h4>
                    <div id="variants-list" style="display: flex; flex-direction: column; gap: 12px;"></div>
                </div>

                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <a id="edit-product-btn" href="#" class="btn btn-primary" style="justify-content: center; background: #38bdf8; color: #0f172a;">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <button onclick="clearResult()" class="btn btn-outline" style="justify-content: center; border-color: #38bdf8; color: #38bdf8;">
                        <i class="bi bi-arrow-clockwise"></i> Ещё раз
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="mobile-result-overlay">
    <div style="padding: 18px 16px; position: sticky; top: 0; background: rgba(15,23,42,0.98); border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: space-between; gap: 12px;">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="bi bi-qr-code-scan" style="color:#38bdf8; font-size: 20px;"></i>
            <div style="font-weight:700; color:#f1f5f9;">Результат сканирования</div>
        </div>
        <button class="btn btn-outline" style="padding: 8px 12px;" onclick="closeMobileResult()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div style="padding: 16px;">
        <div id="mobile-result-content"></div>
        <div style="margin-top: 14px; display:flex; gap:10px;">
            <button class="btn btn-primary" style="flex:1; justify-content:center;" onclick="scanAgainMobile()">
                <i class="bi bi-camera"></i> Сканировать ещё раз
            </button>
        </div>
    </div>
</div>


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
const SCAN_COOLDOWN = 800; // быстрее, но с защитой от дублей
let scanInFlight = false;

function isMobile() {
    return window.matchMedia && window.matchMedia('(max-width: 768px)').matches;
}

function openMobileResult(html) {
    const overlay = document.getElementById('mobile-result-overlay');
    const content = document.getElementById('mobile-result-content');
    content.innerHTML = html;
    overlay.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeMobileResult() {
    const overlay = document.getElementById('mobile-result-overlay');
    overlay.style.display = 'none';
    document.body.style.overflow = '';
}

function scanAgainMobile() {
    closeMobileResult();
    clearResult();
    startScanning();
}

function startScanning() {
    if (isScanning) return;
    isScanning = true;
    scanInFlight = false;

    document.getElementById('start-scan').style.display = 'none';
    document.getElementById('stop-scan').style.display = 'inline-flex';
    document.getElementById('scan-indicator').style.display = 'block';

    // Html5Qrcode сам создаёт/крутит видео внутри контейнера
    html5QrCode = new Html5Qrcode('scanner-container');

    // Меньше qrbox => скан быстрее и ловит при приближении; больше => удобнее держать. Баланс.
    const qrBoxSize = Math.floor(Math.min(window.innerWidth, window.innerHeight) * 0.62);

    html5QrCode.start(
        { facingMode: 'environment' },
        {
            fps: 20,
            qrbox: { width: qrBoxSize, height: qrBoxSize },
            aspectRatio: 1.7777778,
            disableFlip: false
        },
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

    return html5QrCode.stop()
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
async function onScanSuccess(decodedText) {
    const now = Date.now();
    if (now - lastScanTime < SCAN_COOLDOWN) return;
    lastScanTime = now;
    if (scanInFlight) return;
    scanInFlight = true;

    // Звуковой сигнал
    playBeep();

    // Вибрация (если поддерживается)
    if (navigator.vibrate) navigator.vibrate(200);

    console.log('Отсканировано (сырые данные):', decodedText);
    // Сразу останавливаем камеру после первого удачного скана
    try { await stopScanning(); } catch (e) {}
    await fetchProductInfo(decodedText);
}

function onScanError(error) {
    // Игнорируем ошибки сканирования
    // console.error('Ошибка сканирования:', error); // Uncomment for debugging if needed
}

// Получение информации о товаре
async function fetchProductInfo(qrDataFromScan) {
    let article = qrDataFromScan; // Default to raw data

    // Проверяем, является ли отсканированные данные JSON строкой
    try {
        const parsedData = JSON.parse(qrDataFromScan);
        if (parsedData && typeof parsedData === 'object') {
            if (parsedData.article) {
                article = parsedData.article;
                console.log('Обнаружен JSON. Извлечен артикул:', article);
            } else if (parsedData.coupon_id) {
                // Это купонный QR - обработка будет на сервере
                console.log('Обнаружен купонный QR');
            }
        }
    } catch (e) {
        // Если парсинг не удался, предполагаем, что это просто строка артикула
        console.log('Данные QR-кода не являются JSON. Используем как есть.');
        article = qrDataFromScan;
    }

    try {
        const response = await fetch('<?php echo e(route('admin.qr.process')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qr_data: qrDataFromScan }) // Отправляем оригинальные данные
        });

        const data = await response.json().catch(() => ({}));

        if (data.success) {
            if (data.type === 'coupon') {
                // Показываем информацию о купоне
                displayCoupon(data.coupon);
            } else {
                // Показываем информацию о товаре
                displayProduct(data);
            }
        } else {
            // Если товар не найден, показываем сообщение об ошибке
            showNotFound(qrDataFromScan);
        }
    } catch (err) {
        console.error('Ошибка при получении информации о товаре:', err);
        showNotFound(qrDataFromScan);
    }
}

// Ручной поиск по артикулу
async function lookupManualArticle() {
    const article = document.getElementById('manual-article').value.trim();
    if (!article) {
        alert('Введите артикул');
        return;
    }
    
    // Останавливаем сканирование если идет
    if (isScanning) {
        stopScanning();
    }
    
    // Показываем индикатор загрузки
    document.getElementById('manual-article').disabled = true;
    
    try {
        const response = await fetch('<?php echo e(route('admin.qr.process')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qr_data: article })
        });
        
        const data = await response.json().catch(() => ({}));
        
        if (data.success) {
            displayProduct(data);
        } else {
            showNotFound(article);
        }
    } catch (err) {
        console.error('Ошибка при поиске:', err);
        showNotFound(article);
    } finally {
        document.getElementById('manual-article').disabled = false;
    }
}

// Отображение товара
function displayProduct(data) {
    document.getElementById('no-result').style.display = 'none';
    document.getElementById('scan-result').style.display = 'block';
    document.getElementById('known-qr-badge').style.display = data.is_known_qr ? 'block' : 'none';

    const product = data.product;
    const summary = data.summary;

    // Фото
    const photoEl = document.getElementById('product-photo');
    if (product.photo) {
        photoEl.src = product.photo;
        photoEl.style.display = 'inline-block';
    } else {
        photoEl.style.display = 'inline-block';
        photoEl.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"%3E%3Crect fill="%23e2e8f0" width="200" height="200"/%3E%3Ctext fill="%2394a3b8" font-family="Arial" font-size="14" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle"%3EНет фото%3C/text%3E%3C/svg%3E';
    }

    // Основная информация
    document.getElementById('product-name').textContent = product.name || '—';
    document.getElementById('product-article').textContent = product.article || '—';
    document.getElementById('product-price').textContent = (product.retail_price || 0) + ' сом';
    document.getElementById('edit-product-btn').href = '/admin/products/edit/' + product.id;
    
    // Дополнительная информация
    document.getElementById('supplier-price').textContent = (product.supplier_price || 0) + ' сом';
    document.getElementById('wholesale-price').textContent = (product.wholesale_price || 0) + ' сом';
    
    // Прибыль
    const profit = (product.retail_price || 0) - (product.supplier_price || 0);
    document.getElementById('profit-per-item').textContent = (profit > 0 ? '+' : '') + profit.toFixed(2) + ' сом';

    // Остатки
    document.getElementById('total-stock').textContent = summary.total_stock || 0;
    document.getElementById('showcase-stock').textContent = summary.shop_stock || 0;
    document.getElementById('warehouse-stock').textContent = summary.warehouse_stock || 0;

    // Варианты
    const variantsList = document.getElementById('variants-list');
    variantsList.innerHTML = '';
    
    if (product.variants && product.variants.length > 0) {
        product.variants.forEach(v => {
            const variantEl = document.createElement('div');
            variantEl.style.cssText = 'background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; display: flex; justify-content: space-between; align-items: center;';
            
            const sizeColor = [v.size, v.color].filter(Boolean).join(' • ') || '—';
            const variantTotal = v.stock?.reduce((sum, s) => sum + (s.quantity || 0), 0) || 0;
            const shopQty = v.stock?.filter(s => s.shop_id)?.reduce((sum, s) => sum + (s.quantity || 0), 0) || 0;
            const warehouseQty = v.stock?.filter(s => s.warehouse_id)?.reduce((sum, s) => sum + (s.quantity || 0), 0) || 0;
            
            variantEl.innerHTML = `
                <div>
                    <div style="font-weight: 600; color: #0f172a; font-size: 15px;">${sizeColor}</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                        Витрина: <strong style="color: #10b981;">${shopQty}</strong> | 
                        Склад: <strong style="color: #f59e0b;">${warehouseQty}</strong>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 18px; font-weight: 700; color: #38bdf8;">${variantTotal} шт</div>
                </div>
            `;
            variantsList.appendChild(variantEl);
        });
    } else {
        variantsList.innerHTML = '<div style="text-align: center; color: #64748b; padding: 20px;">Нет вариантов</div>';
    }
}

// Отображение купона
function displayCoupon(coupon) {
    document.getElementById('no-result').style.display = 'none';
    document.getElementById('scan-result').style.display = 'block';
    document.getElementById('known-qr-badge').style.display = 'block';
    document.getElementById('known-qr-badge').innerHTML = '<span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">🎫 Купон</span>';

    const panel = document.getElementById('desktop-result-panel');
    const resultDiv = document.getElementById('scan-result');

    resultDiv.innerHTML = `
        <div style="background: white; border-radius: 16px; padding: 24px; text-align: center; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="font-size: 64px; margin-bottom: 16px;">🎫</div>
            <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 8px 0;">Купонная карта</h3>
            <div style="color: #64748b; font-size: 14px; margin-bottom: 16px;">${coupon.code || '—'}</div>
            
            <div style="background: ${coupon.is_active ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'}; 
                        border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                <div style="font-size: 24px; font-weight: 700; color: ${coupon.is_active ? '#10b981' : '#ef4444'};">
                    ${coupon.is_active ? '✓ Активен' : '✗ Неактивен'}
                </div>
            </div>
            
            ${coupon.customer ? `
                <div style="text-align: left; background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                    <div style="font-size: 12px; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Владелец</div>
                    <div style="font-weight: 600; color: #0f172a; font-size: 16px;">${coupon.customer.name || '—'}</div>
                    <div style="color: #64748b; font-size: 14px; margin-top: 4px;">${coupon.customer.phone || '—'}</div>
                </div>
            ` : ''}
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div style="background: rgba(56, 189, 248, 0.1); border-radius: 12px; padding: 16px;">
                    <div style="font-size: 12px; color: #64748b; text-transform: uppercase;">Тип скидки</div>
                    <div style="font-weight: 700; color: #38bdf8; font-size: 16px; margin-top: 4px;">
                        ${coupon.discount_type === 'percent' ? 'Процентная' : 'Оптовая'}
                    </div>
                </div>
                <div style="background: rgba(16, 185, 129, 0.1); border-radius: 12px; padding: 16px;">
                    <div style="font-size: 12px; color: #64748b; text-transform: uppercase;">Размер</div>
                    <div style="font-weight: 700; color: #10b981; font-size: 16px; margin-top: 4px;">
                        ${coupon.discount_type === 'percent' ? coupon.discount_value + '%' : 'Закупочная цена'}
                    </div>
                </div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <button onclick="clearResult()" class="btn btn-outline" style="justify-content: center; border-color: #38bdf8; color: #38bdf8;">
                <i class="bi bi-arrow-clockwise"></i> Ещё раз
            </button>
            <a href="/admin/customers" class="btn btn-primary" style="justify-content: center; background: #38bdf8; color: #0f172a;">
                <i class="bi bi-people"></i> Клиенты
            </a>
        </div>
    `;
}

function clearResult() {
    // На телефоне показываем результат поверх камеры (камера уже остановлена)
    if (isMobile()) {
        const desktopPanel = document.getElementById('desktop-result-panel');
        const html = desktopPanel.innerHTML;
        openMobileResult(html);
    }
}

// Товар не найден
function showNotFound(scannedData) { // Added scannedData parameter for context
    document.getElementById('no-result').style.display = 'block';
    document.getElementById('scan-result').style.display = 'none';
    // Displaying scanned data in the "not found" message for debugging.
    document.getElementById('no-result').innerHTML = `
        <i class="bi bi-x-circle" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3; color: #ef4444;"></i>
        <p style="font-size: 16px; color: #ef4444;">Товар не найден</p>
        <p style="font-size: 13px; color: #94a3b8; margin-top: 8px;">Отсканировано: ${scannedData}</p>
    `;

    if (isMobile()) {
        openMobileResult(document.getElementById('desktop-result-panel').innerHTML);
    }
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
    document.getElementById('known-qr-badge').style.display = 'none';
    scanInFlight = false;
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

// Mobile layout: make scan fullscreen automatically (start on tap only, due to iOS/Android rules)
window.addEventListener('resize', () => {
    // re-calc box on restart only; keep simple
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/qr/scan.blade.php ENDPATH**/ ?>