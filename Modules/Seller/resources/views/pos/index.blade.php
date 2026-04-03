@extends('seller::layouts.seller')
@section('page-title', 'Продажа')
@section('content')
<style>
.pos-container { display: flex; gap: 16px; height: calc(100vh - 140px); }
.pos-products { flex: 1; overflow-y: auto; padding-right: 4px; }
.pos-cart { width: 400px; background: #0f172a; border-radius: 16px; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; border: 1px solid rgba(255,255,255,0.05); }
.product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
.product-card { background: #0f172a; border-radius: 12px; padding: 12px; cursor: pointer; transition: 0.2s; border: 1px solid rgba(255,255,255,0.05); position: relative; overflow: hidden; }
.product-card:hover { background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3); }
.product-card:active { transform: scale(0.98); }
.product-img { width: 100%; height: 120px; border-radius: 8px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: #64748b; margin-bottom: 8px; }
.product-img img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
.product-name { font-size: 14px; font-weight: 500; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.product-price { color: #10b981; font-weight: 600; font-size: 16px; }
.product-stock { font-size: 12px; color: #94a3b8; }
.cart-items { flex: 1; overflow-y: auto; min-height: 200px; }
.cart-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
.cart-item-img { width: 50px; height: 50px; border-radius: 8px; background: rgba(255,255,255,0.05); flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.cart-item-info { flex: 1; }
.cart-item-name { font-size: 14px; font-weight: 500; margin-bottom: 2px; }
.cart-item-price { color: #10b981; font-size: 13px; font-weight: 600; }
.cart-item-qty { display: flex; align-items: center; gap: 12px; margin-top: 6px; }
.qty-btn { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.qty-btn:hover { background: rgba(16,185,129,0.1); border-color: #10b981; color: #10b981; }
.cart-total { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px; margin-top: auto; }
.total-row { display: flex; justify-content: space-between; margin-bottom: 8px; color: #94a3b8; font-size: 14px; }
.total-row.final { color: #f1f5f9; font-size: 20px; font-weight: 700; margin: 8px 0 16px 0; }
.search-box { position: sticky; top: -24px; background: #1e293b; padding: 0 0 16px 0; z-index: 10; margin-top: -24px; padding-top: 24px; }

/* Mobile Tabs */
.pos-tabs { display: none; background: #0f172a; border-radius: 12px; padding: 4px; margin-bottom: 16px; gap: 4px; }
.pos-tab { flex: 1; padding: 10px; text-align: center; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; color: #94a3b8; transition: 0.2s; }
.pos-tab.active { background: #10b981; color: #0f172a; }

/* Floating Cart Button */
.mobile-cart-fab { position: fixed; bottom: 80px; right: 20px; background: #10b981; color: #0f172a; width: 60px; height: 60px; border-radius: 30px; display: none; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(16,185,129,0.4); z-index: 90; transition: 0.3s; }
.mobile-cart-fab .badge { position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; min-width: 22px; height: 22px; border-radius: 11px; font-size: 11px; display: flex; align-items: center; justify-content: center; border: 2px solid #1e293b; }

@media (max-width: 1024px) {
    .pos-container { flex-direction: column; height: auto; min-height: calc(100vh - 200px); padding-bottom: 100px; }
    .pos-cart { width: 100%; height: auto; min-height: 400px; display: none; padding-bottom: 120px; }
    .pos-cart.active { display: flex; }
    .pos-products { display: block; }
    .pos-products.hidden { display: none; }
    .pos-tabs { display: flex; position: sticky; top: 0; z-index: 20; background: #1e293b; padding: 8px 0; margin-top: -8px; }
    .product-grid { grid-template-columns: repeat(2, 1fr); padding-bottom: 20px; }
    .mobile-cart-fab { display: flex; }
    .main-content > div { padding: 12px !important; }
    .search-box { position: static; padding-top: 0; margin-top: 0; }
}

@media (max-width: 480px) {
    .product-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .product-card { padding: 8px; }
    .product-img { height: 100px; }
    .product-name { font-size: 13px; }
    .product-price { font-size: 14px; }
}
</style>

<div class="pos-tabs" id="pos-tabs">
    <div class="pos-tab active" onclick="switchTab('products')">
        <i class="bi bi-grid"></i> Товары
    </div>
    <div class="pos-tab" onclick="switchTab('cart')">
        <i class="bi bi-cart3"></i> Корзина (<span id="tab-cart-count">0</span>)
    </div>
</div>

<div class="pos-container">
    <!-- Товары -->
    <div class="pos-products" id="pos-products-section">
        <div class="search-box">
            <div style="position: relative;">
                <i class="bi bi-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                <input type="text" id="product-search" class="input" placeholder="Поиск товаров..." style="padding-left: 40px;">
            </div>
        </div>
        
        <div class="product-grid" id="products-grid">
            @foreach($products as $product)
            <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock_quantity ?? 0 }})">
                <div class="product-img">
                    @if($product->photo)
                        <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}">
                    @else
                        <i class="bi bi-box" style="font-size: 32px; opacity: 0.3;"></i>
                    @endif
                </div>
                <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                <div class="product-price">{{ number_format($product->price, 0, ',', ' ') }} сом</div>
                <div class="product-stock">{{ $product->stock_quantity ?? 0 }} шт</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Корзина -->
    <div class="pos-cart" id="pos-cart-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 600;"><i class="bi bi-cart3"></i> Корзина</h3>
            <button class="btn btn-danger" style="padding: 6px 12px; font-size: 13px;" onclick="clearCart()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        
        <div class="cart-items" id="cart-items">
            <div style="text-align: center; padding: 60px 20px; color: #64748b;">
                <i class="bi bi-cart" style="font-size: 64px; opacity: 0.1; display: block; margin-bottom: 16px;"></i>
                Корзина пуста
            </div>
        </div>

        <div class="cart-total">
            <div class="total-row">
                <span>Товаров:</span>
                <span id="total-qty">0</span>
            </div>
            <div class="total-row final">
                <span>Итого:</span>
                <span id="total-amount">0 сом</span>
            </div>
            
            <button class="btn btn-primary" style="width: 100%; height: 50px; justify-content: center; font-size: 16px;" onclick="openPaymentModal()">
                <i class="bi bi-cash-stack"></i> Оплатить
            </button>
        </div>
    </div>
</div>

<a href="javascript:void(0)" class="mobile-cart-fab" onclick="switchTab('cart')">
    <i class="bi bi-cart3" style="font-size: 24px;"></i>
    <span class="badge" id="fab-cart-count">0</span>
</a>

<!-- Payment Modal -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 2000; align-items: flex-end; justify-content: center; padding: 0;">
    <div class="card" style="width: 100%; max-width: 500px; border-radius: 24px 24px 0 0; max-height: 90vh; overflow-y: auto; padding-bottom: 40px;">
        <div style="width: 40px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; margin: 0 auto 20px;"></div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="margin: 0; font-size: 20px; font-weight: 700;"><i class="bi bi-wallet2"></i> Оплата</h3>
            <button onclick="closePaymentModal()" style="background: none; border: none; color: #94a3b8; font-size: 24px;"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <div style="background: rgba(16,185,129,0.05); border-radius: 16px; padding: 20px; margin-bottom: 24px; border: 1px solid rgba(16,185,129,0.1);">
            <label style="display: block; color: #94a3b8; font-size: 14px; margin-bottom: 4px;">Сумма к оплате:</label>
            <div style="font-size: 32px; font-weight: 800; color: #10b981;" id="modal-total">0 сом</div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; color: #94a3b8; font-size: 14px; margin-bottom: 12px; font-weight: 500;">Способ оплаты:</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                <button class="btn btn-outline payment-method-btn" onclick="selectPayment('cash', this)" style="flex-direction: column; padding: 16px 8px; gap: 8px;">
                    <i class="bi bi-cash" style="font-size: 24px;"></i>
                    <span style="font-size: 13px; font-weight: 600;">Наличные</span>
                </button>
                <button class="btn btn-outline payment-method-btn" onclick="selectPayment('card', this)" style="flex-direction: column; padding: 16px 8px; gap: 8px;">
                    <i class="bi bi-credit-card" style="font-size: 24px;"></i>
                    <span style="font-size: 13px; font-weight: 600;">Карта</span>
                </button>
                <button class="btn btn-outline payment-method-btn" onclick="selectPayment('transfer', this)" style="flex-direction: column; padding: 16px 8px; gap: 8px;">
                    <i class="bi bi-arrow-left-right" style="font-size: 24px;"></i>
                    <span style="font-size: 13px; font-weight: 600;">Перевод</span>
                </button>
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; color: #94a3b8; font-size: 14px; margin-bottom: 8px; font-weight: 500;">Получено (для сдачи):</label>
            <input type="number" id="paid-amount" class="input" placeholder="Введите сумму" style="height: 54px; font-size: 18px; font-weight: 600;">
        </div>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
            <button class="btn btn-primary" style="flex: 2; height: 54px; justify-content: center; font-size: 16px;" onclick="processSale()">
                <i class="bi bi-check-circle-fill"></i> Подтвердить
            </button>
            <button class="btn btn-outline" style="flex: 1; height: 54px; justify-content: center; background: rgba(255,255,255,0.05); color: #f1f5f9; border-color: rgba(255,255,255,0.1);" onclick="closePaymentModal()">
                Отмена
            </button>
        </div>
    </div>
</div>

<script>
let cart = [];
let selectedPayment = null;

function addToCart(id, name, price, stock) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        if (existing.qty < stock) {
            existing.qty++;
        } else {
            showToast('Недостаточно товара на складе', 'danger');
            return;
        }
    } else {
        if (stock <= 0) {
            showToast('Товара нет в наличии', 'danger');
            return;
        }
        cart.push({ id, name, price, qty: 1, stock });
    }
    renderCart();
    
    // Анимация добавления для мобилки
    if (window.innerWidth <= 1024) {
        const fab = document.querySelector('.mobile-cart-fab');
        fab.style.transform = 'scale(1.2)';
        setTimeout(() => fab.style.transform = 'scale(1)', 200);
    }
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const totalQtySpan = document.getElementById('total-qty');
    const totalAmountSpan = document.getElementById('total-amount');
    const modalTotalSpan = document.getElementById('modal-total');
    const tabCountSpan = document.getElementById('tab-cart-count');
    const fabCountSpan = document.getElementById('fab-cart-count');

    if (cart.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 60px 20px; color: #64748b;">
                <i class="bi bi-cart" style="font-size: 64px; opacity: 0.1; display: block; margin-bottom: 16px;"></i>
                Корзина пуста
            </div>`;
        totalQtySpan.textContent = '0';
        totalAmountSpan.textContent = '0 сом';
        modalTotalSpan.textContent = '0 сом';
        tabCountSpan.textContent = '0';
        fabCountSpan.textContent = '0';
        return;
    }

    let html = '';
    let total = 0;
    let qty = 0;

    cart.forEach(item => {
        total += item.price * item.qty;
        qty += item.qty;
        html += `
            <div class="cart-item">
                <div class="cart-item-img">
                    <i class="bi bi-box" style="opacity: 0.3;"></i>
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${numberFormat(item.price)} сом</div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="changeQty(${item.id}, -1)"><i class="bi bi-dash"></i></button>
                        <span style="font-weight: 600; min-width: 20px; text-align: center;">${item.qty}</span>
                        <button class="qty-btn" onclick="changeQty(${item.id}, 1)"><i class="bi bi-plus"></i></button>
                        <div style="margin-left: auto; color: #f1f5f9; font-weight: 600;">${numberFormat(item.price * item.qty)}</div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    totalQtySpan.textContent = qty;
    totalAmountSpan.textContent = numberFormat(total) + ' сом';
    modalTotalSpan.textContent = numberFormat(total) + ' сом';
    tabCountSpan.textContent = qty;
    fabCountSpan.textContent = qty;
}

function numberFormat(num) {
    return new Intl.NumberFormat('ru-RU').format(num);
}

function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (item) {
        if (delta > 0 && item.qty >= item.stock) {
            showToast('Максимальное количество достигнуто', 'warning');
            return;
        }
        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        renderCart();
    }
}

function clearCart() {
    if (cart.length > 0 && confirm('Очистить корзину?')) {
        cart = [];
        renderCart();
    }
}

function openPaymentModal() {
    if (cart.length === 0) {
        showToast('Корзина пуста', 'warning');
        return;
    }
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

function selectPayment(method, btn) {
    selectedPayment = method;
    document.querySelectorAll('.payment-method-btn').forEach(b => {
        b.style.background = 'rgba(255,255,255,0.05)';
        b.style.borderColor = 'rgba(255,255,255,0.1)';
        b.style.color = '#f1f5f9';
    });
    btn.style.background = 'rgba(16,185,129,0.1)';
    btn.style.borderColor = '#10b981';
    btn.style.color = '#10b981';
    
    if (method === 'cash') {
        document.getElementById('paid-amount').focus();
    }
}

function processSale() {
    if (!selectedPayment) {
        showToast('Выберите способ оплаты', 'warning');
        return;
    }

    const paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);

    if (paid < total && selectedPayment === 'cash') {
        showToast('Сумма оплаты меньше итога', 'warning');
        return;
    }

    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Обработка...';

    fetch('{{ route("seller.sale") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            items: cart,
            payment_method: selectedPayment,
            amount_paid: paid
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Продажа успешно оформлена!', 'success');
            cart = [];
            renderCart();
            closePaymentModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Ошибка оформления', 'danger');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    })
    .catch(err => {
        showToast('Ошибка сети: ' + err.message, 'danger');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function switchTab(tab) {
    const productsSection = document.getElementById('pos-products-section');
    const cartSection = document.getElementById('pos-cart-section');
    const tabs = document.querySelectorAll('.pos-tab');
    const fab = document.querySelector('.mobile-cart-fab');

    if (tab === 'products') {
        productsSection.classList.remove('hidden');
        cartSection.classList.remove('active');
        tabs[0].classList.add('active');
        tabs[1].classList.remove('active');
        fab.style.display = 'flex';
    } else {
        productsSection.classList.add('hidden');
        cartSection.classList.add('active');
        tabs[0].classList.remove('active');
        tabs[1].classList.add('active');
        fab.style.display = 'none';
    }
}

function showToast(message, type = 'success') {
    // В этом проекте может быть своя система уведомлений, 
    // но если нет, используем простой alert или создаем временный toast
    const toast = document.createElement('div');
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.padding = '12px 24px';
    toast.style.borderRadius = '12px';
    toast.style.zIndex = '3000';
    toast.style.color = 'white';
    toast.style.fontWeight = '600';
    toast.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.3)';
    
    if (type === 'success') toast.style.background = '#10b981';
    else if (type === 'danger') toast.style.background = '#ef4444';
    else toast.style.background = '#f59e0b';
    
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = '0.5s';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

// Поиск товаров
document.getElementById('product-search').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.querySelector('.product-name').textContent.toLowerCase();
        card.style.display = name.includes(term) ? 'block' : 'none';
    });
});
</script>
@endsection
