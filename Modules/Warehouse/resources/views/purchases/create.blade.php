@extends('layouts.warehouse')

@section('page-title', 'Новая приёмка')
@section('page-subtitle', 'Поступление товаров на склад')

@section('content')
<style>
    .card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
    }
    .input {
        width: 100%;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 10px 14px;
        color: #f8fafc;
        font-size: 14px;
        box-sizing: border-box;
    }
    .input:focus {
        outline: none;
        border-color: #38bdf8;
    }
    .input::placeholder {
        color: #64748b;
    }
    input[type="date"] {
        color-scheme: dark;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        border: none;
        font-size: 14px;
    }
    .btn-primary {
        background: #38bdf8;
        color: #0f172a;
    }
    .btn-primary:hover {
        background: #0ea5e9;
    }
    .btn-outline {
        background: rgba(56, 189, 248, 0.1);
        color: #38bdf8;
        border: 1px solid rgba(56, 189, 248, 0.2);
    }
    .btn-outline:hover {
        background: rgba(56, 189, 248, 0.15);
    }
    .text-success { color: #10b981 !important; }
    .text-danger { color: #ef4444 !important; }
    .text-muted { color: #94a3b8 !important; }
    .text-accent { color: #38bdf8 !important; }
    .text-text { color: #f8fafc !important; }
    .bg-white\\/5 { background: rgba(255, 255, 255, 0.05) !important; }
    .border-white\\/5 { border-color: rgba(255, 255, 255, 0.05) !important; }
    .has-\\:\\[\\:checked\\]\\:border-accent\\/50:has(:checked) {
        border-color: rgba(56, 189, 248, 0.5);
    }
    .has-\\:\\[\\:checked\\]\\:bg-accent\\/5:has(:checked) {
        background: rgba(56, 189, 248, 0.05);
    }
    .has-\\:\\[\\:checked\\]\\:border-success\\/50:has(:checked) {
        border-color: rgba(16, 185, 129, 0.5);
    }
    .has-\\:\\[\\:checked\\]\\:bg-success\\/5:has(:checked) {
        background: rgba(16, 185, 129, 0.05);
    }
</style>

<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 24px;">
        <i class="bi bi-cart-plus"></i>
    </div>
    <div>
        <h1 style="font-size: 28px; font-weight: 700; margin: 0;">Новая приёмка</h1>
        <p style="font-size: 14px; color: #94a3b8; margin: 4px 0 0 0;">Поступление товаров на склад</p>
    </div>
    <div style="margin-left: auto;">
        <a href="{{ route('warehouse.purchases.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<form action="{{ route('warehouse.purchases.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 24px;">
    @csrf

    {{-- Номер накладной и дата --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.03); display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-receipt" style="color: #38bdf8; font-size: 18px;"></i>
            <h3 style="font-size: 16px; font-weight: 600; margin: 0;">Документ</h3>
        </div>
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Накладная №</label>
                    <input type="text" name="invoice_number" id="invoice_number" class="input"
                        placeholder="PUR-... (оставьте пустым для автогенерации)">
                    <p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">
                        <i class="bi bi-info-circle"></i> Если не заполнить, номер сгенерируется автоматически
                    </p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Дата закупки <span style="color: #ef4444;">*</span></label>
                    <input type="date" name="purchase_date" class="input" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Товар --}}
    <div class="card" style="padding: 0; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.03); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-box-seam" style="color: #38bdf8; font-size: 18px;"></i>
                <h3 style="font-size: 16px; font-weight: 600; margin: 0;">Товар для закупки</h3>
            </div>
        </div>
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 16px;">
                <div style="grid-column: span 6;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Товар <span style="color: #ef4444;">*</span></label>
                    <select name="variant_id" class="input" required onchange="updateSupplierPrice(this)">
                        <option value="">Выберите товар</option>
                        @foreach($products ?? [] as $product)
                            @foreach($product->variants as $variant)
                                <option value="{{ $variant->id }}"
                                    data-supplier-price="{{ $variant->supplier_price ?? 0 }}">
                                    {{ $product->name }} — {{ $variant->size }} {{ $variant->color ? '(' . $variant->color . ')' : '' }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Кол-во <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="quantity" class="input quantity-input" placeholder="0" required min="1" value="1">
                </div>
                <div style="grid-column: span 4;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Цена закупки <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="supplier_price" class="input supplier-price" placeholder="0.00" required min="0" step="0.01" value="0">
                </div>
            </div>
        </div>
    </div>

    {{-- Размещение --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.03); display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-geo-alt" style="color: #38bdf8; font-size: 18px;"></i>
                <h3 style="font-size: 16px; font-weight: 600; margin: 0;">Куда разместить?</h3>
            </div>
            <div style="padding: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 12px; color: #f8fafc; font-size: 14px;">Где разместить товар?</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <label style="display: flex; flex-direction: column; padding: 16px; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; cursor: pointer; transition: 0.2s; background: rgba(255,255,255,0.03);">
                            <input type="radio" name="location_type" value="shop" checked onchange="toggleLocation()" style="opacity: 0; position: absolute;">
                            <i class="bi bi-shop" style="font-size: 24px; color: #10b981; margin-bottom: 8px;"></i>
                            <span style="font-weight: 600; font-size: 14px;">Витрина</span>
                            <span style="font-size: 11px; color: #94a3b8;">Для продажи</span>
                        </label>
                        <label style="display: flex; flex-direction: column; padding: 16px; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; cursor: pointer; transition: 0.2s; background: rgba(255,255,255,0.03);">
                            <input type="radio" name="location_type" value="warehouse" onchange="toggleLocation()" style="opacity: 0; position: absolute;">
                            <i class="bi bi-building" style="font-size: 24px; color: #38bdf8; margin-bottom: 8px;"></i>
                            <span style="font-weight: 600; font-size: 14px;">Склад</span>
                            <span style="font-size: 11px; color: #94a3b8;">На хранение</span>
                        </label>
                    </div>
                </div>

                <div id="shop-section" style="margin-top: 16px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Выберите магазин</label>
                    <select name="shop_id" id="shop_id" class="input">
                        <option value="">Выберите магазин...</option>
                        @foreach($shops ?? [] as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="warehouse-section" style="margin-top: 16px; display: none;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Выберите склад</label>
                    <select name="warehouse_id" id="warehouse_id" class="input" disabled>
                        <option value="">Выберите склад...</option>
                        @foreach($warehouses ?? [] as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.03); display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-calendar" style="color: #38bdf8; font-size: 18px;"></i>
                <h3 style="font-size: 16px; font-weight: 600; margin: 0;">Детали приёмки</h3>
            </div>
            <div style="padding: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Комментарий</label>
                    <textarea name="comment" class="input" rows="3" placeholder="Комментарий к приёмке..." style="resize: vertical;"></textarea>
                </div>
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.05);">
                    <div style="font-size: 13px; color: #94a3b8; margin-bottom: 8px;">Общая сумма:</div>
                    <div id="total-amount" style="font-size: 32px; font-weight: 700; color: #10b981;">0.00 сом</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Кнопки --}}
    <div style="display: flex; gap: 12px; padding-top: 8px;">
        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px 24px; font-size: 15px; box-shadow: 0 10px 15px -3px rgba(56, 189, 248, 0.2);">
            <i class="bi bi-check2-circle" style="font-size: 18px;"></i> <span>Создать приёмку</span>
        </button>
        <a href="{{ route('warehouse.purchases.index') }}" class="btn btn-outline" style="padding: 12px 24px; font-size: 15px;">
            <i class="bi bi-arrow-left"></i> Отмена
        </a>
    </div>
</form>

<script>
function toggleLocation() {
    const locationType = document.querySelector('input[name="location_type"]:checked').value;
    const shopSection = document.getElementById('shop-section');
    const warehouseSection = document.getElementById('warehouse-section');
    const shopSelect = document.getElementById('shop_id');
    const warehouseSelect = document.getElementById('warehouse_id');

    if (locationType === 'shop') {
        shopSection.style.display = 'block';
        warehouseSection.style.display = 'none';
        shopSelect.disabled = false;
        warehouseSelect.disabled = true;
        warehouseSelect.value = '';
    } else {
        warehouseSection.style.display = 'block';
        shopSection.style.display = 'none';
        warehouseSelect.disabled = false;
        shopSelect.disabled = true;
        shopSelect.value = '';
    }
}

function updateSupplierPrice(select) {
    const selectedOption = select.options[select.selectedIndex];
    const supplierPrice = selectedOption.getAttribute('data-supplier-price');
    const supplierPriceInput = select.closest('div[style*="grid"]').querySelector('.supplier-price');
    
    if (supplierPrice && supplierPrice > 0) {
        supplierPriceInput.value = supplierPrice;
    }
    
    calculateTotal();
}

function calculateTotal() {
    const quantityInput = document.querySelector('.quantity-input');
    const supplierPriceInput = document.querySelector('.supplier-price');
    const totalAmount = document.getElementById('total-amount');
    
    const quantity = parseFloat(quantityInput?.value) || 0;
    const price = parseFloat(supplierPriceInput?.value) || 0;
    const total = quantity * price;
    
    totalAmount.textContent = total.toFixed(2) + ' сом';
}

document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.querySelector('.quantity-input');
    const supplierPriceInput = document.querySelector('.supplier-price');
    
    if (quantityInput) {
        quantityInput.addEventListener('input', calculateTotal);
    }
    if (supplierPriceInput) {
        supplierPriceInput.addEventListener('input', calculateTotal);
    }
    
    toggleLocation();
});
</script>
@endsection
