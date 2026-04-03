@extends('seller::layouts.seller')

@section('page-title', 'POS Терминал')

@section('content')
<div class="pos-app-wrapper">
    <!-- Каталог -->
    <div class="pos-catalog-area">
        <div class="pos-search-row">
            <div class="pos-input-group">
                <i class="bi bi-search"></i>
                <input type="text" id="pos-main-search" placeholder="Поиск товара или штрих-код... (Alt+Q)" autocomplete="off">
            </div>
            <button class="pos-btn-cam" onclick="posStartScan('product')">
                <i class="bi bi-camera"></i>
            </button>
        </div>

        <div class="pos-category-nav">
            <button class="pos-cat-pill active" onclick="posApplyFilter(null, this)">Все</button>
            @foreach($categories as $category)
                <button class="pos-cat-pill" onclick="posApplyFilter({{ $category->id }}, this)">{{ $category->name }}</button>
            @endforeach
        </div>

        <div class="pos-products-grid-wrap">
            <div id="pos-grid" class="pos-products-grid">
                @foreach($products as $product)
                    @foreach($product->variants as $variant)
                    <div class="pos-product-card"
                         data-id="{{ $variant->id }}"
                         data-name="{{ $product->name }} ({{ $variant->size }}/{{ $variant->color }})"
                         data-price="{{ $product->retail_price }}"
                         data-supplier-price="{{ $variant->supplier_price }}"
                         data-category="{{ $product->category_id }}"
                         data-stock="{{ $variant->stock_quantity }}"
                         onclick="posClickAdd(this)">
                        <span class="pos-stock-tag">{{ $variant->stock_quantity }} шт</span>
                        <div class="pos-img-container">
                            @if($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="">
                            @else
                                <i class="bi bi-box"></i>
                            @endif
                        </div>
                        <div class="pos-card-body">
                            <div class="pos-p-title">{{ Str::limit($product->name, 25) }}</div>
                            <div class="pos-p-price">{{ number_format($product->retail_price, 0, '.', ' ') }} <span>с</span></div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <!-- Корзина -->
    <div class="pos-cart-area" id="pos-cart-aside">
        <div class="pos-cart-header">
            <span><i class="bi bi-cart3"></i> Чек</span>
            <button class="pos-close-mobile" onclick="posToggleMobileCart()"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="pos-customer-section">
            <div id="pos-selected-cust" style="display: none;">
                <div class="pos-cust-info-card">
                    <div class="pos-cust-text">
                        <strong id="pos-cust-name">Клиент</strong>
                        <span id="pos-cust-phone">+996...</span>
                    </div>
                    <button onclick="posRemoveCust()" class="pos-cust-remove"><i class="bi bi-x-circle"></i></button>
                </div>
            </div>
            <div id="pos-cust-btns" class="pos-cust-buttons">
                <button onclick="posStartScan('customer')"><i class="bi bi-qr-code"></i> Карта</button>
                <button onclick="posSearchPhone()"><i class="bi bi-telephone"></i> Поиск</button>
            </div>
        </div>

        <div class="pos-cart-items" id="pos-items-list">
            <div class="pos-empty-cart">
                <i class="bi bi-cart-x"></i>
                <p>Корзина пуста</p>
            </div>
        </div>

        <div class="pos-cart-summary">
            <div id="pos-discount-row" class="pos-summary-row" style="display: none;">
                <span>Скидка:</span>
                <span id="pos-discount-val" style="color: #ef4444;">0 с</span>
            </div>
            <div class="pos-summary-row pos-total-row">
                <span>Итого:</span>
                <span id="pos-total-sum">0 с</span>
            </div>
            <div class="pos-bottom-actions">
                <button class="pos-btn-clear" onclick="posClearAll()">Сброс</button>
                <button class="pos-btn-pay" onclick="posOpenPay()">Оплата (F9)</button>
            </div>
        </div>
    </div>

    <button class="pos-mobile-cart-btn" onclick="posToggleMobileCart()">
        <i class="bi bi-cart-fill"></i>
        <span id="pos-mobile-badge">0</span>
    </button>
</div>

<!-- Модалки -->
<div id="pos-modal-scan" class="pos-full-modal">
    <div class="pos-modal-container">
        <div class="pos-modal-header">
            <span id="pos-scan-title">Сканер</span>
            <button onclick="posStopScan()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="pos-qr-box" style="width: 100%; min-height: 280px; background: #000;"></div>
    </div>
</div>

<div id="pos-modal-pay" class="pos-full-modal">
    <div class="pos-modal-container" style="max-width: 380px;">
        <div class="pos-modal-header">
            <span>Оплата</span>
            <button onclick="posClosePay()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="pos-pay-body">
            <div id="pos-pay-total-val" class="pos-pay-sum">0 с</div>
            <div class="pos-pay-methods">
                <button class="pay-method-pill active" data-m="cash" onclick="posSetPayment('cash', this)">Наличные</button>
                <button class="pay-method-pill" data-m="card" onclick="posSetPayment('card', this)">Карта</button>
                <button class="pay-method-pill" data-m="transfer" onclick="posSetPayment('transfer', this)">Перевод</button>
            </div>
            <div id="pos-cash-calc" style="margin-top: 15px;">
                <input type="number" id="pos-cash-input" class="pos-field-ui" placeholder="Получено" style="text-align: center; font-size: 20px;">
                <div class="pos-change-text">Сдача: <strong id="pos-change-display">0 с</strong></div>
            </div>
            <button class="pos-btn-pay" style="width: 100%; margin-top: 20px; height: 54px;" onclick="posComplete()">Готово</button>
        </div>
    </div>
</div>

<style>
    /* Изолированные стили POS */
    .pos-app-wrapper { display: flex; gap: 15px; height: calc(100vh - 160px); background: #0f172a; padding: 12px; border-radius: 12px; border: 1px solid #334155; overflow: hidden; position: relative; }
    
    .pos-catalog-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .pos-search-row { display: flex; gap: 8px; margin-bottom: 12px; }
    .pos-input-group { flex: 1; position: relative; }
    .pos-input-group i { position: absolute; left: 12px; top: 11px; color: #64748b; }
    .pos-input-group input { width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 10px 12px 10px 38px; color: #fff; outline: none; }
    .pos-btn-cam { width: 44px; height: 44px; background: #10b981; border: none; border-radius: 8px; color: #0f172a; cursor: pointer; }

    .pos-category-nav { display: flex; gap: 6px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
    .pos-cat-pill { padding: 7px 14px; background: #1e293b; border: 1px solid #334155; border-radius: 7px; color: #94a3b8; font-size: 13px; white-space: nowrap; cursor: pointer; }
    .pos-cat-pill.active { background: #10b981; color: #0f172a; font-weight: 700; border-color: #10b981; }

    .pos-products-grid-wrap { flex: 1; overflow-y: auto; }
    .pos-products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; }
    
    .pos-product-card { background: #1e293b; border: 1px solid #334155; border-radius: 10px; overflow: hidden; position: relative; cursor: pointer; transition: 0.2s; }
    .pos-product-card:hover { border-color: #10b981; transform: translateY(-2px); }
    .pos-stock-tag { position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.7); font-size: 10px; padding: 2px 6px; border-radius: 4px; color: #fff; }
    .pos-img-container { height: 90px; background: #000; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #334155; }
    .pos-img-container img { width: 100%; height: 100%; object-fit: cover; }
    .pos-card-body { padding: 8px; }
    .pos-p-title { font-size: 12px; font-weight: 600; color: #f1f5f9; height: 28px; overflow: hidden; margin-bottom: 4px; }
    .pos-p-price { font-size: 14px; font-weight: 700; color: #10b981; }
    .pos-p-price span { font-size: 10px; opacity: 0.6; }

    .pos-cart-area { width: 330px; background: #1e293b; border-radius: 10px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid #334155; }
    .pos-cart-header { padding: 12px; border-bottom: 1px solid #334155; display: flex; justify-content: space-between; font-weight: 700; color: #fff; }
    .pos-close-mobile { display: none; background: none; border: none; color: #fff; font-size: 18px; }

    .pos-customer-section { padding: 10px; border-bottom: 1px solid #334155; }
    .pos-cust-info-card { display: flex; justify-content: space-between; align-items: center; background: rgba(16,185,129,0.1); padding: 8px; border-radius: 8px; border: 1px solid #10b981; }
    .pos-cust-text strong { display: block; font-size: 12px; color: #10b981; }
    .pos-cust-text span { font-size: 11px; color: #94a3b8; }
    .pos-cust-remove { background: none; border: none; color: #ef4444; font-size: 18px; cursor: pointer; }
    .pos-cust-buttons { display: flex; gap: 6px; }
    .pos-cust-buttons button { flex: 1; padding: 7px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #94a3b8; font-size: 12px; cursor: pointer; }

    .pos-cart-items { flex: 1; overflow-y: auto; padding: 10px; }
    .pos-empty-cart { text-align: center; padding-top: 40px; color: #475569; }
    .pos-empty-cart i { font-size: 40px; opacity: 0.3; }

    .pos-cart-item-ui { display: flex; gap: 8px; background: #0f172a; padding: 8px; border-radius: 8px; margin-bottom: 8px; border: 1px solid #334155; }
    .pos-cart-info-col { flex: 1; }
    .pos-cart-name { font-size: 12px; color: #f1f5f9; margin-bottom: 4px; }
    .pos-cart-ctrl { display: flex; align-items: center; justify-content: space-between; }
    .pos-qty-row { display: flex; align-items: center; gap: 10px; }
    .pos-qty-row button { width: 22px; height: 22px; border: none; background: #1e293b; color: #fff; border-radius: 4px; cursor: pointer; }
    .pos-item-sum { font-weight: 700; color: #10b981; font-size: 13px; }

    .pos-cart-summary { padding: 12px; background: #0f172a; border-top: 1px solid #334155; }
    .pos-summary-row { display: flex; justify-content: space-between; margin-bottom: 4px; color: #94a3b8; font-size: 13px; }
    .pos-total-row { font-size: 18px; font-weight: 800; color: #fff; margin-top: 5px; margin-bottom: 12px; }
    .pos-bottom-actions { display: grid; grid-template-columns: 1fr 2fr; gap: 8px; }
    .pos-btn-clear { background: rgba(239,68,68,0.1); border: 1px solid #ef4444; color: #ef4444; border-radius: 6px; padding: 8px; cursor: pointer; }
    .pos-btn-pay { background: #10b981; color: #0f172a; border: none; border-radius: 6px; padding: 10px; font-weight: 700; cursor: pointer; }

    .pos-full-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 15px; }
    .pos-modal-container { background: #1e293b; border-radius: 12px; width: 100%; max-width: 450px; overflow: hidden; }
    .pos-modal-header { padding: 15px; border-bottom: 1px solid #334155; display: flex; justify-content: space-between; font-weight: 700; color: #fff; }
    .pos-modal-header button { background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; }
    .pos-field-ui { width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 12px; color: #fff; outline: none; }
    .pos-pay-methods { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
    .pay-method-pill { padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #94a3b8; font-size: 12px; cursor: pointer; }
    .pay-method-pill.active { background: rgba(16,185,129,0.15); border-color: #10b981; color: #10b981; font-weight: 700; }
    .pos-change-text { margin-top: 10px; text-align: right; color: #94a3b8; font-size: 13px; }

    .pos-mobile-cart-btn { position: fixed; bottom: 80px; right: 20px; width: 60px; height: 60px; border-radius: 30px; background: #10b981; color: #0f172a; font-size: 24px; display: none; z-index: 1000; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.4); }
    .pos-mobile-cart-btn span { position: absolute; top: 0; right: 0; background: #ef4444; color: #fff; font-size: 11px; width: 22px; height: 22px; border-radius: 11px; display: flex; align-items: center; justify-content: center; border: 2px solid #10b981; }

    @media (max-width: 768px) {
        .pos-app-wrapper { padding: 8px; gap: 8px; height: calc(100vh - 140px); }
        .pos-products-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .pos-product-card { min-height: auto; }
        .pos-img-container { height: 70px; }
        .pos-card-body { padding: 6px; }
        .pos-p-title { font-size: 11px; }
        .pos-p-price { font-size: 12px; }
        .pos-cat-pill { padding: 6px 12px; font-size: 12px; }
        .pos-input-group input { padding: 8px 8px 8px 34px; font-size: 14px; }
        .pos-btn-cam { width: 40px; height: 40px; }
        
        /* Корзина на весь экран на мобильном */
        .pos-cart-area { position: fixed; right: -100%; top: 0; height: 100vh; z-index: 2000; transition: 0.3s; width: 100%; max-width: 400px; border-radius: 0; }
        .pos-cart-area.open { right: 0; }
        .pos-mobile-cart-btn { display: block; }
        .pos-close-mobile { display: block; }
    }

    @media (max-width: 480px) {
        .pos-products-grid { grid-template-columns: repeat(2, 1fr); gap: 6px; }
        .pos-img-container { height: 60px; }
        .pos-search-row { flex-wrap: wrap; }
        .pos-btn-cam { flex: 1; }
    }
</style>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let posCart = [];
    let posCust = null;
    let posCoupon = null;
    let posMethod = 'cash';
    let posScanner = null;
    let posLastScanTime = 0;
    let posLastScannedCode = '';
    const POS_SCAN_COOLDOWN = 1500; // 1.5 сек

    // Загрузка из localStorage
    function posLoadFromStorage() {
        try {
            const savedCart = localStorage.getItem('posCart');
            const savedCust = localStorage.getItem('posCust');
            const savedCoupon = localStorage.getItem('posCoupon');
            
            if (savedCart) posCart = JSON.parse(savedCart);
            if (savedCust) posCust = JSON.parse(savedCust);
            if (savedCoupon) posCoupon = JSON.parse(savedCoupon);
            
            // Восстанавливаем UI клиента
            if (posCust) {
                document.getElementById('pos-selected-cust').style.display = 'block';
                document.getElementById('pos-cust-btns').style.display = 'none';
                document.getElementById('pos-cust-name').textContent = posCust.full_name;
                document.getElementById('pos-cust-phone').textContent = posCust.phone;
            }
        } catch(e) {
            console.error('Ошибка загрузки из localStorage:', e);
        }
    }
    
    // Сохранение в localStorage
    function posSaveToStorage() {
        try {
            localStorage.setItem('posCart', JSON.stringify(posCart));
            localStorage.setItem('posCust', JSON.stringify(posCust));
            localStorage.setItem('posCoupon', JSON.stringify(posCoupon));
        } catch(e) {
            console.error('Ошибка сохранения в localStorage:', e);
        }
    }
    
    // Очистка localStorage
    function posClearStorage() {
        localStorage.removeItem('posCart');
        localStorage.removeItem('posCust');
        localStorage.removeItem('posCoupon');
    }

    // Автофокус (убран - не открывать клавиатуру автоматически)
    window.onload = () => {
        posLoadFromStorage();
        posUpdateUI();
    };
    // Убран автофокус при клике - клавиатура не будет открываться

    // Горячие клавиши
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F9') { e.preventDefault(); posOpenPay(); }
        if (e.key === 'Escape') { posStopScan(); posClosePay(); }
    });

    // USB Сканер
    document.getElementById('pos-main-search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const code = this.value.trim();
            if (code) { posAutoSearch(code); this.value = ''; }
        }
    });

    function posToggleMobileCart() { document.getElementById('pos-cart-aside').classList.toggle('open'); }

    function posAddToCart(id, name, price, stock, supplierPrice) {
        const item = posCart.find(i => i.variant_id === id);
        if (item) {
            if (item.quantity < stock) item.quantity++;
            else alert('Нет на складе!');
        } else {
            posCart.push({ variant_id: id, name, price, quantity: 1, stock, supplier_price: supplierPrice || 0 });
        }
        posUpdateUI();
    }

    function posClickAdd(el) {
        posAddToCart(
            parseInt(el.dataset.id),
            el.dataset.name,
            parseFloat(el.dataset.price),
            parseInt(el.dataset.stock),
            parseFloat(el.dataset.supplierPrice || 0)
        );
    }

    function posChangeQty(id, delta) {
        const i = posCart.find(x => x.variant_id === id);
        if (i) {
            i.quantity += delta;
            if (i.quantity <= 0) posCart = posCart.filter(x => x.variant_id !== id);
            posUpdateUI();
        }
    }

    function posUpdateUI() {
        const list = document.getElementById('pos-items-list');
        if (posCart.length === 0) {
            list.innerHTML = '<div class="pos-empty-cart"><i class="bi bi-cart-x"></i><p>Пусто</p></div>';
            document.getElementById('pos-mobile-badge').textContent = '0';
            posRecalc();
            posSaveToStorage();
            return;
        }
        let html = '';
        let qTotal = 0;
        posCart.forEach(item => {
            qTotal += item.quantity;
            html += `
                <div class="pos-cart-item-ui">
                    <div class="pos-cart-info-col">
                        <div class="pos-cart-name">${item.name}</div>
                        <div class="pos-cart-ctrl">
                            <div class="pos-qty-row">
                                <button onclick="posChangeQty(${item.variant_id}, -1)">-</button>
                                <span style="font-weight:700;">${item.quantity}</span>
                                <button onclick="posChangeQty(${item.variant_id}, 1)">+</button>
                            </div>
                            <div class="pos-item-sum">${(item.price * item.quantity).toLocaleString()} с</div>
                        </div>
                    </div>
                </div>`;
        });
        list.innerHTML = html;
        document.getElementById('pos-mobile-badge').textContent = qTotal;
        posRecalc();
        posSaveToStorage();
    }

    function posRecalc() {
        const sub = posCart.reduce((a, b) => a + (b.price * b.quantity), 0);
        let disc = 0;
        let isWholesale = false;
        
        // Оптовая цена (wholesale) — продажа по цене закупки
        if (posCoupon && posCoupon.discount_type === 'wholesale') {
            isWholesale = true;
            // Пересчитываем сумму по ценам закупки
            const supplierTotal = posCart.reduce((a, b) => {
                // Цена закупки хранится в item.supplier_price
                const supplierPrice = b.supplier_price || 0;
                return a + (supplierPrice * b.quantity);
            }, 0);
            disc = sub - supplierTotal; // Скидка = разница между розницей и оптом
            disc = Math.max(0, disc);
        }
        // Процентная скидка
        else if (posCoupon && posCoupon.discount_type === 'percent') {
            disc = sub * (posCoupon.discount_value / 100);
            disc = Math.min(disc, sub);
        }
        // Скидка лояльности (если нет купона)
        else if (posCust && posCust.loyalty_discount_percent > 0) {
            disc = sub * (posCust.loyalty_discount_percent / 100);
        }
        
        const total = Math.max(0, sub - disc);
        document.getElementById('pos-total-sum').textContent = total.toLocaleString() + ' с';
        document.getElementById('pos-pay-total-val').textContent = total.toLocaleString() + ' с';
        const dRow = document.getElementById('pos-discount-row');
        if (disc > 0) {
            dRow.style.display = 'flex';
            document.getElementById('pos-discount-val').textContent = '- ' + disc.toLocaleString() + ' с';
        } else dRow.style.display = 'none';
    }

    function posClearAll() { if(confirm('Очистить корзину?')) { posCart = []; posRemoveCust(); posUpdateUI(); posClearStorage(); } }

    function posStartScan(type) {
        document.getElementById('pos-modal-scan').style.display = 'flex';
        document.getElementById('pos-scan-title').textContent = type === 'product' ? 'Товар' : 'Клиент';
        posScanner = new Html5Qrcode("pos-qr-box");
        posScanner.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, (txt) => {
            const now = Date.now();
            
            // Защита от повторного сканирования того же кода
            if (txt === posLastScannedCode && now - posLastScanTime < 3000) {
                return; // Игнорируем тот же код в течение 3 секунд
            }
            
            if (now - posLastScanTime < POS_SCAN_COOLDOWN) return;
            
            posLastScanTime = now;
            posLastScannedCode = txt;
            
            posStopScan();
            if (type === 'product') posAutoSearch(txt); else posFetchCust(txt);
            
            // Очищаем код через 5 секунд
            setTimeout(() => { posLastScannedCode = ''; }, 5000);
        }).catch(() => { alert('Камера не найдена'); posStopScan(); });
    }

    function posStopScan() {
        if(posScanner) posScanner.stop().then(() => { posScanner.clear(); posScanner = null; });
        document.getElementById('pos-modal-scan').style.display = 'none';
    }

    function posAutoSearch(code) {
        const url = '{{ route("seller.pos.scan-product") }}';
        const token = '{{ csrf_token() }}';
        
        const scanTitle = document.getElementById('pos-scan-title');
        const originalTitle = scanTitle.textContent;
        scanTitle.textContent = 'Поиск...';
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', token);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        const v = data.variant;
                        posAddToCart(
                            v.id,
                            v.name + ' (' + v.size + '/' + v.color + ')',
                            v.price,
                            v.quantity,
                            v.supplier_price || 0
                        );
                        scanTitle.textContent = '✓ Добавлено!';
                        setTimeout(() => { scanTitle.textContent = originalTitle; }, 1500);
                    } else {
                        scanTitle.textContent = '✗ Не найдено';
                        setTimeout(() => { scanTitle.textContent = originalTitle; }, 2000);
                        alert(data.message || 'Товар не найден');
                    }
                } catch(e) {
                    scanTitle.textContent = '✗ Ошибка!';
                    setTimeout(() => { scanTitle.textContent = originalTitle; }, 2000);
                    alert('Ошибка обработки ответа');
                }
            } else {
                scanTitle.textContent = '✗ Ошибка ' + xhr.status;
                setTimeout(() => { scanTitle.textContent = originalTitle; }, 2000);
                alert('Ошибка сервера: ' + xhr.status);
            }
        };
        
        xhr.onerror = function() {
            scanTitle.textContent = '✗ Ошибка!';
            setTimeout(() => { scanTitle.textContent = originalTitle; }, 2000);
            alert('Ошибка сети. Проверьте подключение.');
        };
        
        xhr.send(JSON.stringify({ qr_data: code }));
    }

    function posFetchCust(code) {
        const url = '{{ route("seller.pos.scan-customer") }}';
        const token = '{{ csrf_token() }}';
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', token);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        posCust = data.customer;
                        posCoupon = data.coupon;
                        document.getElementById('pos-selected-cust').style.display = 'block';
                        document.getElementById('pos-cust-btns').style.display = 'none';
                        document.getElementById('pos-cust-name').textContent = data.customer.full_name;
                        document.getElementById('pos-cust-phone').textContent = data.customer.phone;
                        posRecalc();
                        posSaveToStorage();
                    } else {
                        alert(data.message || 'Клиент не найден');
                    }
                } catch(e) {
                    alert('Ошибка обработки ответа');
                }
            } else {
                alert('Ошибка сервера: ' + xhr.status);
            }
        };
        
        xhr.onerror = function() {
            alert('Ошибка сети. Проверьте подключение.');
        };
        
        xhr.send(JSON.stringify({ qr_data: code }));
    }

    function posSearchPhone() { const p = prompt('Введите номер:'); if(p) posFetchCust(p); }
    function posRemoveCust() { posCust = null; posCoupon = null; document.getElementById('pos-selected-cust').style.display = 'none'; document.getElementById('pos-cust-btns').style.display = 'flex'; posRecalc(); posSaveToStorage(); }

    function posOpenPay() { if(posCart.length > 0) document.getElementById('pos-modal-pay').style.display = 'flex'; }
    function posClosePay() { document.getElementById('pos-modal-pay').style.display = 'none'; }
    function posSetPayment(m, btn) { 
        posMethod = m; 
        document.querySelectorAll('.pay-method-pill').forEach(b => b.classList.remove('active')); 
        btn.classList.add('active'); 
    }

    document.getElementById('pos-cash-input').addEventListener('input', function() {
        const total = parseFloat(document.getElementById('pos-total-sum').textContent.replace(/[^0-9]/g, ''));
        const cash = parseFloat(this.value) || 0;
        document.getElementById('pos-change-display').textContent = Math.max(0, cash - total).toLocaleString() + ' с';
    });

    function posComplete() {
        const total = parseFloat(document.getElementById('pos-total-sum').textContent.replace(/[^0-9]/g, ''));
        const paid = parseFloat(document.getElementById('pos-cash-input').value) || total;
        fetch('{{ route("seller.pos.checkout") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                items: posCart,
                payment_method: posMethod,
                amount_paid: paid,
                customer_id: posCust ? posCust.id : null,
                coupon_id: posCoupon ? posCoupon.id : null
            })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                window.open('/seller/pos/receipt/' + data.transaction.id, '_blank', 'width=350,height=600');
                posCart = [];
                posCust = null;
                posCoupon = null;
                posClearStorage();
                location.reload();
            } else alert(data.message);
        });
    }

    // Фильтр по названию
    document.getElementById('pos-main-search').addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.pos-product-card').forEach(c => {
            c.style.display = c.dataset.name.toLowerCase().includes(q) ? 'block' : 'none';
        });
    });

    function posApplyFilter(id, btn) {
        document.querySelectorAll('.pos-cat-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.pos-product-card').forEach(c => {
            c.style.display = (id === null || parseInt(c.dataset.category) === id) ? 'block' : 'none';
        });
    }
</script>
@endpush
@endsection
