@extends('layouts.warehouse')

@section('page-title', 'Новое списание')
@section('page-subtitle', 'Списание товара со склада')

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
        white-space: nowrap;
    }
    .btn-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.25);
    }
    .btn-outline {
        background: rgba(56, 189, 248, 0.1);
        color: #38bdf8;
        border: 1px solid rgba(56, 189, 248, 0.2);
    }
    .btn-outline:hover {
        background: rgba(56, 189, 248, 0.15);
    }
    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
        border-radius: 8px;
    }
    .text-danger { color: #ef4444 !important; }
    .text-muted { color: #94a3b8 !important; }
    .text-text { color: #f8fafc !important; }
</style>

@if(session('error'))
<div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
</div>
@endif

@if($errors->any())
<div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
    <i class="bi bi-exclamation-triangle"></i>
    <ul style="margin: 8px 0 0 20px; padding: 0;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 24px;">
        <i class="bi bi-trash"></i>
    </div>
    <div>
        <h1 style="font-size: 28px; font-weight: 700; margin: 0;">Новое списание</h1>
        <p style="font-size: 14px; color: #94a3b8; margin: 4px 0 0 0;">Списание товара со склада</p>
    </div>
    <div style="margin-left: auto;">
        <a href="{{ route('warehouse.writeoff.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<form action="{{ route('warehouse.writeoff.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 24px;">
    @csrf

    {{-- Причина и местоположение --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.03); display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-exclamation-triangle" style="color: #ef4444; font-size: 18px;"></i>
            <h3 style="font-size: 16px; font-weight: 600; margin: 0;">Причина и место</h3>
        </div>
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Причина списания <span style="color: #ef4444;">*</span></label>
                    <select name="reason" class="input" required>
                        <option value="">Выберите...</option>
                        <option value="damage">Брак / Повреждение</option>
                        <option value="loss">Потеря</option>
                        <option value="expired">Истёк срок годности</option>
                        <option value="other">Другое</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Местоположение <span style="color: #ef4444;">*</span></label>
                    <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                        <label style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: rgba(56,189,248,0.1); border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="location_type" value="warehouse" checked onchange="updateLocation()" style="accent-color: #38bdf8;">
                            <span style="font-size: 13px;">Склад</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: rgba(16,185,129,0.1); border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="location_type" value="shop" onchange="updateLocation()" style="accent-color: #10b981;">
                            <span style="font-size: 13px;">Магазин</span>
                        </label>
                    </div>
                    <select name="warehouse_id" id="warehouse_id" class="input" required>
                        <option value="">Выберите склад...</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    <select name="shop_id" id="shop_id" class="input" style="display: none;" required disabled>
                        <option value="">Выберите магазин...</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Комментарий</label>
                <textarea name="comment" class="input" rows="3" placeholder="Опишите причину списания..." style="resize: vertical;"></textarea>
            </div>
        </div>
    </div>

    {{-- Товары --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.03); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-box-seam" style="color: #ef4444; font-size: 18px;"></i>
                <h3 style="font-size: 16px; font-weight: 600; margin: 0;">Товары</h3>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="addWriteoffItem()">
                <i class="bi bi-plus-lg"></i> Добавить товар
            </button>
        </div>
        <div style="padding: 20px;">
            <div id="writeoff-items-container">
                <!-- Товары добавляются через JS -->
            </div>
            <p id="no-items-message" style="text-align: center; color: #94a3b8; padding: 40px 20px;">
                <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                Нажмите "Добавить товар" для выбора
            </p>
        </div>
    </div>

    {{-- Кнопки --}}
    <div style="display: flex; gap: 12px;">
        <button type="submit" class="btn btn-danger" style="flex: 1; padding: 14px 24px; font-size: 15px;">
            <i class="bi bi-trash"></i> <span>СПИСАТЬ ТОВАР</span>
        </button>
        <a href="{{ route('warehouse.writeoff.index') }}" class="btn btn-outline" style="padding: 14px 24px; font-size: 15px;">
            <i class="bi bi-arrow-left"></i> ОТМЕНА
        </a>
    </div>
</form>

<script>
const products = @json($products);
let itemCounter = 0;

function updateLocation() {
    const locationType = document.querySelector('input[name="location_type"]:checked').value;
    const warehouseSelect = document.getElementById('warehouse_id');
    const shopSelect = document.getElementById('shop_id');
    
    if (locationType === 'warehouse') {
        warehouseSelect.style.display = 'block';
        warehouseSelect.required = true;
        warehouseSelect.disabled = false;
        shopSelect.required = false;
        shopSelect.disabled = true;
        shopSelect.value = '';
    } else {
        warehouseSelect.style.display = 'none';
        warehouseSelect.required = false;
        warehouseSelect.disabled = true;
        warehouseSelect.value = '';
        shopSelect.style.display = 'block';
        shopSelect.required = true;
        shopSelect.disabled = false;
    }
}

function addWriteoffItem() {
    const container = document.getElementById('writeoff-items-container');
    const noItemsMsg = document.getElementById('no-items-message');
    
    if (noItemsMsg) {
        noItemsMsg.style.display = 'none';
    }
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'writeoff-item';
    itemDiv.style.cssText = 'display: flex; gap: 12px; margin-bottom: 16px; padding: 16px; background: rgba(255,255,255,0.03); border-radius: 12px; align-items: flex-end;';
    
    itemDiv.innerHTML = `
        <div style="flex: 2;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Товар <span style="color: #ef4444;">*</span></label>
            <select name="items[${itemCounter}][variant_id]" class="input" required onchange="updateStockInfo(this)">
                <option value="">Выберите товар</option>
                ${products.map(p => 
                    p.variants.map(v => `
                        <option value="${v.id}" data-stock="${v.stock.reduce((sum, s) => sum + s.quantity, 0)}">
                            ${p.name} — ${v.size} ${v.color ? '(' + v.color + ')' : ''}
                        </option>
                    `).join('')
                ).flat().join('')}
            </select>
        </div>
        <div style="flex: 1;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Количество <span style="color: #ef4444;">*</span></label>
            <input type="number" name="items[${itemCounter}][quantity]" class="input" placeholder="0" required min="1" value="1">
        </div>
        <div style="flex: 1;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f8fafc; font-size: 14px;">Доступно</label>
            <div class="stock-info" style="padding: 10px; background: rgba(148,163,184,0.1); border-radius: 8px; font-size: 13px; color: #94a3b8;">
                Выберите товар
            </div>
        </div>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeWriteoffItem(this)" style="padding: 10px 12px;">
            <i class="bi bi-trash"></i>
        </button>
    `;
    
    container.appendChild(itemDiv);
    itemCounter++;
}

function removeWriteoffItem(button) {
    button.closest('.writeoff-item').remove();
    
    const container = document.getElementById('writeoff-items-container');
    if (container.children.length === 0) {
        const noItemsMsg = document.getElementById('no-items-message');
        if (noItemsMsg) {
            noItemsMsg.style.display = 'block';
        }
    }
}

function updateStockInfo(select) {
    const selectedOption = select.options[select.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock') || 0;
    const stockInfoDiv = select.closest('.writeoff-item').querySelector('.stock-info');
    stockInfoDiv.textContent = stock + ' шт на складах';
    stockInfoDiv.style.color = stock > 0 ? '#10b981' : '#ef4444';
}

// Инициализация
updateLocation();
</script>
@endsection
