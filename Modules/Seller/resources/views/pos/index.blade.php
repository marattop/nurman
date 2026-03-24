@extends('seller::layouts.seller')
@section('page-title', 'Продажа')
@section('content')
<style>
.pos-container { display: flex; gap: 16px; height: calc(100vh - 100px); }
.pos-products { flex: 1; overflow-y: auto; }
.pos-cart { width: 400px; background: #0f172a; border-radius: 16px; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; }
.product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
.product-card { background: #0f172a; border-radius: 12px; padding: 12px; cursor: pointer; transition: 0.2s; border: 1px solid rgba(255,255,255,0.05); }
.product-card:hover { background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3); }
.product-img { width: 100%; height: 120px; border-radius: 8px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: #64748b; margin-bottom: 8px; }
.product-img img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
.product-name { font-size: 14px; font-weight: 500; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.product-price { color: #10b981; font-weight: 600; font-size: 16px; }
.product-stock { font-size: 12px; color: #94a3b8; }
.cart-items { flex: 1; overflow-y: auto; }
.cart-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
.cart-item-img { width: 60px; height: 60px; border-radius: 8px; background: rgba(255,255,255,0.05); flex-shrink: 0; }
.cart-item-info { flex: 1; }
.cart-item-name { font-size: 14px; font-weight: 500; margin-bottom: 4px; }
.cart-item-price { color: #10b981; font-size: 13px; }
.cart-item-qty { display: flex; align-items: center; gap: 8px; margin-top: 4px; }
.qty-btn { width: 28px; height: 28px; border-radius: 6px; background: rgba(255,255,255,0.1); border: none; color: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.qty-btn:hover { background: rgba(255,255,255,0.2); }
.cart-total { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px; margin-top: auto; }
.total-row { display: flex; justify-content: space-between; margin-bottom: 8px; color: #94a3b8; }
.total-row.final { color: #f1f5f9; font-size: 20px; font-weight: 700; margin-bottom: 16px; }
.search-box { position: sticky; top: 0; background: #1e293b; padding-bottom: 16px; z-index: 10; }
@media (max-width: 1024px) { .pos-container { flex-direction: column; } .pos-cart { width: 100%; } }
</style>

<div class="pos-container">
    <!-- Товары -->
    <div class="pos-products">
        <div class="search-box">
            <input type="text" id="product-search" class="input" placeholder="Поиск товаров..." style="max-width: 400px;">
        </div>
        
        <div class="product-grid" id="products-grid">
            @foreach($products as $product)
            <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock_quantity ?? 0 }})">
                <div class="product-img">
                    @if($product->photo)
                        <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}">
                    @else
                        <i class="bi bi-box" style="font-size: 32px; opacity: 0.3;"></i>
                    @endif
                </div>
                <div class="product-name">{{ Str::limit($product->name, 25) }}</div>
                <div class="product-price">{{ number_format($product->price, 0) }} сом</div>
                <div class="product-stock">{{ $product->stock_quantity ?? 0 }} шт</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Корзина -->
    <div class="pos-cart">
        <h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600;"><i class="bi bi-cart3"></i> Корзина</h3>
        
        <div class="cart-items" id="cart-items">
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="bi bi-cart" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
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
            
            <button class="btn btn-primary" style="width: 100%; margin-bottom: 8px;" onclick="openPaymentModal()">
                <i class="bi bi-cash"></i> Оплатить
            </button>
            <button class="btn btn-danger" style="width: 100%;" onclick="clearCart()">
                <i class="bi bi-trash"></i> Очистить
            </button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 400px; max-height: 80vh; overflow-y: auto;">
        <h3 style="margin: 0 0 20px 0;"><i class="bi bi-wallet2"></i> Оплата</h3>
        
        <div style="margin-bottom: 16px;">
            <label style="display: block; color: #94a3b8; font-size: 13px; margin-bottom: 8px;">Сумма к оплате:</label>
            <div style="font-size: 28px; font-weight: 700; color: #10b981;" id="modal-total">0 сом</div>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; color: #94a3b8; font-size: 13px; margin-bottom: 8px;">Способ оплаты:</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
                <button class="btn btn-outline" onclick="selectPayment('cash', this)" style="flex-direction: column; padding: 12px;">
                    <i class="bi bi-cash" style="font-size: 20px; margin-bottom: 4px;"></i>
                    <span style="font-size: 12px;">Наличные</span>
                </button>
                <button class="btn btn-outline" onclick="selectPayment('card', this)" style="flex-direction: column; padding: 12px;">
                    <i class="bi bi-credit-card" style="font-size: 20px; margin-bottom: 4px;"></i>
                    <span style="font-size: 12px;">Карта</span>
                </button>
                <button class="btn btn-outline" onclick="selectPayment('transfer', this)" style="flex-direction: column; padding: 12px;">
                    <i class="bi bi-arrow-left-right" style="font-size: 20px; margin-bottom: 4px;"></i>
                    <span style="font-size: 12px;">Перевод</span>
                </button>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; color: #94a3b8; font-size: 13px; margin-bottom: 8px;">Получено:</label>
            <input type="number" id="paid-amount" class="input" placeholder="0">
        </div>

        <div style="display: flex; gap: 12px;">
            <button class="btn btn-primary" style="flex: 1;" onclick="processSale()">
                <i class="bi bi-check-lg"></i> Подтвердить
            </button>
            <button class="btn btn-outline" style="flex: 1;" onclick="closePaymentModal()">
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
            alert('Недостаточно товара на складе');
            return;
        }
    } else {
        cart.push({ id, name, price, qty: 1, stock });
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items');
    if (cart.length === 0) {
        container.innerHTML = `<div style="text-align: center; padding: 40px 20px; color: #64748b;"><i class="bi bi-cart" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>Корзина пуста</div>`;
        document.getElementById('total-qty').textContent = '0';
        document.getElementById('total-amount').textContent = '0 сом';
        document.getElementById('modal-total').textContent = '0 сом';
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
                <div class="cart-item-img"></div>
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${item.price} сом</div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="changeQty(${item.id}, -1)"><i class="bi bi-dash"></i></button>
                        <span>${item.qty}</span>
                        <button class="qty-btn" onclick="changeQty(${item.id}, 1)"><i class="bi bi-plus"></i></button>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    document.getElementById('total-qty').textContent = qty;
    document.getElementById('total-amount').textContent = total + ' сом';
    document.getElementById('modal-total').textContent = total + ' сом';
}

function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (item) {
        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        renderCart();
    }
}

function clearCart() {
    cart = [];
    renderCart();
}

function openPaymentModal() {
    if (cart.length === 0) {
        alert('Корзина пуста');
        return;
    }
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

function selectPayment(method, btn) {
    selectedPayment = method;
    document.querySelectorAll('#paymentModal .btn-outline').forEach(b => {
        b.style.background = 'rgba(16,185,129,0.1)';
        b.style.color = '#10b981';
    });
    btn.style.background = 'rgba(16,185,129,0.2)';
    btn.style.color = '#10b981';
}

function processSale() {
    if (!selectedPayment) {
        alert('Выберите способ оплаты');
        return;
    }

    const paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);

    if (paid < total && selectedPayment === 'cash') {
        alert('Недостаточная сумма оплаты');
        return;
    }

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
            alert('Продажа оформлена!');
            cart = [];
            renderCart();
            closePaymentModal();
            location.reload();
        } else {
            alert(data.message || 'Ошибка оформления');
        }
    })
    .catch(err => alert('Ошибка: ' + err.message));
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
