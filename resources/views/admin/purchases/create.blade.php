@extends('layouts.admin')
@section('page-title', 'Создать закупку')
@section('content')

<div class="page-header">
    <div class="page-icon"><i class="bi bi-cart-plus"></i></div>
    <div>
        <h1 class="page-title">Создать закупку</h1>
        <p class="page-subtitle">Закупка товаров для пополнения остатков</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0 sm:ml-auto w-full sm:w-auto">
        <a href="{{ route('admin.purchases.deficit') }}" class="btn btn-outline justify-center">
            <i class="bi bi-exclamation-triangle"></i> Дефицит
        </a>
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline justify-center">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

{{-- Режимы --}}
<div class="card" style="padding: 16px 20px; margin-bottom: 24px;">
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.purchases.create', ['mode' => 'all']) }}" 
           class="btn {{ $mode === 'all' ? 'btn-primary' : 'btn-outline' }}">
            <i class="bi bi-grid"></i> Все товары
        </a>
        <a href="{{ route('admin.purchases.create', ['mode' => 'deficit']) }}" 
           class="btn {{ $mode === 'deficit' ? 'btn-primary' : 'btn-outline' }}">
            <i class="bi bi-exclamation-triangle"></i> Дефицит (≤5 шт)
        </a>
    </div>
</div>

<form action="{{ route('admin.purchases.store') }}" method="POST" class="space-y-6">
    @csrf

    {{-- Товары --}}
    <div class="card !p-0 overflow-hidden border-white/5 shadow-xl">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
            <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                <i class="bi bi-box-seam text-accent"></i> Товары для закупки
            </h3>
            <button type="button" class="btn btn-outline py-2 px-3 text-xs" onclick="addPurchaseItem()">
                <i class="bi bi-plus-lg"></i> Добавить товар
            </button>
        </div>
        <div class="p-5">
            <div id="purchase-items-container" class="space-y-4">
                <div class="purchase-item bg-white/5 p-4 rounded-xl border border-white/5 relative group">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-5">
                            <label class="block font-semibold mb-2 text-text text-sm">Товар <span class="text-danger">*</span></label>
                            <select name="items[0][variant_id]" class="input" required onchange="loadVariantInfo(this, 0)">
                                <option value="">Выберите товар</option>
                                @foreach($products as $product)
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
                            <input type="number" name="items[0][quantity]" class="input quantity-input" placeholder="0" required min="1" value="1" onchange="checkSlowMoving(this)">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-semibold mb-2 text-text text-sm">Цена закупки <span class="text-danger">*</span></label>
                            <input type="number" name="items[0][supplier_price]" class="input supplier-price" placeholder="0.00" required min="0" step="0.01" value="0">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold mb-2 text-text text-sm">Инфо</label>
                            <div class="variant-info text-sm text-muted" style="padding: 10px; background: rgba(148,163,184,0.1); border-radius: 8px;">
                                Выберите товар
                            </div>
                        </div>
                    </div>
                    <div class="warning-message" style="display: none; margin-top: 12px; padding: 12px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; color: #f59e0b;">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Внимание:</strong> <span class="warning-text"></span>
                    </div>
                    <button type="button" class="absolute -top-2 -right-2 w-7 h-7 bg-danger text-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity" onclick="removePurchaseItem(this)">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Размещение --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card !p-0 overflow-hidden border-white/5">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-geo-alt text-accent"></i> Куда разместить?
                </h3>
            </div>
            <div class="p-5 space-y-6">
                <div>
                    <label class="block font-semibold mb-3 text-text text-sm">Где разместить товар?</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all border-white/5 bg-white/5 hover:bg-white/10 has-[:checked]:border-accent/50 has-[:checked]:bg-accent/5">
                            <input type="radio" name="location_type" value="shop" checked onchange="toggleLocation()" class="absolute opacity-0">
                            <i class="bi bi-shop text-2xl text-success mb-2"></i>
                            <span class="font-bold text-sm">Витрина</span>
                            <span class="text-[10px] text-muted">Для продажи</span>
                        </label>
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all border-white/5 bg-white/5 hover:bg-white/10 has-[:checked]:border-accent/50 has-[:checked]:bg-accent/5">
                            <input type="radio" name="location_type" value="warehouse" onchange="toggleLocation()" class="absolute opacity-0">
                            <i class="bi bi-building text-2xl text-accent mb-2"></i>
                            <span class="font-bold text-sm">Склад</span>
                            <span class="text-[10px] text-muted">На хранение</span>
                        </label>
                    </div>
                </div>

                <div id="shop-section" class="space-y-2">
                    <label class="block font-semibold text-text text-sm">Выберите магазин</label>
                    <select name="shop_id" id="shop_id" class="input">
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="warehouse-section" class="hidden space-y-2">
                    <label class="block font-semibold text-text text-sm">Выберите склад</label>
                    <select name="warehouse_id" id="warehouse_id" class="input" disabled>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card !p-0 overflow-hidden border-white/5">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-calendar text-accent"></i> Детали закупки
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Дата закупки <span class="text-danger">*</span></label>
                    <input type="date" name="purchase_date" class="input" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Комментарий</label>
                    <textarea name="comment" class="input" rows="3" placeholder="Комментарий к закупке..."></textarea>
                </div>
                <div class="pt-4">
                    <div class="text-sm text-muted mb-2">Общая сумма:</div>
                    <div id="total-amount" class="text-3xl font-bold text-success">0.00 сом</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Кнопки --}}
    <div class="flex flex-col sm:flex-row gap-3 pt-4">
        <button type="submit" class="btn btn-primary px-8 py-3 justify-center shadow-lg shadow-accent/20">
            <i class="bi bi-check2-circle text-lg"></i> <span>Создать закупку</span>
        </button>
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline px-8 py-3 justify-center">
            <i class="bi bi-arrow-left"></i> Отмена
        </a>
    </div>
</form>

<script>
let itemCounter = 1;

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
    } else {
        shopSection.classList.add('hidden');
        warehouseSection.classList.remove('hidden');
        shopSelect.disabled = true;
        warehouseSelect.disabled = false;
    }
}

function addPurchaseItem() {
    const container = document.getElementById('purchase-items-container');
    const newVariant = document.createElement('div');
    newVariant.className = 'purchase-item bg-white/5 p-4 rounded-xl border border-white/5 relative group animate-in fade-in slide-in-from-top-2 duration-300';
    newVariant.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-5">
                <label class="block font-semibold mb-2 text-text text-sm">Товар <span class="text-danger">*</span></label>
                <select name="items[${itemCounter}][variant_id]" class="input" required onchange="loadVariantInfo(this, ${itemCounter})">
                    <option value="">Выберите товар</option>
                    @foreach($products as $product)
                        @foreach($product->variants as $variant)
                            <option value="{{ $variant->id }}" data-supplier-price="{{ $variant->supplier_price ?? 0 }}">
                                {{ $product->name }} — {{ $variant->size }} {{ $variant->color ? '(' . $variant->color . ')' : '' }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-2 text-text text-sm">Кол-во <span class="text-danger">*</span></label>
                <input type="number" name="items[${itemCounter}][quantity]" class="input quantity-input" placeholder="0" required min="1" value="1" onchange="checkSlowMoving(this)">
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-2 text-text text-sm">Цена закупки <span class="text-danger">*</span></label>
                <input type="number" name="items[${itemCounter}][supplier_price]" class="input supplier-price" placeholder="0.00" required min="0" step="0.01" value="0">
            </div>
            <div class="md:col-span-3">
                <label class="block font-semibold mb-2 text-text text-sm">Инфо</label>
                <div class="variant-info text-sm text-muted" style="padding: 10px; background: rgba(148,163,184,0.1); border-radius: 8px;">
                    Выберите товар
                </div>
            </div>
        </div>
        <div class="warning-message" style="display: none; margin-top: 12px; padding: 12px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; color: #f59e0b;">
            <i class="bi bi-exclamation-triangle"></i> <strong>Внимание:</strong> <span class="warning-text"></span>
        </div>
        <button type="button" class="absolute -top-2 -right-2 w-7 h-7 bg-danger text-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity" onclick="removePurchaseItem(this)">
            <i class="bi bi-x"></i>
        </button>
    `;
    container.appendChild(newVariant);
    itemCounter++;
}

function removePurchaseItem(button) {
    const items = document.querySelectorAll('.purchase-item');
    if (items.length > 1) {
        button.closest('.purchase-item').remove();
        calculateTotal();
    } else {
        alert('Должен быть хотя бы один товар');
    }
}

function loadVariantInfo(select, index) {
    const option = select.options[select.selectedIndex];
    const row = select.closest('.purchase-item');
    const priceInput = row.querySelector('.supplier-price');
    const infoDiv = row.querySelector('.variant-info');
    const warningDiv = row.querySelector('.warning-message');
    const warningText = row.querySelector('.warning-text');
    
    if (!option.value) {
        infoDiv.textContent = 'Выберите товар';
        warningDiv.style.display = 'none';
        return;
    }
    
    // Загружаем информацию через AJAX
    fetch(`/admin/purchases/variant/${option.value}/info`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const v = data.variant;
                
                // Автозаполнение цены
                if (v.supplier_price > 0 && priceInput.value == 0) {
                    priceInput.value = v.supplier_price;
                }
                
                // Информация о товаре
                let info = `<div><strong>Остаток:</strong> ${v.current_qty} шт</div>`;
                info += `<div><strong>Продаж за 30 дней:</strong> ${v.sales_last_30_days} шт</div>`;
                if (v.last_sale_at) {
                    info += `<div><strong>Последняя продажа:</strong> ${new Date(v.last_sale_at).toLocaleDateString('ru-RU')}</div>`;
                }
                
                // Предупреждение о медленно продающемся товаре
                if (v.is_slow_moving || v.turnover_days > 30) {
                    warningDiv.style.display = 'block';
                    warningText.textContent = `Товар продается медленно (1 раз в ${v.turnover_days} дней). Последняя продажа была ${v.last_sale_at ? new Date(v.last_sale_at).toLocaleDateString('ru-RU') : 'давно'}. На складе еще есть ${v.current_qty} шт.`;
                } else {
                    warningDiv.style.display = 'none';
                }
                
                infoDiv.innerHTML = info;
            }
        })
        .catch(e => {
            console.error(e);
            infoDiv.textContent = 'Ошибка загрузки';
        });
    
    calculateTotal();
}

function checkSlowMoving(input) {
    const row = input.closest('.purchase-item');
    const select = row.querySelector('select');
    if (select.value) {
        loadVariantInfo(select, row);
    }
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.purchase-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('input[name*="[quantity]"]').value) || 0;
        const price = parseFloat(item.querySelector('.supplier-price').value) || 0;
        total += quantity * price;
    });
    document.getElementById('total-amount').textContent = total.toFixed(2) + ' сом';
}

document.addEventListener('DOMContentLoaded', function() {
    toggleLocation();
    
    document.querySelectorAll('.purchase-item').forEach(item => {
        item.querySelector('input[name*="[quantity]"]').addEventListener('input', calculateTotal);
        item.querySelector('.supplier-price').addEventListener('input', calculateTotal);
    });
    
    calculateTotal();
});
</script>
@endsection
