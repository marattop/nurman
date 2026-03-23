@extends('layouts.app')

@section('title', 'POS-терминал - Продавец')

@section('content')
<style>
    :root {
        --pos-header-height: 60px;
        --pos-sidebar-width: 350px;
    }
    
    .pos-container {
        display: flex;
        height: calc(100vh - var(--pos-header-height));
        overflow: hidden;
    }
    
    .pos-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .pos-sidebar {
        width: var(--pos-sidebar-width);
        background: var(--bg-card);
        border-left: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .pos-search-section {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-card);
    }
    
    .pos-search-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-card);
        color: var(--text-primary);
        font-size: 1rem;
    }
    
    .pos-search-input:focus {
        outline: none;
        border-color: var(--primary);
    }
    
    .pos-scan-btn {
        width: 100%;
        margin-top: 0.5rem;
        padding: 0.75rem;
        background: var(--gradient);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    
    .pos-scan-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .pos-categories {
        padding: 0.75rem 1rem;
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-secondary);
    }
    
    .pos-category-btn {
        padding: 0.5rem 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        color: var(--text-primary);
        cursor: pointer;
        white-space: nowrap;
        transition: 0.2s;
    }
    
    .pos-category-btn.active,
    .pos-category-btn:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    
    .pos-products {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
        background: var(--bg-secondary);
    }
    
    .pos-products-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .pos-product-card {
        display: flex;
        gap: 1rem;
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: 0.2s;
        border: 2px solid transparent;
        align-items: center;
    }
    
    .pos-product-card:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        border-color: var(--primary);
    }
    
    .pos-product-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        background: var(--bg-secondary);
        flex-shrink: 0;
    }
    
    .pos-product-info {
        flex: 1;
        min-width: 0;
    }
    
    .pos-product-name {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    
    .pos-product-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
    
    .pos-product-article {
        font-size: 0.85rem;
        color: var(--text-secondary);
        background: var(--bg-secondary);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }
    
    .pos-product-variants {
        font-size: 0.85rem;
        color: var(--primary);
        background: rgba(16, 185, 129, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }
    
    .pos-product-price {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.3rem;
    }
    
    .pos-product-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    
    .pos-select-btn {
        padding: 0.75rem 1.5rem;
        background: var(--gradient);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        white-space: nowrap;
    }
    
    .pos-select-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .pos-variants-count {
        font-size: 0.75rem;
        color: var(--text-secondary);
        text-align: center;
    }
    
    .pos-cart {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .pos-cart-header {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--gradient);
        color: white;
    }
    
    .pos-cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem;
    }
    
    .pos-cart-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--bg-secondary);
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    
    .pos-cart-item-info {
        flex: 1;
        min-width: 0;
    }
    
    .pos-cart-item-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-primary);
    }
    
    .pos-cart-item-variant {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }
    
    .pos-cart-item-price {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    
    .pos-cart-item-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .pos-qty-btn {
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 6px;
        background: var(--primary);
        color: white;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .pos-qty-btn:hover {
        background: var(--primary-dark);
    }
    
    .pos-qty-value {
        font-weight: 600;
        min-width: 24px;
        text-align: center;
    }
    
    .pos-cart-item-remove {
        background: none;
        border: none;
        color: var(--accent-red);
        cursor: pointer;
        padding: 0.25rem;
        font-size: 1.2rem;
    }
    
    .pos-customer-info {
        padding: 1rem;
        background: rgba(16, 185, 129, 0.1);
        border-top: 1px solid var(--border-color);
    }
    
    .pos-customer-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        background: var(--bg-card);
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    
    .pos-coupon-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: var(--primary);
        color: white;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .pos-totals {
        padding: 1rem;
        border-top: 1px solid var(--border-color);
        background: var(--bg-secondary);
    }
    
    .pos-total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .pos-total-row.discount {
        color: var(--success);
    }
    
    .pos-total-row.final {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 2px solid var(--border-color);
    }
    
    .pos-checkout-btn {
        width: 100%;
        padding: 1rem;
        background: var(--gradient);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        margin-top: 0.75rem;
    }
    
    .pos-checkout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
    }
    
    .pos-checkout-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    .scanner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .scanner-modal {
        background: var(--bg-card);
        border-radius: 16px;
        padding: 1.5rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .scanner-reader-container {
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        min-height: 300px;
    }

    .scanner-reader-container #scanner-reader {
        width: 100%;
        min-height: 300px;
    }

    .scanner-reader-container video {
        width: 100% !important;
        height: auto !important;
        max-height: 400px;
        object-fit: cover;
    }

    .scanner-overlay-frame {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 250px;
        height: 250px;
        border: 3px solid rgba(16, 185, 129, 0.5);
        border-radius: 12px;
        pointer-events: none;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
        z-index: 10;
    }

    .scanner-overlay-frame::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--accent-primary);
        box-shadow: 0 0 10px var(--accent-primary);
        animation: scan 2s ease-in-out infinite;
    }

    @keyframes scan {
        0%, 100% { top: 0; }
        50% { top: calc(100% - 3px); }
    }
    
    .payment-amount {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary);
        text-align: center;
        margin: 1rem 0;
    }
    
    .payment-input {
        width: 100%;
        padding: 1rem;
        font-size: 1.5rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-card);
        color: var(--text-primary);
        text-align: center;
    }
    
    .payment-input:focus {
        outline: none;
        border-color: var(--primary);
    }
    
    .payment-methods {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin: 1rem 0;
    }
    
    .payment-method-btn {
        padding: 1rem;
        background: var(--bg-secondary);
        border: 2px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }
    
    .payment-method-btn.active {
        border-color: var(--primary);
        background: rgba(16, 185, 129, 0.1);
        color: var(--primary);
    }
    
    .change-amount {
        font-size: 1.25rem;
        text-align: center;
        padding: 1rem;
        background: rgba(16, 185, 129, 0.1);
        border-radius: 8px;
        margin-top: 1rem;
    }
    
    .change-amount span {
        color: var(--primary);
        font-weight: 700;
    }
    
    .variants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .variant-card {
        background: var(--bg-secondary);
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 1rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .variant-card:hover,
    .variant-card.selected {
        border-color: var(--primary);
        background: rgba(16, 185, 129, 0.1);
    }

    .variant-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: var(--bg-tertiary);
    }

    .variant-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .variant-color {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid var(--border-color);
    }

    .variant-price {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.1rem;
    }

    .variant-stock {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .variant-stock.in-stock {
        color: var(--success);
    }

    .variant-stock.out-of-stock {
        color: var(--accent-red);
    }
    
    .pos-toast {
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: var(--bg-card);
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    }
    
    .pos-toast.success {
        border-left: 4px solid var(--success);
    }
    
    .pos-toast.error {
        border-left: 4px solid var(--accent-red);
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .pos-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-secondary);
    }

    .pos-empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .hotkeys-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-align: center;
        padding: 0.5rem;
        background: var(--bg-secondary);
        border-radius: 8px;
        margin-top: 0.5rem;
    }

    .hotkey-badge {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.75rem;
        margin: 0 0.25rem;
    }
</style>

<div class="pos-container">
    <div class="pos-main">
        <div class="pos-search-section">
            <input type="text" class="pos-search-input" id="productSearch" placeholder="Поиск товара...">
            <button class="pos-scan-btn" onclick="openScanner()">
                <i class="bi bi-qr-code-scan"></i> Сканировать (F2)
            </button>
            <div class="hotkeys-hint">
                <span class="hotkey-badge">F1</span> Поиск
                <span class="hotkey-badge">F2</span> Сканировать
                <span class="hotkey-badge">F4</span> Оплата
                <span class="hotkey-badge">F5</span> Обновить
            </div>
        </div>
        
        <div class="pos-categories">
            <button class="pos-category-btn active" data-category="all">Все товары</button>
            @foreach($categories as $category)
                <button class="pos-category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
            @endforeach
        </div>

        <div class="pos-products">
            <div class="pos-products-list" id="productsList">
                @forelse($products as $product)
                    @php
                        $variantsCount = $product->variants->count();
                        $firstVariant = $product->variants->first();
                        $price = $product->retail_price ?? 0;
                    @endphp
                    <div class="pos-product-card" 
                         data-product-id="{{ $product->id }}"
                         data-product='@json($product)'
                         data-variants='@json($product->variants)'>
                        @if($product->photo)
                            <img src="/storage/{{ $product->photo }}" alt="{{ $product->name }}" class="pos-product-image">
                        @else
                            <div class="pos-product-image" style="display:flex;align-items:center;justify-content:center;background:var(--bg-secondary);">
                                <i class="bi bi-image" style="font-size:2rem;"></i>
                            </div>
                        @endif
                        <div class="pos-product-info">
                            <div class="pos-product-name">{{ $product->name }}</div>
                            <div class="pos-product-meta">
                                <span class="pos-product-article">Арт. {{ $product->article }}</span>
                            </div>
                            <div class="pos-product-price">{{ number_format($price, 0, ',', ' ') }} с</div>
                        </div>
                        <div class="pos-product-action">
                            <button class="pos-select-btn">
                                <i class="bi bi-cart-plus"></i> Выбрать
                            </button>
                            @if($variantsCount > 1)
                                <span class="pos-variants-count">{{ $variantsCount }} вариант(ов)</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="pos-empty">
                        <div class="pos-empty-icon"><i class="bi bi-box"></i></div>
                        <p>Товары не найдены</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="pos-sidebar">
        <div class="pos-cart">
            <div class="pos-cart-header">
                <h5 class="mb-0"><i class="bi bi-cart3"></i> Корзина</h5>
                <small id="cartCount">0 товаров</small>
            </div>
            
            <div class="pos-cart-items" id="cartItems">
                <div class="pos-empty" id="emptyCart">
                    <div class="pos-empty-icon"><i class="bi bi-cart"></i></div>
                    <p>Корзина пуста</p>
                </div>
            </div>
            
            <div class="pos-customer-info" id="customerInfo" style="display: none;">
                <div class="pos-customer-badge">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--primary);"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600;" id="customerName"></div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);" id="customerPhone"></div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeCustomer()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div id="couponInfo"></div>
            </div>

            <div class="pos-totals">
                <div class="pos-total-row">
                    <span>Товаров:</span>
                    <span id="totalItems">0</span>
                </div>
                <div class="pos-total-row">
                    <span>Сумма:</span>
                    <span id="totalAmount">0 с</span>
                </div>
                <div class="pos-total-row discount" id="discountRow" style="display: none;">
                    <span>Скидка:</span>
                    <span id="discountAmount">0 с</span>
                </div>
                <div class="pos-total-row final">
                    <span>К оплате:</span>
                    <span id="finalAmount">0 с</span>
                </div>
                
                <button class="pos-checkout-btn" id="checkoutBtn" onclick="openPaymentModal()" disabled>
                    <i class="bi bi-credit-card"></i> Оплатить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно вариантов -->
<div class="modal fade" id="variantsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span id="modalProductName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="variants-grid" id="variantsGrid"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно сканера -->
<div id="scannerModal" class="scanner-overlay" style="display: none;">
    <div class="scanner-modal">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-qr-code-scan"></i> Сканирование</h4>
            <button type="button" class="btn btn-danger btn-sm" onclick="closeScanner()">
                <i class="bi bi-x-lg"></i> Закрыть
            </button>
        </div>
        <div id="scanner-reader-container" class="scanner-reader-container mb-3">
            <div id="scanner-reader"></div>
            <div class="scanner-overlay-frame"></div>
        </div>
        <div class="mt-3">
            <input type="text" id="manualQrInput" class="form-control form-control-lg" placeholder="Введите код" onkeypress="if(event.key==='Enter')processManualQr()">
            <button class="btn btn-primary btn-lg w-100 mt-2" onclick="processManualQr()">
                <i class="bi bi-check-lg"></i> ОК
            </button>
        </div>
    </div>
</div>

<!-- Модальное окно оплаты -->
<div id="paymentModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-credit-card"></i> Оплата</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-muted">К оплате:</div>
                <div class="payment-amount" id="paymentAmountDisplay">0 с</div>
                <label class="form-label">Получено от клиента:</label>
                <input type="number" id="amountPaid" class="payment-input" placeholder="0" oninput="calculateChange()">
                <label class="form-label mt-3">Способ оплаты:</label>
                <div class="payment-methods">
                    <button class="payment-method-btn active" data-method="cash" onclick="selectPaymentMethod('cash')">
                        <i class="bi bi-cash"></i><br>Наличные
                    </button>
                    <button class="payment-method-btn" data-method="card" onclick="selectPaymentMethod('card')">
                        <i class="bi bi-credit-card"></i><br>Карта
                    </button>
                    <button class="payment-method-btn" data-method="transfer" onclick="selectPaymentMethod('transfer')">
                        <i class="bi bi-phone"></i><br>Перевод
                    </button>
                </div>
                <div class="change-amount" id="changeDisplay" style="display: none;">
                    Сдача: <span id="changeAmount">0 с</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="processCheckout()">
                    <i class="bi bi-check-lg"></i> Завершить продажу
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
// Состояние
let cart = [];
let currentCustomer = null;
let currentCoupon = null;
let productStock = {};
let currentVariants = [];
let currentProduct = null;
let videoStream = null;
let animationFrame = null;
let isScannerActive = false;
let isProcessingScan = false;
let lastScannedCode = null;
let selectedPaymentMethod = 'cash'; // Значение по умолчанию
const SCAN_DELAY = 1500; // Задержка между сканированиями (1.5 секунды)

// Горячие клавиши
function initHotkeys() {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F1') { e.preventDefault(); document.getElementById('productSearch').focus(); }
        if (e.key === 'F2') { e.preventDefault(); openScanner(); }
        if (e.key === 'F4') { e.preventDefault(); if (cart.length > 0) openPaymentModal(); }
        if (e.key === 'F5') { e.preventDefault(); location.reload(); }
        if (e.key === 'Escape') { closeScanner(); }
    });
}

// Инициализация карточек товаров
function initProductCards() {
    document.querySelectorAll('.pos-product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const product = JSON.parse(this.dataset.product);
            const variants = JSON.parse(this.dataset.variants);
            openVariantsModal(productId, product, variants);
        });
    });
}

// Категории
function initCategoryFilter() {
    document.querySelectorAll('.pos-category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.pos-category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const catId = this.dataset.category;
            if (catId === 'all') {
                location.reload();
            } else {
                filterByCategory(catId);
            }
        });
    });
}

// Поиск товаров
function initSearch() {
    let searchTimeout;
    document.getElementById('productSearch').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const query = e.target.value.trim();
            if (query.length >= 2) {
                searchProducts(query);
            } else if (query.length === 0) {
                location.reload();
            }
        }, 300);
    });
}

async function searchProducts(query) {
    const res = await fetch(`/seller/pos/products/search?q=${encodeURIComponent(query)}`);
    const data = await res.json();
    if (data.success) {
        const list = document.getElementById('productsList');
        if (!data.products || data.products.length === 0) {
            list.innerHTML = '<div class="pos-empty"><div class="pos-empty-icon"><i class="bi bi-box"></i></div><p>Товары не найдены</p></div>';
            return;
        }

        list.innerHTML = data.products.map(product => {
            const variantsCount = product.variants?.length || 0;
            const price = product.retail_price || 0;
            const photoHtml = product.photo
                ? `<img src="/storage/${product.photo}" alt="${product.name}" class="pos-product-image">`
                : `<div class="pos-product-image" style="display:flex;align-items:center;justify-content:center;background:var(--bg-secondary);"><i class="bi bi-image" style="font-size:2rem;"></i></div>`;

            return `
            <div class="pos-product-card"
                 data-product-id="${product.id}"
                 data-product='${JSON.stringify(product)}'
                 data-variants='${JSON.stringify(product.variants || [])}'>
                ${photoHtml}
                <div class="pos-product-info">
                    <div class="pos-product-name">${product.name}</div>
                    <div class="pos-product-meta">
                        <span class="pos-product-article">Арт. ${product.article}</span>
                    </div>
                    <div class="pos-product-price">${price.toLocaleString()} с</div>
                </div>
                <div class="pos-product-action">
                    <button class="pos-select-btn">
                        <i class="bi bi-cart-plus"></i> Выбрать
                    </button>
                    ${variantsCount > 1 ? `<span class="pos-variants-count">${variantsCount} вариант(ов)</span>` : ''}
                </div>
            </div>
            `;
        }).join('');

        initProductCards();
        loadAllStock();
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    initHotkeys();
    initProductCards();
    initCategoryFilter();
    initSearch();
    loadAllStock();
});

async function filterByCategory(categoryId) {
    const res = await fetch(`/seller/pos/products/category/${categoryId}`);
    const data = await res.json();
    if (data.success) {
        const list = document.getElementById('productsList');
        if (!data.products || data.products.length === 0) {
            list.innerHTML = '<div class="pos-empty"><div class="pos-empty-icon"><i class="bi bi-box"></i></div><p>Товары не найдены</p></div>';
            return;
        }
        
        list.innerHTML = data.products.map(product => {
            const variantsCount = product.variants?.length || 0;
            const price = product.retail_price || 0;
            const photoHtml = product.photo 
                ? `<img src="/storage/${product.photo}" alt="${product.name}" class="pos-product-image">`
                : `<div class="pos-product-image" style="display:flex;align-items:center;justify-content:center;background:var(--bg-secondary);"><i class="bi bi-image" style="font-size:2rem;"></i></div>`;
            
            return `
            <div class="pos-product-card" 
                 data-product-id="${product.id}"
                 data-product='${JSON.stringify(product)}'
                 data-variants='${JSON.stringify(product.variants || [])}'>
                ${photoHtml}
                <div class="pos-product-info">
                    <div class="pos-product-name">${product.name}</div>
                    <div class="pos-product-meta">
                        <span class="pos-product-article">Арт. ${product.article}</span>
                    </div>
                    <div class="pos-product-price">${price.toLocaleString()} с</div>
                </div>
                <div class="pos-product-action">
                    <button class="pos-select-btn">
                        <i class="bi bi-cart-plus"></i> Выбрать
                    </button>
                    ${variantsCount > 1 ? `<span class="pos-variants-count">${variantsCount} вариант(ов)</span>` : ''}
                </div>
            </div>
            `;
        }).join('');
        
        // Пересоздаём обработчики для новых карточек
        initProductCards();
        loadAllStock();
    }
}

// Загрузка остатков
function loadAllStock() {
    // Помечаем товары без остатков
    document.querySelectorAll('.pos-product-card').forEach(card => {
        try {
            const variants = JSON.parse(card.dataset.variants);
            const hasStock = variants.some(v => v.stock_quantity > 0);
            if (!hasStock) {
                card.style.opacity = '0.5';
                card.style.pointerEvents = 'none';
            }
        } catch (e) {}
    });
}

// Открытие вариантов
window.openVariantsModal = function(productId, product, variants) {
    currentProduct = product;
    currentVariants = variants;
    document.getElementById('modalProductName').textContent = product.name;

    const grid = document.getElementById('variantsGrid');
    grid.innerHTML = variants.map((v, i) => {
        const stockQty = v.stock_quantity || 0;
        const inStock = stockQty > 0;
        const price = product.retail_price || 0;
        
        return `
        <div class="variant-card ${!inStock ? 'disabled' : ''}" onclick="${inStock ? `selectVariant(${i})` : ''}">
            <div class="variant-info">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <strong>${v.size || '—'} / ${v.color || '—'}</strong>
                    <span class="variant-price">${price.toLocaleString()} с</span>
                </div>
                <div style="font-size:0.75rem;color:#94a3b8;">Арт: ${v.sku || product.article || '—'}</div>
                <div class="variant-stock ${inStock ? 'in-stock' : 'out-of-stock'}">
                    ${inStock ? `✓ В наличии: ${stockQty} шт.` : '✗ Нет в наличии'}
                </div>
            </div>
        </div>
    `}).join('');

    new bootstrap.Modal(document.getElementById('variantsModal')).show();
};

window.selectVariant = function(idx) {
    const v = currentVariants[idx];
    const stockQty = v.stock_quantity || 0;

    if (stockQty <= 0) {
        showToast('error', 'Товара нет в наличии');
        return;
    }

    // Базовая цена
    let price = currentProduct.retail_price || 0;
    let variantDetails = {
        size: v.size,
        color: v.color,
        sku: v.sku,
        wholesale_price: v.wholesale_price,
        supplier_price: v.supplier_price
    };

    // Если есть оптовый купон, используем оптовую цену
    if (currentCoupon && currentCoupon.discount_type === 'wholesale') {
        price = v.wholesale_price || v.supplier_price || price;
    }
    // Если есть процентная скидка купона
    else if (currentCoupon && currentCoupon.discount_type === 'percent') {
        price = price * (1 - currentCoupon.discount_value / 100);
    }

    addToCart(v.id, price, currentProduct.name, currentProduct.article, variantDetails);
    bootstrap.Modal.getInstance(document.getElementById('variantsModal')).hide();
};

// Корзина
function addToCart(variantId, price, name, article, variantDetails) {
    const existing = cart.find(item => item.variant_id === variantId);

    // Применяем скидку клиента если есть
    let finalPrice = price;
    
    // Если есть оптовый купон - используем оптовую цену
    if (currentCoupon && currentCoupon.discount_type === 'wholesale') {
        finalPrice = variantDetails.wholesale_price || variantDetails.supplier_price || price;
    }
    // Если есть процентная скидка купона - применяем процент
    else if (currentCoupon && currentCoupon.discount_type === 'percent') {
        finalPrice = price * (1 - currentCoupon.discount_value / 100);
    }
    // Если есть скидка лояльности клиента (без купона)
    else if (currentCustomer && currentCustomer.loyalty_discount_percent > 0) {
        finalPrice = price * (1 - currentCustomer.loyalty_discount_percent / 100);
    }
    
    if (existing) {
        existing.quantity++;
        existing.price = finalPrice; // Обновляем цену с учётом скидки
    } else {
        cart.push({
            variant_id: variantId,
            name, 
            article, 
            price: finalPrice,
            basePrice: price, // Базовая цена до скидки
            quantity: 1,
            variant_details: variantDetails
        });
    }
    renderCart();
    showToast('success', 'Товар добавлен');
}

// Добавление товара напрямую (цена уже со скидкой)
function addToCartDirect(variantId, price, name, article, variantDetails) {
    const existing = cart.find(item => item.variant_id === variantId);
    
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({
            variant_id: variantId,
            name, 
            article, 
            price: price,
            basePrice: price,
            quantity: 1,
            variant_details: variantDetails
        });
    }
    renderCart();
    showToast('success', 'Товар добавлен');
}

function renderCart() {
    const container = document.getElementById('cartItems');
    if (cart.length === 0) {
        container.innerHTML = '<div class="pos-empty"><div class="pos-empty-icon"><i class="bi bi-cart"></i></div><p>Корзина пуста</p></div>';
        document.getElementById('checkoutBtn').disabled = true;
    } else {
        container.innerHTML = cart.map((item, i) => {
            const variantInfo = [];
            if (item.variant_details?.size) variantInfo.push(item.variant_details.size);
            if (item.variant_details?.color) variantInfo.push(item.variant_details.color);
            
            return `
            <div class="pos-cart-item">
                <div class="pos-cart-item-info">
                    <div class="pos-cart-item-name">${item.name}</div>
                    <div class="pos-cart-item-variant">${variantInfo.join(' / ') || ''}</div>
                    <div class="pos-cart-item-price">${item.price.toLocaleString()} с${item.basePrice && item.basePrice !== item.price ? ' <span style="color:#94a3b8;font-size:0.75rem;">(было ' + item.basePrice.toLocaleString() + ' с)</span>' : ''}</div>
                </div>
                <div class="pos-cart-item-controls">
                    <button class="pos-qty-btn" onclick="updateQty(${i}, -1)">-</button>
                    <span class="pos-qty-value">${item.quantity}</span>
                    <button class="pos-qty-btn" onclick="updateQty(${i}, 1)">+</button>
                    <button class="pos-cart-item-remove" onclick="removeFromCart(${i})"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `}).join('');
        document.getElementById('checkoutBtn').disabled = false;
    }
    updateTotals();
}

window.updateQty = function(i, d) {
    cart[i].quantity += d;
    if (cart[i].quantity <= 0) cart.splice(i, 1);
    renderCart();
};

window.removeFromCart = function(i) {
    cart.splice(i, 1);
    renderCart();
};

function updateTotals() {
    const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
    const totalAmount = cart.reduce((s, i) => s + i.price * i.quantity, 0);
    const baseAmount = cart.reduce((s, i) => s + (i.basePrice || i.price) * i.quantity, 0);

    let discount = baseAmount - totalAmount;

    const final = totalAmount;

    document.getElementById('totalItems').textContent = totalItems + (totalItems > 0 ? ' шт' : '');
    document.getElementById('totalAmount').textContent = baseAmount.toLocaleString() + ' с';
    document.getElementById('finalAmount').textContent = final.toLocaleString() + ' с';

    if (discount > 0) {
        document.getElementById('discountRow').style.display = 'flex';
        document.getElementById('discountAmount').textContent = '-' + discount.toLocaleString() + ' с';
    } else {
        document.getElementById('discountRow').style.display = 'none';
    }
    
    return { totalItems, totalAmount, baseAmount, discount, finalAmount: final };
}

// Сканирование
window.openScanner = function() {
    // Сбрасываем память при каждом открытии
    lastScannedCode = null;
    isProcessingScan = false;
    
    document.getElementById('scannerModal').style.display = 'flex';
    startScanner();
};

window.closeScanner = function() {
    stopScanner();
    document.getElementById('scannerModal').style.display = 'none';
    document.getElementById('manualQrInput').value = '';
};

async function startScanner() {
    if (isScannerActive) return;
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        const video = document.createElement('video');
        video.srcObject = videoStream;
        video.autoplay = true;
        video.muted = true;
        document.getElementById('scanner-reader').innerHTML = '';
        document.getElementById('scanner-reader').appendChild(video);
        isScannerActive = true;
        scanFrame();
    } catch (e) {
        showToast('error', 'Ошибка камеры: ' + e.message);
    }
}

function stopScanner() {
    isScannerActive = false;
    if (animationFrame) cancelAnimationFrame(animationFrame);
    if (videoStream) {
        videoStream.getTracks().forEach(t => t.stop());
        videoStream = null;
    }
    document.getElementById('scanner-reader').innerHTML = '';
}

function scanFrame() {
    if (!isScannerActive || isProcessingScan) return;
    
    const video = document.querySelector('#scanner-reader video');
    if (!video || video.readyState < 2) {
        animationFrame = requestAnimationFrame(scanFrame);
        return;
    }
    
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, imageData.width, imageData.height);
    
    if (code) {
        // Проверяем, не сканировали ли уже этот код
        if (code.data === lastScannedCode) {
            // Тот же код - игнорируем, продолжаем сканировать
            animationFrame = requestAnimationFrame(scanFrame);
            return;
        }
        
        // Сохраняем код
        lastScannedCode = code.data;
        
        // Останавливаем сканер и обрабатываем
        stopScanner();
        processScanResult(code.data);
    } else {
        animationFrame = requestAnimationFrame(scanFrame);
    }
}

function processScanResult(data) {
    try {
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;
        if (parsed.customer_id) scanCustomer(data);
        else if (parsed.coupon_id || parsed.code) scanCoupon(data);
        else scanProduct(data);
    } catch (e) {
        scanProduct(data);
    }
}

window.processManualQr = function() {
    const code = document.getElementById('manualQrInput').value.trim();
    if (code) processScanResult(code);
};

async function scanProduct(qrData) {
    if (isProcessingScan) return; // Защита от повторного вызова
    isProcessingScan = true;
    
    try {
        const res = await fetch('/seller/pos/scan-product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qr_data: qrData })
        });
        const data = await res.json();
        if (data.success) {
            const v = data.variant;

            // Применяем скидку клиента если есть
            let finalPrice = v.price;
            let variantDetails = {
                size: v.size,
                color: v.color,
                sku: v.sku,
                wholesale_price: v.wholesale_price,
                supplier_price: v.supplier_price
            };

            // Если есть оптовый купон - используем оптовую цену
            if (currentCoupon && currentCoupon.discount_type === 'wholesale') {
                finalPrice = v.wholesale_price || v.supplier_price || v.price;
            }
            // Если есть процентная скидка купона - применяем процент
            else if (currentCoupon && currentCoupon.discount_type === 'percent') {
                finalPrice = v.price * (1 - currentCoupon.discount_value / 100);
            }
            // Если есть скидка лояльности клиента (без купона)
            else if (currentCustomer && currentCustomer.loyalty_discount_percent > 0) {
                finalPrice = v.price * (1 - currentCustomer.loyalty_discount_percent / 100);
            }

            addToCartDirect(v.id, finalPrice, v.name, v.article, variantDetails);
        } else {
            showToast('error', data.message || 'Товар не найден');
        }
    } finally {
        isProcessingScan = false;
        lastScannedCode = null; // Сбрасываем память для следующего сканирования
    }
}

async function scanCustomer(qrData) {
    if (isProcessingScan) return;
    isProcessingScan = true;
    
    try {
        const res = await fetch('/seller/pos/scan-customer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qr_data: qrData })
        });
        const data = await res.json();
        if (data.success) {
            currentCustomer = data.customer;
            currentCoupon = data.coupon;

            document.getElementById('customerName').textContent = data.customer.full_name;
            document.getElementById('customerPhone').textContent = data.customer.phone;
            document.getElementById('customerInfo').style.display = 'block';

            if (data.coupon) {
                let text = '';
                if (data.coupon.discount_type === 'percent') text = `Скидка ${data.coupon.discount_value}%`;
                else if (data.coupon.discount_type === 'wholesale') text = 'Оптовая цена';
                document.getElementById('couponInfo').innerHTML = `<span class="pos-coupon-badge">${text}</span>`;

                // Пересчитываем корзину для применения скидки
                recalculateCartPrices();
            } else if (data.customer.loyalty_discount_percent > 0) {
                document.getElementById('couponInfo').innerHTML = `<span class="pos-coupon-badge">Скидка ${data.customer.loyalty_discount_percent}%</span>`;
                // Пересчитываем корзину для применения скидки лояльности
                recalculateCartPrices();
            }

            updateTotals();
            showToast('success', `Клиент: ${data.customer.full_name}`);
        } else {
            showToast('error', data.message || 'Клиент не найден');
        }
    } finally {
        isProcessingScan = false;
        lastScannedCode = null; // Сбрасываем память
        closeScanner(); // Закрываем сканер
    }
}

async function scanCoupon(qrData) {
    if (isProcessingScan) return;
    isProcessingScan = true;
    
    try {
        const res = await fetch('/seller/pos/scan-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qr_data: qrData })
        });
        const data = await res.json();
        if (data.success) {
            currentCoupon = data.coupon;
            let text = '';
            if (data.coupon.discount_type === 'percent') {
                text = `Скидка ${data.coupon.discount_value}%`;
                // Пересчитываем корзину для процентной скидки
                recalculateCartPrices();
            } else if (data.coupon.discount_type === 'wholesale') {
                text = 'Оптовая цена';
                // Пересчитываем корзину для оптовой цены
                recalculateCartPrices();
            }
            document.getElementById('couponInfo').innerHTML = `<span class="pos-coupon-badge">${text}</span>`;
            updateTotals();
            showToast('success', `Купон: ${text}`);
        } else {
            showToast('error', data.message || 'Купон не найден');
        }
    } finally {
        isProcessingScan = false;
        lastScannedCode = null; // Сбрасываем память
        closeScanner(); // Закрываем сканер
    }
}

function recalculateCartPrices() {
    cart.forEach(item => {
        if (!item.variant_details) return;
        
        let basePrice = item.basePrice || item.price;
        let finalPrice = basePrice;
        
        // Если есть оптовый купон
        if (currentCoupon && currentCoupon.discount_type === 'wholesale') {
            finalPrice = item.variant_details.wholesale_price || item.variant_details.supplier_price || basePrice;
        }
        // Если есть скидка лояльности клиента
        else if (currentCustomer && currentCustomer.loyalty_discount_percent > 0) {
            finalPrice = basePrice * (1 - currentCustomer.loyalty_discount_percent / 100);
        }
        // Если есть процентная скидка купона
        else if (currentCoupon && currentCoupon.discount_type === 'percent') {
            finalPrice = basePrice * (1 - currentCoupon.discount_value / 100);
        }
        
        item.price = finalPrice;
        item.basePrice = basePrice;
    });
    
    renderCart();
}

// Оплата
window.openPaymentModal = function() {
    if (cart.length === 0) return;
    const { finalAmount } = updateTotals();
    document.getElementById('paymentAmountDisplay').textContent = finalAmount.toLocaleString() + ' с';
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
};

window.selectPaymentMethod = function(method) {
    document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('active'));
    document.querySelector(`[data-method="${method}"]`).classList.add('active');
    selectedPaymentMethod = method;
};

window.calculateChange = function() {
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const { finalAmount } = updateTotals();
    if (paid > finalAmount) {
        document.getElementById('changeDisplay').style.display = 'block';
        document.getElementById('changeAmount').textContent = (paid - finalAmount).toLocaleString() + ' с';
    } else {
        document.getElementById('changeDisplay').style.display = 'none';
    }
};

window.processCheckout = async function() {
    const { finalAmount } = updateTotals();
    const paid = parseFloat(document.getElementById('amountPaid').value) || finalAmount;
    
    // Проверяем выбран ли способ оплаты
    if (!selectedPaymentMethod) {
        showToast('error', 'Выберите способ оплаты');
        return;
    }
    
    // Проверяем есть ли товары
    if (cart.length === 0) {
        showToast('error', 'Корзина пуста');
        return;
    }

    try {
        const res = await fetch('/seller/pos/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                items: cart,
                payment_method: selectedPaymentMethod,
                amount_paid: paid,
                customer_id: currentCustomer?.id,
                coupon_id: currentCoupon?.id
            })
        });

        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            showToast('success', 'Продажа оформлена');
            cart = [];
            currentCustomer = null;
            currentCoupon = null;
            renderCart();
            document.getElementById('customerInfo').style.display = 'none';
            document.getElementById('couponInfo').innerHTML = '';
            location.reload();
        } else {
            showToast('error', data.message || 'Ошибка оформления');
        }
    } catch (error) {
        console.error('Checkout error:', error);
        showToast('error', 'Ошибка сети: ' + error.message);
    }
};

// Уведомления
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `pos-toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

window.removeCustomer = function() {
    currentCustomer = null;
    currentCoupon = null;
    document.getElementById('customerInfo').style.display = 'none';
    document.getElementById('couponInfo').innerHTML = '';
    updateTotals();
};
</script>
@endsection
