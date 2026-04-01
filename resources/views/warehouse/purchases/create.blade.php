@extends('layouts.storekeeper')

@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-cart-plus"></i></div>
    <div>
        <h1 class="page-title">Новая приёмка</h1>
        <p class="page-subtitle">Поступление товаров на склад</p>
    </div>
    <div style="margin-left: auto;">
        <a href="{{ route('warehouse.purchases.index') }}" class="btn btn-outline justify-center">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<form action="{{ route('warehouse.purchases.store') }}" method="POST" class="space-y-6">
    @csrf

    {{-- Номер накладной и дата --}}
    <div class="card !p-0 overflow-hidden border-white/5">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5">
            <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                <i class="bi bi-receipt" style="color: #22c55e;"></i> Документ
            </h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Накладная №</label>
                    <input type="text" name="invoice_number" id="invoice_number" class="input"
                        placeholder="PUR-... (оставьте пустым для автогенерации)">
                    <p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">
                        <i class="bi bi-info-circle"></i> Если не заполнить, номер сгенерируется автоматически
                    </p>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Дата закупки <span class="text-danger">*</span></label>
                    <input type="date" name="purchase_date" class="input" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Товар --}}
    <div class="card !p-0 overflow-hidden border-white/5 shadow-xl">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
            <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                <i class="bi bi-box-seam" style="color: #22c55e;"></i> Товар для закупки
            </h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-6">
                    <label class="block font-semibold mb-2 text-text text-sm">Товар <span class="text-danger">*</span></label>
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
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-2 text-text text-sm">Кол-во <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="input quantity-input" placeholder="0" required min="1" value="1">
                </div>
                <div class="md:col-span-4">
                    <label class="block font-semibold mb-2 text-text text-sm">Цена закупки <span class="text-danger">*</span></label>
                    <input type="number" name="supplier_price" class="input supplier-price" placeholder="0.00" required min="0" step="0.01" value="0">
                </div>
            </div>
        </div>
    </div>

    {{-- Размещение --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card !p-0 overflow-hidden border-white/5">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-geo-alt" style="color: #22c55e;"></i> Куда разместить?
                </h3>
            </div>
            <div class="p-5 space-y-6">
                <div>
                    <label class="block font-semibold mb-3 text-text text-sm">Где разместить товар?</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all border-white/5 bg-white/5 hover:bg-white/10 has-[:checked]:border-success/50 has-[:checked]:bg-success/5">
                            <input type="radio" name="location_type" value="shop" checked onchange="toggleLocation()" class="absolute opacity-0">
                            <i class="bi bi-shop text-2xl text-success mb-2"></i>
                            <span class="font-bold text-sm">Витрина</span>
                            <span class="text-[10px] text-muted">Для продажи</span>
                        </label>
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all border-white/5 bg-white/5 hover:bg-white/10 has-[:checked]:border-success/50 has-[:checked]:bg-success/5">
                            <input type="radio" name="location_type" value="warehouse" onchange="toggleLocation()" class="absolute opacity-0">
                            <i class="bi bi-building text-2xl text-success mb-2"></i>
                            <span class="font-bold text-sm">Склад</span>
                            <span class="text-[10px] text-muted">На хранение</span>
                        </label>
                    </div>
                </div>

                <div id="shop-section" class="space-y-2">
                    <label class="block font-semibold text-text text-sm">Выберите магазин</label>
                    <select name="shop_id" id="shop_id" class="input">
                        <option value="">Выберите магазин...</option>
                        @foreach($shops ?? [] as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="warehouse-section" class="hidden space-y-2">
                    <label class="block font-semibold text-text text-sm">Выберите склад</label>
                    <select name="warehouse_id" id="warehouse_id" class="input" disabled>
                        <option value="">Выберите склад...</option>
                        @foreach($warehouses ?? [] as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card !p-0 overflow-hidden border-white/5">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-calendar" style="color: #22c55e;"></i> Детали приёмки
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Комментарий</label>
                    <textarea name="comment" class="input" rows="3" placeholder="Комментарий к приёмке..."></textarea>
                </div>
                <div class="pt-4">
                    <div class="text-sm text-muted mb-2">Общая сумма:</div>
                    <div id="total-amount" class="text-3xl font-bold" style="color: #22c55e;">0.00 сом</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Кнопки --}}
    <div class="flex flex-col sm:flex-row gap-3 pt-4">
        <button type="submit" class="btn btn-success px-8 py-3 justify-center shadow-lg shadow-success/20">
            <i class="bi bi-check2-circle text-lg"></i> <span>Создать приёмку</span>
        </button>
        <a href="{{ route('warehouse.purchases.index') }}" class="btn btn-outline px-8 py-3 justify-center">
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
        shopSection.classList.remove('hidden');
        warehouseSection.classList.add('hidden');
        shopSelect.disabled = false;
        warehouseSelect.disabled = true;
        warehouseSelect.value = '';
    } else {
        warehouseSection.classList.remove('hidden');
        shopSection.classList.add('hidden');
        warehouseSelect.disabled = false;
        shopSelect.disabled = true;
        shopSelect.value = '';
    }
}

function updateSupplierPrice(select) {
    const selectedOption = select.options[select.selectedIndex];
    const supplierPrice = selectedOption.getAttribute('data-supplier-price');
    const quantityInput = select.closest('.grid').querySelector('.quantity-input');
    const supplierPriceInput = select.closest('.grid').querySelector('.supplier-price');
    
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
