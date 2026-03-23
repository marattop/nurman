@extends('layouts.app')

@section('title', 'Редактировать товар - Администратор')

@section('page-title', 'Редактировать товар')

@section('content')
<style>
    :root {
        --primary: var(--accent-primary);
        --primary-dark: var(--accent-primary-dark);
        --primary-light: #D1FAE5;
        --gradient: linear-gradient(135deg, var(--accent-primary), var(--accent-primary-dark));
    }
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
    }
    .btn-primary {
        background: var(--gradient);
        border: none;
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--accent-primary-dark), #047857);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    .btn-outline-secondary, .btn-outline-success, .btn-outline-danger {
        border-radius: 8px;
        padding: 0.625rem 1.25rem;
        font-weight: 600;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 0.625rem 0.875rem;
        transition: all 0.2s;
        font-size: 0.9rem;
        background: var(--bg-card);
        color: var(--text-primary);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        background: var(--bg-card);
        color: var(--text-primary);
    }
    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    .photo-upload {
        border: 2px dashed var(--primary);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: var(--primary-light);
        cursor: pointer;
        transition: all 0.2s;
    }
    .photo-upload:hover {
        background: #A7F3D0;
        border-color: var(--primary-dark);
    }
    .photo-upload i {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }
    .photo-preview {
        max-width: 200px;
        border-radius: 8px;
        margin-top: 1rem;
        display: none;
    }
    .section-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gradient);
        color: white;
        margin-right: 0.75rem;
        font-size: 1rem;
    }
    .section-title {
        display: flex;
        align-items: center;
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
    }
    .required-mark {
        color: #DC2626;
        margin-left: 2px;
    }
    .variant-item {
        background: var(--table-header-bg);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border: 1px solid var(--border-color);
    }
    .form-text {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }
</style>

<div class="container-fluid">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- Основная информация --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <span>Основная информация</span>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="article" class="form-label">Артикул</label>
                        <div class="input-group">
                            <input type="text" name="article" id="article" class="form-control @error('article') is-invalid @enderror"
                                   value="{{ old('article', $product->article) }}" placeholder="Оставьте пустым для автогенерации">
                            <button type="button" class="btn btn-outline-secondary" onclick="generateArticle()" title="Сгенерировать артикул">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                        @error('article')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Название <span class="required-mark">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name) }}" required placeholder="Например: Футболка базовая">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                              rows="4" placeholder="Описание товара...">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="category_id" class="form-label">Категория</label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">Выберите категорию</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="brand" class="form-label">Бренд</label>
                        <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror"
                               value="{{ old('brand', $product->brand) }}" placeholder="Например: Zara">
                        @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="retail_price" class="form-label">Цена продажи (сом) <span class="required-mark">*</span></label>
                        <input type="number" name="retail_price" id="retail_price" class="form-control @error('retail_price') is-invalid @enderror"
                               value="{{ old('retail_price', $product->retail_price) }}" required min="0" step="0.01" placeholder="0.00">
                        @error('retail_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="supplier_price" class="form-label">Цена закупки (сом)</label>
                        <input type="number" name="supplier_price" id="supplier_price" class="form-control @error('supplier_price') is-invalid @enderror"
                               value="{{ old('supplier_price', $product->variants->first()->supplier_price ?? 0) }}" min="0" step="0.01" placeholder="0.00">
                        <small class="form-text text-muted">Закупочная цена первого варианта</small>
                        @error('supplier_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Характеристики --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="bi bi-sliders"></i>
                    </div>
                    <span>Характеристики</span>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="material" class="form-label">Материал</label>
                        <input type="text" name="material" id="material" class="form-control @error('material') is-invalid @enderror"
                               value="{{ old('material', $product->material) }}" placeholder="Например: Хлопок 100%">
                        @error('material')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="country" class="form-label">Страна производства</label>
                        <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror"
                               value="{{ old('country', $product->country) }}" placeholder="Например: Кыргызстан">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Активный товар</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Фото товара --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="bi bi-image"></i>
                    </div>
                    <span>Фото товара</span>
                </div>

                <div class="text-center mb-3">
                    <div class="photo-upload" onclick="document.getElementById('photo').click()" style="cursor: pointer; padding: 2rem; border: 2px dashed var(--border-color); border-radius: 8px; background: var(--bg-secondary);">
                        <i class="bi bi-cloud-upload" style="font-size: 3rem; color: var(--primary);"></i>
                        <p class="mb-1 fw-semibold">Загрузить новое фото</p>
                        <small class="form-text">PNG, JPG до 2MB</small>
                        <input type="file" name="photo" id="photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary" onclick="openCameraModal()">
                            <i class="bi bi-camera"></i> Сделать фото
                        </button>
                    </div>
                </div>
                
                @if($product->photo && file_exists(storage_path('app/public/' . $product->photo)))
                    <div style="position: relative; display: inline-block;">
                        <img id="preview" class="photo-preview" src="{{ asset('storage/' . $product->photo) }}" alt="Текущее фото" style="display: block; max-width: 300px; border-radius: 8px;">
                        <small class="text-muted" style="display: block; text-align: center; margin-top: 0.5rem;">✓ Текущее фото</small>
                    </div>
                @else
                    <img id="preview" class="photo-preview" src="#" alt="Предпросмотр" style="display: none;">
                @endif
                @error('photo')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

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
                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-lg" onclick="takePhoto()">
                                <i class="bi bi-camera-fill"></i> Сфотографировать
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg" onclick="stopCamera()">
                                <i class="bi bi-x-lg"></i> Отмена
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Варианты товара --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="bi bi-layers"></i>
                    </div>
                    <span>Варианты (размеры/цвета)</span>
                </div>
                
                <input type="hidden" name="shop_id" id="main_shop_id" value="">

                <div id="variants-container">
                    @forelse($product->variants as $index => $variant)
                    <div class="variant-item">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="variants[{{ $index }}][size]" class="form-control" placeholder="Размер (S, M, L...)" required value="{{ $variant->size }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="variants[{{ $index }}][color]" class="form-control" placeholder="Цвет" value="{{ $variant->color }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="variants[{{ $index }}][quantity]" class="form-control" placeholder="Кол-во" required value="{{ $variant->stock->sum('quantity') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="variants[{{ $index }}][price]" class="form-control" placeholder="Цена" step="0.01" value="{{ $variant->product->retail_price }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100" onclick="removeVariant(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="variant-item">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="variants[0][size]" class="form-control" placeholder="Размер (S, M, L...)" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="variants[0][color]" class="form-control" placeholder="Цвет">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="variants[0][quantity]" class="form-control" placeholder="Кол-во" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="variants[0][price]" class="form-control" placeholder="Цена" step="0.01">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100" onclick="removeVariant(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                <button type="button" class="btn btn-outline-success" onclick="addVariant()">
                    <i class="bi bi-plus-lg"></i> Добавить вариант
                </button>
            </div>
        </div>
        
        {{-- Привязка к складам и магазинам --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <span>Магазины и склады</span>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="shop_id" class="form-label">Магазин</label>
                        <select name="shop_id" id="shop_id" class="form-select @error('shop_id') is-invalid @enderror">
                            <option value="">Выберите магазин</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('shop_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="warehouse_id" class="form-label">Склад</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                            <option value="">Выберите склад</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Кнопки действий --}}
        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Создать товар
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg"></i> Отмена
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let variantCounter = 1;

// Синхронизация shop_id из селекта в скрытое поле
document.addEventListener('DOMContentLoaded', function() {
    const shopSelect = document.getElementById('shop_id');
    const mainShopInput = document.getElementById('main_shop_id');
    
    if (shopSelect && mainShopInput) {
        shopSelect.addEventListener('change', function() {
            mainShopInput.value = this.value;
        });
    }
});

function addVariant() {
    const container = document.getElementById('variants-container');
    const newVariant = document.createElement('div');
    newVariant.className = 'variant-item';
    newVariant.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="variants[${variantCounter}][size]" class="form-control" placeholder="Размер (S, M, L...)" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="variants[${variantCounter}][color]" class="form-control" placeholder="Цвет">
            </div>
            <div class="col-md-2">
                <input type="number" name="variants[${variantCounter}][quantity]" class="form-control" placeholder="Кол-во" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="variants[${variantCounter}][price]" class="form-control" placeholder="Цена" step="0.01">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100" onclick="removeVariant(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newVariant);
    variantCounter++;
}

// Генерация артикула
function generateArticle() {
    const randomDigits = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
    const article = 'ART' + randomDigits;
    document.getElementById('article').value = article;
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
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Работа с камерой
let cameraStream = null;

function openCameraModal() {
    const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
    modal.show();
    startCamera();
}

async function startCamera() {
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        const video = document.getElementById('cameraVideo');
        video.srcObject = cameraStream;
    } catch (err) {
        alert('Ошибка доступа к камере: ' + err.message);
        console.error('Camera error:', err);
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
@endpush
@endsection
