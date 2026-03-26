@extends('layouts.admin')
@section('page-title', 'Добавить товар')
@section('content')

<div class="flex items-center gap-3 mb-6">
    <div class="w-12 h-12 rounded-xl bg-success/15 flex items-center justify-center text-success text-2xl">
        <i class="bi bi-box-seam"></i>
    </div>
    <div>
        <h2 class="text-2xl md:text-3xl font-bold m-0">Добавить товар</h2>
        <p class="text-sm text-muted m-0">Создание нового товара в системе</p>
    </div>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    
    {{-- Основная информация --}}
    <div class="card !p-0 overflow-hidden border-white/5 shadow-xl">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5">
            <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                <i class="bi bi-info-circle text-accent"></i> Основная информация
            </h3>
        </div>
        <div class="p-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Артикул</label>
                    <div class="flex gap-2">
                        <input type="text" name="article" class="input" value="{{ old('article') }}" placeholder="Автогенерация если пусто">
                        <button type="button" class="w-11 h-11 flex-shrink-0 flex items-center justify-center rounded-xl bg-accent/10 text-accent border border-accent/20 hover:bg-accent/20 transition-colors" onclick="generateArticle()" title="Сгенерировать">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Название <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="input" value="{{ old('name') }}" required placeholder="Например: Футболка базовая">
                </div>
            </div>
            
            <div>
                <label class="block font-semibold mb-2 text-text text-sm">Описание</label>
                <textarea name="description" class="input min-h-[100px]" rows="3" placeholder="Краткое описание товара...">{{ old('description') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Категория</label>
                    <select name="category_id" class="input">
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Бренд</label>
                    <input type="text" name="brand" class="input" value="{{ old('brand') }}" placeholder="Например: Zara">
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Цена продажи (сом) <span class="text-danger">*</span></label>
                    <input type="number" name="retail_price" class="input" value="{{ old('retail_price') }}" required min="0" step="0.01" placeholder="0.00">
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Цена закупки</label>
                    <input type="number" name="supplier_price" class="input" value="{{ old('supplier_price') }}" min="0" step="0.01" placeholder="0.00">
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Оптовая цена</label>
                    <input type="number" name="wholesale_price" class="input" value="{{ old('wholesale_price') }}" min="0" step="0.01" placeholder="0.00">
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Материал</label>
                    <input type="text" name="material" class="input" value="{{ old('material') }}" placeholder="Напр: Хлопок">
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Страна</label>
                    <input type="text" name="country" class="input" value="{{ old('country') }}" placeholder="Напр: Кыргызстан">
                </div>
            </div>
            
            <div class="pt-2">
                <label class="inline-flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-white/10 bg-sidebar text-accent focus:ring-accent/30 focus:ring-offset-0">
                    </div>
                    <span class="text-text font-medium group-hover:text-accent transition-colors">Активный товар для продажи</span>
                </label>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Фото товара --}}
        <div class="card !p-0 overflow-hidden border-white/5 flex flex-col">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-image text-accent"></i> Фото товара
                </h3>
            </div>
            <div class="p-5 flex-grow flex flex-col justify-center">
                <div class="relative group">
                    <div class="border-2 border-dashed border-white/10 rounded-2xl bg-white/5 p-8 text-center hover:border-accent/50 hover:bg-accent/5 transition-all cursor-pointer overflow-hidden" onclick="document.getElementById('photo').click()">
                        <div id="upload-placeholder" class="space-y-3">
                            <i class="bi bi-cloud-arrow-up text-4xl text-accent opacity-50 group-hover:opacity-100 transition-opacity"></i>
                            <div class="text-text font-semibold">Выберите файл или перетащите</div>
                            <div class="text-muted text-xs">PNG, JPG до 2MB</div>
                        </div>
                        <img id="preview" src="#" alt="Предпросмотр" class="hidden max-w-full max-h-[300px] rounded-xl mx-auto object-contain shadow-2xl">
                        <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </div>
                    
                    <div class="mt-4 flex flex-wrap gap-3 justify-center">
                        <button type="button" class="btn btn-outline py-2 px-4 text-xs" onclick="openCameraModal()">
                            <i class="bi bi-camera"></i> Сделать фото
                        </button>
                        <button type="button" id="remove-photo" class="hidden btn btn-danger py-2 px-4 text-xs" onclick="clearPhoto()">
                            <i class="bi bi-x-circle"></i> Удалить
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Магазины и склады --}}
        <div class="card !p-0 overflow-hidden border-white/5 flex flex-col">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-geo-alt text-accent"></i> Место размещения
                </h3>
            </div>
            <div class="p-5 space-y-6">
                <div>
                    <label class="block font-semibold mb-3 text-text text-sm">Куда поместить новый товар?</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all border-white/5 bg-white/5 hover:bg-white/10 has-[:checked]:border-accent/50 has-[:checked]:bg-accent/5">
                            <input type="radio" name="location_type" value="warehouse" checked onchange="toggleLocation()" class="absolute opacity-0">
                            <i class="bi bi-box-seam text-2xl text-accent mb-2"></i>
                            <span class="font-bold text-sm">Склад</span>
                            <span class="text-[10px] text-muted">Хранение и логистика</span>
                        </label>
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all border-white/5 bg-white/5 hover:bg-white/10 has-[:checked]:border-accent/50 has-[:checked]:bg-accent/5">
                            <input type="radio" name="location_type" value="shop" onchange="toggleLocation()" class="absolute opacity-0">
                            <i class="bi bi-shop text-2xl text-success mb-2"></i>
                            <span class="font-bold text-sm">Витрина</span>
                            <span class="text-[10px] text-muted">Сразу в продажу</span>
                        </label>
                    </div>
                </div>

                <div id="warehouse-section" class="space-y-2">
                    <label class="block font-semibold text-text text-sm">Выберите склад</label>
                    <select name="warehouse_id" id="warehouse_id" class="input">
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="shop-section" class="hidden space-y-2">
                    <label class="block font-semibold text-text text-sm">Выберите магазин</label>
                    <select name="shop_id" id="shop_id" class="input" disabled>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ old('shop_id', $firstShopId ?? '') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Варианты товара --}}
    <div class="card !p-0 overflow-hidden border-white/5 shadow-xl">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
            <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                <i class="bi bi-layers text-accent"></i> Варианты товара
            </h3>
            <button type="button" class="btn btn-outline py-2 px-3 text-xs" onclick="addVariant()">
                <i class="bi bi-plus-lg"></i> Добавить
            </button>
        </div>
        <div class="p-5">
            <div id="variants-container" class="space-y-4">
                <div class="variant-item bg-white/5 p-4 rounded-xl border border-white/5 relative">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Размер</label>
                            <select name="variants[0][size]" class="input" required>
                                <option value="">Выберите размер</option>
                                <option value="XS">XS (Extra Small)</option>
                                <option value="S">S (Small)</option>
                                <option value="M">M (Medium)</option>
                                <option value="L">L (Large)</option>
                                <option value="XL">XL (Extra Large)</option>
                                <option value="2XL">2XL (XXL)</option>
                                <option value="3XL">3XL (XXXL)</option>
                                <option value="4XL">4XL (XXXXL)</option>
                                <option value="5XL">5XL (XXXXXL)</option>
                                <option value="42">42 (EU)</option>
                                <option value="44">44 (EU)</option>
                                <option value="46">46 (EU)</option>
                                <option value="48">48 (EU)</option>
                                <option value="50">50 (EU)</option>
                                <option value="52">52 (EU)</option>
                                <option value="54">54 (EU)</option>
                                <option value="56">56 (EU)</option>
                                <option value="58">58 (EU)</option>
                                <option value="60">60 (EU)</option>
                                <option value="25">25 (W)</option>
                                <option value="26">26 (W)</option>
                                <option value="27">27 (W)</option>
                                <option value="28">28 (W)</option>
                                <option value="29">29 (W)</option>
                                <option value="30">30 (W)</option>
                                <option value="31">31 (W)</option>
                                <option value="32">32 (W)</option>
                                <option value="33">33 (W)</option>
                                <option value="34">34 (W)</option>
                                <option value="35">35 (W)</option>
                                <option value="36">36 (W)</option>
                                <option value="37">37 (W)</option>
                                <option value="38">38 (W)</option>
                                <option value="39">39 (W)</option>
                                <option value="40">40 (W)</option>
                                <option value="41">41 (W)</option>
                                <option value="42">42 (W)</option>
                                <option value="43">43 (W)</option>
                                <option value="44">44 (W)</option>
                                <option value="45">45 (W)</option>
                                <option value="46">46 (W)</option>
                                <option value="47">47 (W)</option>
                                <option value="48">48 (W)</option>
                                <option value="49">49 (W)</option>
                                <option value="50">50 (W)</option>
                                <option value="28/32">28/32 (W/L)</option>
                                <option value="29/32">29/32 (W/L)</option>
                                <option value="30/32">30/32 (W/L)</option>
                                <option value="31/32">31/32 (W/L)</option>
                                <option value="32/32">32/32 (W/L)</option>
                                <option value="33/32">33/32 (W/L)</option>
                                <option value="34/32">34/32 (W/L)</option>
                                <option value="36/32">36/32 (W/L)</option>
                                <option value="38/32">38/32 (W/L)</option>
                                <option value="29/30">29/30 (W/L)</option>
                                <option value="30/30">30/30 (W/L)</option>
                                <option value="31/30">31/30 (W/L)</option>
                                <option value="32/30">32/30 (W/L)</option>
                                <option value="33/30">33/30 (W/L)</option>
                                <option value="34/30">34/30 (W/L)</option>
                                <option value="36/30">36/30 (W/L)</option>
                                <option value="38/30">38/30 (W/L)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Цвет</label>
                            <input type="text" name="variants[0][color]" class="input" placeholder="Цвет">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Начальное кол-во</label>
                            <input type="number" name="variants[0][quantity]" class="input" placeholder="0" required min="0">
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="btn btn-outline w-full py-2" onclick="addVariantField(this)">
                                <i class="bi bi-pencil"></i> Свой размер
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Кнопки --}}
    <div class="flex flex-col sm:flex-row gap-3 pt-4">
        <button type="submit" class="btn btn-primary px-8 py-3 justify-center shadow-lg shadow-accent/20">
            <i class="bi bi-check2-circle text-lg"></i> <span>Создать товар</span>
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline px-8 py-3 justify-center">
            <i class="bi bi-arrow-left"></i> Отмена
        </a>
    </div>
</form>

{{-- Модальное окно камеры --}}
<div class="modal fade" id="cameraModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-b border-white/5">
                <h5 class="modal-title"><i class="bi bi-camera text-accent"></i> Сделать фото</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-black">
                <div class="relative aspect-video flex items-center justify-center">
                    <video id="cameraVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                    <canvas id="cameraCanvas" class="hidden"></canvas>
                </div>
            </div>
            <div class="modal-footer bg-sidebar border-t border-white/5">
                <div class="flex gap-3 w-full justify-center">
                    <button type="button" class="btn btn-primary py-3 px-6 rounded-full" onclick="takePhoto()">
                        <i class="bi bi-camera-fill text-xl"></i>
                    </button>
                    <button type="button" class="btn btn-outline py-3 px-6 rounded-full" onclick="stopCamera()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let variantCounter = 1;

const sizeOptionsHtml = `
<option value="">Выберите размер</option>
<option value="XS">XS (Extra Small)</option>
<option value="S">S (Small)</option>
<option value="M">M (Medium)</option>
<option value="L">L (Large)</option>
<option value="XL">XL (Extra Large)</option>
<option value="2XL">2XL (XXL)</option>
<option value="3XL">3XL (XXXL)</option>
<option value="4XL">4XL (XXXXL)</option>
<option value="5XL">5XL (XXXXXL)</option>
<option value="42">42 (EU)</option>
<option value="44">44 (EU)</option>
<option value="46">46 (EU)</option>
<option value="48">48 (EU)</option>
<option value="50">50 (EU)</option>
<option value="52">52 (EU)</option>
<option value="54">54 (EU)</option>
<option value="56">56 (EU)</option>
<option value="58">58 (EU)</option>
<option value="60">60 (EU)</option>
<option value="25">25 (W)</option>
<option value="26">26 (W)</option>
<option value="27">27 (W)</option>
<option value="28">28 (W)</option>
<option value="29">29 (W)</option>
<option value="30">30 (W)</option>
<option value="31">31 (W)</option>
<option value="32">32 (W)</option>
<option value="33">33 (W)</option>
<option value="34">34 (W)</option>
<option value="35">35 (W)</option>
<option value="36">36 (W)</option>
<option value="37">37 (W)</option>
<option value="38">38 (W)</option>
<option value="39">39 (W)</option>
<option value="40">40 (W)</option>
<option value="41">41 (W)</option>
<option value="42">42 (W)</option>
<option value="43">43 (W)</option>
<option value="44">44 (W)</option>
<option value="45">45 (W)</option>
<option value="46">46 (W)</option>
<option value="47">47 (W)</option>
<option value="48">48 (W)</option>
<option value="49">49 (W)</option>
<option value="50">50 (W)</option>
<option value="28/32">28/32 (W/L)</option>
<option value="29/32">29/32 (W/L)</option>
<option value="30/32">30/32 (W/L)</option>
<option value="31/32">31/32 (W/L)</option>
<option value="32/32">32/32 (W/L)</option>
<option value="33/32">33/32 (W/L)</option>
<option value="34/32">34/32 (W/L)</option>
<option value="36/32">36/32 (W/L)</option>
<option value="38/32">38/32 (W/L)</option>
<option value="29/30">29/30 (W/L)</option>
<option value="30/30">30/30 (W/L)</option>
<option value="31/30">31/30 (W/L)</option>
<option value="32/30">32/30 (W/L)</option>
<option value="33/30">33/30 (W/L)</option>
<option value="34/30">34/30 (W/L)</option>
<option value="36/30">36/30 (W/L)</option>
<option value="38/30">38/30 (W/L)</option>
`;

function toggleLocation() {
    const locationType = document.querySelector('input[name="location_type"]:checked').value;
    const warehouseSection = document.getElementById('warehouse-section');
    const shopSection = document.getElementById('shop-section');
    const warehouseSelect = document.getElementById('warehouse_id');
    const shopSelect = document.getElementById('shop_id');

    if (locationType === 'warehouse') {
        warehouseSection.classList.remove('hidden');
        shopSection.classList.add('hidden');
        warehouseSelect.disabled = false;
        shopSelect.disabled = true;
    } else {
        warehouseSection.classList.add('hidden');
        shopSection.classList.remove('hidden');
        warehouseSelect.disabled = true;
        shopSelect.disabled = false;
    }
}

function addVariant() {
    const container = document.getElementById('variants-container');
    const newVariant = document.createElement('div');
    newVariant.className = 'variant-item bg-white/5 p-4 rounded-xl border border-white/5 relative group animate-in fade-in slide-in-from-top-2 duration-300';
    newVariant.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Размер</label>
                <select name="variants[${variantCounter}][size]" class="input" required>
                    ${sizeOptionsHtml}
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Цвет</label>
                <input type="text" name="variants[${variantCounter}][color]" class="input" placeholder="Цвет">
            </div>
            <div>
                <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Начальное кол-во</label>
                <input type="number" name="variants[${variantCounter}][quantity]" class="input" placeholder="0" required min="0">
            </div>
            <div class="flex items-end">
                <button type="button" class="btn btn-outline w-full py-2" onclick="addVariantField(this)">
                    <i class="bi bi-pencil"></i> Свой размер
                </button>
            </div>
        </div>
        <button type="button" class="absolute -top-2 -right-2 w-7 h-7 bg-danger text-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity" onclick="removeVariant(this)">
            <i class="bi bi-x"></i>
        </button>
    `;
    container.appendChild(newVariant);
    variantCounter++;
}

function addVariantField(button) {
    const sizeSelect = button.closest('.grid').querySelector('select[name^="variants"][name$="[size]"]');
    sizeSelect.outerHTML = `<input type="text" name="${sizeSelect.name}" class="input" placeholder="Введите размер" required>`;
    button.parentElement.outerHTML = `<div class="flex items-end"><button type="button" class="btn btn-outline w-full py-2" disabled><i class="bi bi-check"></i> Текстовое поле</button></div>`;
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
            const placeholder = document.getElementById('upload-placeholder');
            const removeBtn = document.getElementById('remove-photo');

            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            removeBtn.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearPhoto() {
    const input = document.getElementById('photo');
    const preview = document.getElementById('preview');
    const placeholder = document.getElementById('upload-placeholder');
    const removeBtn = document.getElementById('remove-photo');

    input.value = '';
    preview.src = '#';
    preview.classList.add('hidden');
    placeholder.classList.remove('hidden');
    removeBtn.classList.add('hidden');
}

let cameraStream = null;

function openCameraModal() {
    const modalElement = document.getElementById('cameraModal');
    const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modal.show();
    startCamera();
}

async function startCamera() {
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
        });
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
    const modalElement = document.getElementById('cameraModal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) modal.hide();
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
    }, 'image/jpeg', 0.9);
}

document.addEventListener('DOMContentLoaded', toggleLocation);
</script>
@endsection
