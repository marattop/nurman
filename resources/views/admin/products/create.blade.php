@extends('layouts.admin')
@section('page-title', 'Добавить товар')
@section('content')

<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
        <i class="bi bi-box-seam"></i>
    </div>
    <div>
        <h2 style="font-size: 28px; font-weight: 700; margin: 0;">Добавить товар</h2>
        <p style="font-size: 14px; color: #94a3b8; margin: 4px 0 0 0;">Создание нового товара</p>
    </div>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    {{-- Основная информация --}}
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 20px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-box-seam" style="color: #38bdf8;"></i> Основная информация
            </h3>
        </div>
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Артикул</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="article" class="input" value="{{ old('article') }}" placeholder="Оставьте пустым для автогенерации">
                        <button type="button" class="btn btn-outline" onclick="generateArticle()" title="Сгенерировать">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Название <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="name" class="input" value="{{ old('name') }}" required placeholder="Например: Футболка базовая">
                </div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Описание</label>
                <textarea name="description" class="input" rows="4" placeholder="Описание товара...">{{ old('description') }}</textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Категория</label>
                    <select name="category_id" class="input">
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Бренд</label>
                    <input type="text" name="brand" class="input" value="{{ old('brand') }}" placeholder="Например: Zara">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Цена продажи (сом) <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="retail_price" class="input" value="{{ old('retail_price') }}" required min="0" step="0.01" placeholder="0.00">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Цена закупки (сом)</label>
                    <input type="number" name="supplier_price" class="input" value="{{ old('supplier_price') }}" min="0" step="0.01" placeholder="0.00">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Оптовая цена (сом)</label>
                    <input type="number" name="wholesale_price" class="input" value="{{ old('wholesale_price') }}" min="0" step="0.01" placeholder="0.00">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Материал</label>
                    <input type="text" name="material" class="input" value="{{ old('material') }}" placeholder="Например: Хлопок 100%">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Страна производства</label>
                    <input type="text" name="country" class="input" value="{{ old('country') }}" placeholder="Например: Кыргызстан">
                </div>
            </div>
            
            <div>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <span style="color: #f1f5f9; font-weight: 500;">Активный товар</span>
                </label>
            </div>
        </div>
    </div>
    
    {{-- Фото товара --}}
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 20px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <h3 style="font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-image" style="color: #38bdf8;"></i> Фото товара
            </h3>
        </div>
        <div style="padding: 20px;">
            <div style="text-align: center; padding: 3rem; border: 2px dashed rgba(56,189,248,0.3); border-radius: 12px; background: rgba(56,189,248,0.05); cursor: pointer; transition: 0.2s;" onclick="document.getElementById('photo').click()" onmouseover="this.style.background='rgba(56,189,248,0.1)'" onmouseout="this.style.background='rgba(56,189,248,0.05)'">
                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #38bdf8; margin-bottom: 1rem;"></i>
                <p style="margin: 0; font-weight: 600; color: #f1f5f9;">Загрузить из устройства</p>
                <small style="color: #94a3b8; display: block; margin-top: 4px;">PNG, JPG до 2MB</small>
                <input type="file" name="photo" id="photo" accept="image/*" onchange="previewImage(this)" style="display: none;">
            </div>
            <div style="margin-top: 16px; text-align: center;">
                <button type="button" class="btn btn-outline" onclick="openCameraModal()">
                    <i class="bi bi-camera"></i> Сделать фото
                </button>
            </div>
            <img id="preview" src="#" alt="Предпросмотр" style="max-width: 300px; max-height: 300px; object-fit: cover; border-radius: 12px; display: none; margin: 16px auto 0;">
        </div>
    </div>
    
    {{-- Варианты товара --}}
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 20px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-layers" style="color: #38bdf8;"></i> Варианты товара (размеры/цвета)
            </h3>
            <button type="button" class="btn btn-primary" onclick="addVariant()">
                <i class="bi bi-plus-lg"></i> Добавить
            </button>
        </div>
        <div style="padding: 20px;">
            <div id="variants-container">
                <div class="variant-item">
                    <div style="display: grid; grid-template-columns: 2fr 2fr 1fr auto; gap: 12px; align-items: end; margin-bottom: 12px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9; font-size: 14px;">Размер</label>
                            <input type="text" name="variants[0][size]" class="input" placeholder="S, M, L..." required>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9; font-size: 14px;">Цвет</label>
                            <input type="text" name="variants[0][color]" class="input" placeholder="Цвет">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9; font-size: 14px;">Кол-во</label>
                            <input type="number" name="variants[0][quantity]" class="input" placeholder="Кол-во" required>
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger" onclick="removeVariant(this)" style="margin-top: 10px;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Магазины и склады --}}
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 20px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <h3 style="font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-shop" style="color: #38bdf8;"></i> Куда разместить товар?
            </h3>
        </div>
        <div style="padding: 20px;">
            {{-- Переключатель Склад/Витрина --}}
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 12px; color: #f1f5f9;">Место размещения</label>
                <div style="display: flex; gap: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="location_type" value="warehouse" checked onchange="toggleLocation()" style="width: 18px; height: 18px;">
                        <span style="color: #f1f5f9; font-weight: 500;"><i class="bi bi-box-seam" style="color: #38bdf8; margin-right: 6px;"></i> Склад</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="location_type" value="shop" onchange="toggleLocation()" style="width: 18px; height: 18px;">
                        <span style="color: #f1f5f9; font-weight: 500;"><i class="bi bi-shop" style="color: #10b981; margin-right: 6px;"></i> Витрина (магазин)</span>
                    </label>
                </div>
            </div>

            {{-- Склад --}}
            <div id="warehouse-section">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Выберите склад</label>
                <select name="warehouse_id" id="warehouse_id" class="input">
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Магазин --}}
            <div id="shop-section" style="display: none;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">Выберите магазин</label>
                <select name="shop_id" id="shop_id" class="input">
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}" {{ old('shop_id', $firstShopId ?? '') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    {{-- Кнопки --}}
    <div style="display: flex; gap: 12px;">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Создать товар
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline">
            <i class="bi bi-x-lg"></i> Отмена
        </a>
    </div>
</form>

{{-- Модальное окно камеры --}}
<div class="modal fade" id="cameraModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-camera"></i> Сделать фото</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <video id="cameraVideo" autoplay playsinline style="width: 100%; max-width: 640px; border-radius: 8px; background: #000;"></video>
                <canvas id="cameraCanvas" style="display: none;"></canvas>
                <div style="margin-top: 16px; display: flex; gap: 8px; justify-content: center;">
                    <button type="button" class="btn btn-primary" onclick="takePhoto()">
                        <i class="bi bi-camera-fill"></i> Сфотографировать
                    </button>
                    <button type="button" class="btn btn-outline" onclick="stopCamera()">
                        <i class="bi bi-x-lg"></i> Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let variantCounter = 1;

// Переключение Склад/Витрина
function toggleLocation() {
    const locationType = document.querySelector('input[name="location_type"]:checked').value;
    const warehouseSection = document.getElementById('warehouse-section');
    const shopSection = document.getElementById('shop-section');
    const warehouseSelect = document.getElementById('warehouse_id');
    const shopSelect = document.getElementById('shop_id');

    if (locationType === 'warehouse') {
        warehouseSection.style.display = 'block';
        shopSection.style.display = 'none';
        warehouseSelect.removeAttribute('disabled');
        shopSelect.setAttribute('disabled', 'disabled');
    } else {
        warehouseSection.style.display = 'none';
        shopSection.style.display = 'block';
        warehouseSelect.setAttribute('disabled', 'disabled');
        shopSelect.removeAttribute('disabled');
    }
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    toggleLocation();
});

function addVariant() {
    const container = document.getElementById('variants-container');
    const newVariant = document.createElement('div');
    newVariant.className = 'variant-item';
    newVariant.innerHTML = '<div style="display: grid; grid-template-columns: 2fr 2fr 1fr auto; gap: 12px; align-items: end; margin-bottom: 12px;"><div><label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9; font-size: 14px;">Размер</label><input type="text" name="variants[' + variantCounter + '][size]" class="input" placeholder="S, M, L..." required></div><div><label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9; font-size: 14px;">Цвет</label><input type="text" name="variants[' + variantCounter + '][color]" class="input" placeholder="Цвет"></div><div><label style="display: block; font-weight: 600; margin-bottom: 8px; color: #f1f5f9; font-size: 14px;">Кол-во</label><input type="number" name="variants[' + variantCounter + '][quantity]" class="input" placeholder="Кол-во" required></div><div><button type="button" class="btn btn-danger" onclick="removeVariant(this)" style="margin-top: 10px;"><i class="bi bi-trash"></i></button></div></div>';
    container.appendChild(newVariant);
    variantCounter++;
}

function generateArticle() {
    const randomDigits = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
    document.querySelector('input[name="article"]').value = 'ART' + randomDigits;
}

function removeVariant(button) {
    button.closest('.variant-item').remove();
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

let cameraStream = null;

function openCameraModal() {
    const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
    modal.show();
    startCamera();
}

async function startCamera() {
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        document.getElementById('cameraVideo').srcObject = cameraStream;
    } catch (err) {
        alert('Ошибка доступа к камере: ' + err.message);
    }
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
    modal.hide();
}

function takePhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    canvas.toBlob(function(blob) {
        const file = new File([blob], 'camera-photo.jpg', { type: 'image/jpeg' });
        const input = document.getElementById('photo');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        previewImage(input);
        stopCamera();
    }, 'image/jpeg', 0.95);
}
</script>
@endsection
