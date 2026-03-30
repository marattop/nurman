<?php $__env->startSection('page-title', 'Редактировать товар'); ?>
<?php $__env->startSection('content'); ?>

<div class="flex items-center gap-3 mb-6">
    <div class="w-12 h-12 rounded-xl bg-accent/15 flex items-center justify-center text-accent text-2xl">
        <i class="bi bi-pencil"></i>
    </div>
    <div>
        <h2 class="text-2xl md:text-3xl font-bold m-0">Редактировать товар</h2>
        <p class="text-sm text-muted m-0">Артикул: <span class="text-text font-mono"><?php echo e($product->article); ?></span></p>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="bg-success/15 border border-success/30 text-success rounded-xl p-4 mb-6 flex items-center gap-3 animate-in fade-in duration-500">
        <i class="bi bi-check-circle-fill text-xl"></i>
        <span class="font-medium"><?php echo e(session('success')); ?></span>
    </div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="bg-danger/15 border border-danger/30 text-danger rounded-xl p-4 mb-6">
        <div class="flex items-center gap-3 mb-2">
            <i class="bi bi-exclamation-triangle-fill text-xl"></i>
            <span class="font-bold">Ошибка при сохранении</span>
        </div>
        <ul class="list-disc list-inside text-sm opacity-90 space-y-1 ml-2">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?php echo e(route('admin.products.update', $product)); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?>

    
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
                        <input type="text" name="article" class="input" value="<?php echo e(old('article', $product->article)); ?>" placeholder="Автогенерация если пусто">
                        <button type="button" class="w-11 h-11 flex-shrink-0 flex items-center justify-center rounded-xl bg-accent/10 text-accent border border-accent/20 hover:bg-accent/20 transition-colors" onclick="generateArticle()" title="Сгенерировать">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Название <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="input" value="<?php echo e(old('name', $product->name)); ?>" required placeholder="Например: Футболка базовая">
                </div>
            </div>

            <div>
                <label class="block font-semibold mb-2 text-text text-sm">Описание</label>
                <textarea name="description" class="input min-h-[100px]" rows="3" placeholder="Описание товара..."><?php echo e(old('description', $product->description)); ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Категория</label>
                    <select name="category_id" class="input">
                        <option value="">Выберите категорию</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $product->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Бренд</label>
                    <input type="text" name="brand" class="input" value="<?php echo e(old('brand', $product->brand)); ?>" placeholder="Например: Zara">
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Цена продажи (сом) <span class="text-danger">*</span></label>
                    <input type="number" name="retail_price" class="input" value="<?php echo e(old('retail_price', $product->retail_price ?? 0)); ?>" required min="0" step="0.01" placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Цена закупки</label>
                    <input type="number" name="supplier_price" class="input" value="<?php echo e(old('supplier_price', $product->variants->first()->supplier_price ?? 0)); ?>" min="0" step="0.01" placeholder="0.00">
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Материал</label>
                    <input type="text" name="material" class="input" value="<?php echo e(old('material', $product->material)); ?>" placeholder="Напр: Хлопок">
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-text text-sm">Страна</label>
                    <input type="text" name="country" class="input" value="<?php echo e(old('country', $product->country)); ?>" placeholder="Напр: Кыргызстан">
                </div>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $product->is_active) ? 'checked' : ''); ?> class="w-5 h-5 rounded border-white/10 bg-sidebar text-accent focus:ring-accent/30 focus:ring-offset-0">
                    </div>
                    <span class="text-text font-medium group-hover:text-accent transition-colors">Активный товар</span>
                </label>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="card !p-0 overflow-hidden border-white/5 flex flex-col">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-image text-accent"></i> Фото товара
                </h3>
            </div>
            <div class="p-5 flex-grow flex flex-col justify-center">
                <div class="relative group">
                    <div class="border-2 border-dashed border-white/10 rounded-2xl bg-white/5 p-6 text-center hover:border-accent/50 hover:bg-accent/5 transition-all cursor-pointer overflow-hidden" onclick="document.getElementById('photo').click()">
                        <div id="upload-placeholder" class="<?php echo e($product->photo ? 'hidden' : ''); ?> space-y-3 py-4">
                            <i class="bi bi-cloud-arrow-up text-4xl text-accent opacity-50 group-hover:opacity-100 transition-opacity"></i>
                            <div class="text-text font-semibold text-sm">Загрузить новое фото</div>
                            <div class="text-muted text-[10px]">PNG, JPG до 2MB</div>
                        </div>
                        
                        <img id="preview" src="<?php echo e($product->photo ? asset('storage/' . $product->photo) : '#'); ?>" alt="Предпросмотр" class="<?php echo e($product->photo ? '' : 'hidden'); ?> max-w-full max-h-[300px] rounded-xl mx-auto object-contain shadow-2xl border border-white/10">
                        <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </div>
                    
                    <div class="mt-4 flex flex-wrap gap-3 justify-center">
                        <button type="button" class="btn btn-outline py-2 px-4 text-xs" onclick="openCameraModal()">
                            <i class="bi bi-camera"></i> Камера
                        </button>
                        <button type="button" id="remove-photo" class="<?php echo e($product->photo ? '' : 'hidden'); ?> btn btn-danger py-2 px-4 text-xs" onclick="clearPhoto()">
                            <i class="bi bi-x-circle"></i> Сбросить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card !p-0 overflow-hidden border-white/5 flex flex-col">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-geo-alt text-accent"></i> Место размещения
                </h3>
            </div>
            <div class="p-5 space-y-6">
                <div>
                    <label class="block font-semibold mb-3 text-text text-sm">Где находится товар?</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all border-white/5 bg-white/5 hover:bg-white/10 has-[:checked]:border-accent/50 has-[:checked]:bg-accent/5">
                            <input type="radio" name="location_type" value="warehouse" onchange="toggleLocation()" class="absolute opacity-0" <?php echo e(!$shopId ? 'checked' : ''); ?>>
                            <i class="bi bi-box-seam text-2xl text-accent mb-2"></i>
                            <span class="font-bold text-sm">Склад</span>
                        </label>
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all border-white/5 bg-white/5 hover:bg-white/10 has-[:checked]:border-accent/50 has-[:checked]:bg-accent/5">
                            <input type="radio" name="location_type" value="shop" onchange="toggleLocation()" class="absolute opacity-0" <?php echo e($shopId ? 'checked' : ''); ?>>
                            <i class="bi bi-shop text-2xl text-success mb-2"></i>
                            <span class="font-bold text-sm">Витрина</span>
                        </label>
                    </div>
                </div>

                <div id="warehouse-section" class="<?php echo e($shopId ? 'hidden' : ''); ?> space-y-2">
                    <label class="block font-semibold text-text text-sm">Выберите склад</label>
                    <select name="warehouse_id" id="warehouse_id" class="input" <?php echo e($shopId ? 'disabled' : ''); ?>>
                        <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($warehouse->id); ?>" <?php echo e(old('warehouse_id', $warehouseId ?? '') == $warehouse->id ? 'selected' : ''); ?>><?php echo e($warehouse->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div id="shop-section" class="<?php echo e(!$shopId ? 'hidden' : ''); ?> space-y-2">
                    <label class="block font-semibold text-text text-sm">Выберите магазин</label>
                    <select name="shop_id" id="shop_id" class="input" <?php echo e(!$shopId ? 'disabled' : ''); ?>>
                        <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($shop->id); ?>" <?php echo e(old('shop_id', $shopId ?? '') == $shop->id ? 'selected' : ''); ?>><?php echo e($shop->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card !p-0 overflow-hidden border-white/5 shadow-xl">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
            <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                <i class="bi bi-layers text-accent"></i> Варианты и остатки
            </h3>
            <button type="button" class="btn btn-outline py-2 px-3 text-xs" onclick="addVariant()">
                <i class="bi bi-plus-lg"></i> Добавить
            </button>
        </div>
        <div class="p-5">
            <div id="variants-container" class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="variant-item bg-white/5 p-4 rounded-xl border border-white/5 relative group">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Размер</label>
                            <select name="variants[<?php echo e($index); ?>][size]" class="input" required>
                                <option value="">Выберите размер</option>
                                <option value="XS" <?php echo e($variant->size == 'XS' ? 'selected' : ''); ?>>XS (Extra Small)</option>
                                <option value="S" <?php echo e($variant->size == 'S' ? 'selected' : ''); ?>>S (Small)</option>
                                <option value="M" <?php echo e($variant->size == 'M' ? 'selected' : ''); ?>>M (Medium)</option>
                                <option value="L" <?php echo e($variant->size == 'L' ? 'selected' : ''); ?>>L (Large)</option>
                                <option value="XL" <?php echo e($variant->size == 'XL' ? 'selected' : ''); ?>>XL (Extra Large)</option>
                                <option value="2XL" <?php echo e($variant->size == '2XL' ? 'selected' : ''); ?>>2XL (XXL)</option>
                                <option value="3XL" <?php echo e($variant->size == '3XL' ? 'selected' : ''); ?>>3XL (XXXL)</option>
                                <option value="4XL" <?php echo e($variant->size == '4XL' ? 'selected' : ''); ?>>4XL (XXXXL)</option>
                                <option value="5XL" <?php echo e($variant->size == '5XL' ? 'selected' : ''); ?>>5XL (XXXXXL)</option>
                                <option value="42" <?php echo e($variant->size == '42' ? 'selected' : ''); ?>>42 (EU)</option>
                                <option value="44" <?php echo e($variant->size == '44' ? 'selected' : ''); ?>>44 (EU)</option>
                                <option value="46" <?php echo e($variant->size == '46' ? 'selected' : ''); ?>>46 (EU)</option>
                                <option value="48" <?php echo e($variant->size == '48' ? 'selected' : ''); ?>>48 (EU)</option>
                                <option value="50" <?php echo e($variant->size == '50' ? 'selected' : ''); ?>>50 (EU)</option>
                                <option value="52" <?php echo e($variant->size == '52' ? 'selected' : ''); ?>>52 (EU)</option>
                                <option value="54" <?php echo e($variant->size == '54' ? 'selected' : ''); ?>>54 (EU)</option>
                                <option value="56" <?php echo e($variant->size == '56' ? 'selected' : ''); ?>>56 (EU)</option>
                                <option value="58" <?php echo e($variant->size == '58' ? 'selected' : ''); ?>>58 (EU)</option>
                                <option value="60" <?php echo e($variant->size == '60' ? 'selected' : ''); ?>>60 (EU)</option>
                                <option value="25" <?php echo e($variant->size == '25' ? 'selected' : ''); ?>>25 (W)</option>
                                <option value="26" <?php echo e($variant->size == '26' ? 'selected' : ''); ?>>26 (W)</option>
                                <option value="27" <?php echo e($variant->size == '27' ? 'selected' : ''); ?>>27 (W)</option>
                                <option value="28" <?php echo e($variant->size == '28' ? 'selected' : ''); ?>>28 (W)</option>
                                <option value="29" <?php echo e($variant->size == '29' ? 'selected' : ''); ?>>29 (W)</option>
                                <option value="30" <?php echo e($variant->size == '30' ? 'selected' : ''); ?>>30 (W)</option>
                                <option value="31" <?php echo e($variant->size == '31' ? 'selected' : ''); ?>>31 (W)</option>
                                <option value="32" <?php echo e($variant->size == '32' ? 'selected' : ''); ?>>32 (W)</option>
                                <option value="33" <?php echo e($variant->size == '33' ? 'selected' : ''); ?>>33 (W)</option>
                                <option value="34" <?php echo e($variant->size == '34' ? 'selected' : ''); ?>>34 (W)</option>
                                <option value="35" <?php echo e($variant->size == '35' ? 'selected' : ''); ?>>35 (W)</option>
                                <option value="36" <?php echo e($variant->size == '36' ? 'selected' : ''); ?>>36 (W)</option>
                                <option value="37" <?php echo e($variant->size == '37' ? 'selected' : ''); ?>>37 (W)</option>
                                <option value="38" <?php echo e($variant->size == '38' ? 'selected' : ''); ?>>38 (W)</option>
                                <option value="39" <?php echo e($variant->size == '39' ? 'selected' : ''); ?>>39 (W)</option>
                                <option value="40" <?php echo e($variant->size == '40' ? 'selected' : ''); ?>>40 (W)</option>
                                <option value="41" <?php echo e($variant->size == '41' ? 'selected' : ''); ?>>41 (W)</option>
                                <option value="42" <?php echo e($variant->size == '42' ? 'selected' : ''); ?>>42 (W)</option>
                                <option value="43" <?php echo e($variant->size == '43' ? 'selected' : ''); ?>>43 (W)</option>
                                <option value="44" <?php echo e($variant->size == '44' ? 'selected' : ''); ?>>44 (W)</option>
                                <option value="45" <?php echo e($variant->size == '45' ? 'selected' : ''); ?>>45 (W)</option>
                                <option value="46" <?php echo e($variant->size == '46' ? 'selected' : ''); ?>>46 (W)</option>
                                <option value="47" <?php echo e($variant->size == '47' ? 'selected' : ''); ?>>47 (W)</option>
                                <option value="48" <?php echo e($variant->size == '48' ? 'selected' : ''); ?>>48 (W)</option>
                                <option value="49" <?php echo e($variant->size == '49' ? 'selected' : ''); ?>>49 (W)</option>
                                <option value="50" <?php echo e($variant->size == '50' ? 'selected' : ''); ?>>50 (W)</option>
                                <option value="28/32" <?php echo e($variant->size == '28/32' ? 'selected' : ''); ?>>28/32 (W/L)</option>
                                <option value="29/32" <?php echo e($variant->size == '29/32' ? 'selected' : ''); ?>>29/32 (W/L)</option>
                                <option value="30/32" <?php echo e($variant->size == '30/32' ? 'selected' : ''); ?>>30/32 (W/L)</option>
                                <option value="31/32" <?php echo e($variant->size == '31/32' ? 'selected' : ''); ?>>31/32 (W/L)</option>
                                <option value="32/32" <?php echo e($variant->size == '32/32' ? 'selected' : ''); ?>>32/32 (W/L)</option>
                                <option value="33/32" <?php echo e($variant->size == '33/32' ? 'selected' : ''); ?>>33/32 (W/L)</option>
                                <option value="34/32" <?php echo e($variant->size == '34/32' ? 'selected' : ''); ?>>34/32 (W/L)</option>
                                <option value="36/32" <?php echo e($variant->size == '36/32' ? 'selected' : ''); ?>>36/32 (W/L)</option>
                                <option value="38/32" <?php echo e($variant->size == '38/32' ? 'selected' : ''); ?>>38/32 (W/L)</option>
                                <option value="29/30" <?php echo e($variant->size == '29/30' ? 'selected' : ''); ?>>29/30 (W/L)</option>
                                <option value="30/30" <?php echo e($variant->size == '30/30' ? 'selected' : ''); ?>>30/30 (W/L)</option>
                                <option value="31/30" <?php echo e($variant->size == '31/30' ? 'selected' : ''); ?>>31/30 (W/L)</option>
                                <option value="32/30" <?php echo e($variant->size == '32/30' ? 'selected' : ''); ?>>32/30 (W/L)</option>
                                <option value="33/30" <?php echo e($variant->size == '33/30' ? 'selected' : ''); ?>>33/30 (W/L)</option>
                                <option value="34/30" <?php echo e($variant->size == '34/30' ? 'selected' : ''); ?>>34/30 (W/L)</option>
                                <option value="36/30" <?php echo e($variant->size == '36/30' ? 'selected' : ''); ?>>36/30 (W/L)</option>
                                <option value="38/30" <?php echo e($variant->size == '38/30' ? 'selected' : ''); ?>>38/30 (W/L)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Цвет</label>
                            <input type="text" name="variants[<?php echo e($index); ?>][color]" class="input" placeholder="Цвет" value="<?php echo e($variant->color); ?>">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Кол-во</label>
                            <input type="number" name="variants[<?php echo e($index); ?>][quantity]" class="input" placeholder="0" required value="<?php echo e($variant->stock->sum('quantity')); ?>">
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="btn btn-outline w-full py-2" onclick="convertToInput(this)">
                                <i class="bi bi-pencil"></i> Свой размер
                            </button>
                        </div>
                    </div>
                    <button type="button" class="absolute -top-2 -right-2 w-7 h-7 bg-danger text-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity" onclick="removeVariant(this)">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="variant-item bg-white/5 p-4 rounded-xl border border-white/5 relative group">
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
                            <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Кол-во</label>
                            <input type="number" name="variants[0][quantity]" class="input" placeholder="0" required>
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="btn btn-outline w-full py-2" onclick="convertToInput(this)">
                                <i class="bi bi-pencil"></i> Свой размер
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="flex flex-col sm:flex-row gap-3 pt-4">
        <button type="submit" class="btn btn-primary px-8 py-3 justify-center shadow-lg shadow-accent/20">
            <i class="bi bi-check2-circle text-lg"></i> <span>Сохранить изменения</span>
        </button>
        <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline px-8 py-3 justify-center">
            <i class="bi bi-arrow-left"></i> Отмена
        </a>
    </div>
</form>


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
let variantCounter = <?php echo e($product->variants->count() > 0 ? $product->variants->count() : 1); ?>;

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
                <input type="text" name="variants[${variantCounter}][color]" class="input" placeholder="Цвет">
            </div>
            <div>
                <label class="block font-semibold mb-1 text-muted text-[11px] uppercase">Кол-во</label>
                <input type="number" name="variants[${variantCounter}][quantity]" class="input" placeholder="0" required min="0">
            </div>
            <div class="flex items-end">
                <button type="button" class="btn btn-outline w-full py-2" onclick="convertToInput(this)">
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

function convertToInput(button) {
    const sizeSelect = button.closest('.grid').querySelector('select[name^="variants"][name$="[size]"]');
    sizeSelect.outerHTML = `<input type="text" name="${sizeSelect.name}" class="input" placeholder="Введите размер" required value="${sizeSelect.value}">`;
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
            if(placeholder) placeholder.classList.add('hidden');
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
    if(placeholder) placeholder.classList.remove('hidden');
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/products/edit.blade.php ENDPATH**/ ?>