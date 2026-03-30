<?php $__env->startSection('page-title', 'Магазины'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-icon"><i class="bi bi-shop"></i></div>
    <div>
        <h1 class="page-title">Магазины</h1>
        <p class="page-subtitle">Управление магазинами</p>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="card" style="padding: 16px; border: 1px solid rgba(16,185,129,0.25); margin-bottom: 16px;">
        <div style="font-weight:700; color:#10b981; margin-bottom: 6px;">
            <i class="bi bi-check-circle"></i> Успешно
        </div>
        <div style="color:#f1f5f9;"><?php echo e(session('success')); ?></div>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="card" style="padding: 16px; border: 1px solid rgba(239,68,68,0.25); margin-bottom: 16px;">
        <div style="font-weight:700; color:#ef4444; margin-bottom: 6px;">
            <i class="bi bi-x-circle"></i> Ошибка
        </div>
        <div style="color:#f1f5f9;"><?php echo e(session('error')); ?></div>
    </div>
<?php endif; ?>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список магазинов</h3>
        <button type="button" class="btn btn-primary" onclick="showAddShopModal()">
            <i class="bi bi-plus-lg"></i> Добавить
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Название</th>
                    <th>Адрес</th>
                    <th>Телефон</th>
                    <th>Продавцы</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isMain = isset($mainShop) && $mainShop && (int)$mainShop->id === (int)$shop->id;
                    ?>
                <tr>
                    <td>
                        <div style="font-weight: 500;"><?php echo e($shop->name); ?></div>
                        <?php if($isMain): ?>
                            <div style="margin-top:4px;">
                                <span class="badge badge-blue">Основной</span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td style="color: #94a3b8;"><?php echo e($shop->address ?? '—'); ?></td>
                    <td style="color: #94a3b8;"><?php echo e($shop->phone ?? '—'); ?></td>
                    <td>
                        <span class="badge badge-blue"><?php echo e($shop->sellers_count ?? 0); ?> чел.</span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-outline"
                            title="Редактировать"
                            onclick="openEditShopModal({
                                id: <?php echo e($shop->id); ?>,
                                name: <?php echo \Illuminate\Support\Js::from($shop->name)->toHtml() ?>,
                                code: <?php echo \Illuminate\Support\Js::from($shop->code)->toHtml() ?>,
                                address: <?php echo \Illuminate\Support\Js::from($shop->address)->toHtml() ?>,
                                phone: <?php echo \Illuminate\Support\Js::from($shop->phone)->toHtml() ?>,
                                is_active: <?php echo e($shop->is_active ? 'true' : 'false'); ?>

                            })"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>

                        <?php if($isMain): ?>
                            <button
                                type="button"
                                class="btn btn-danger"
                                title="Удалить основной магазин (нужен перенос)"
                                onclick="openTransferMainShopModal(<?php echo e($shop->id); ?>)"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php else: ?>
                            <form action="<?php echo e(route('admin.shops.delete', $shop)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Удалить магазин? Если есть связанные данные — он будет деактивирован.')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Магазины не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div id="addShopModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"><i class="bi bi-plus-circle"></i> Добавить магазин</div>
                <button type="button" class="btn-close" onclick="hideAddShopModal()">&times;</button>
            </div>
            <div class="modal-body">
                <?php if($errors->any()): ?>
                    <div style="margin-bottom: 12px; padding: 12px; border-radius: 12px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25);">
                        <div style="font-weight:700; color:#ef4444; margin-bottom: 6px;">
                            <i class="bi bi-exclamation-triangle"></i> Проверьте поля
                        </div>
                        <ul style="margin:0; padding-left: 18px; color:#f1f5f9; font-size:14px;">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li style="margin: 4px 0;"><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('admin.shops.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Название *</label>
                            <input class="input" type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="255">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Code *</label>
                            <input class="input" type="text" name="code" value="<?php echo e(old('code')); ?>" required maxlength="50">
                        </div>
                    </div>

                    <div style="margin-top:12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Адрес</label>
                        <input class="input" type="text" name="address" value="<?php echo e(old('address')); ?>" maxlength="65535">
                    </div>

                    <div style="margin-top:12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Телефон</label>
                        <input class="input" type="text" name="phone" value="<?php echo e(old('phone')); ?>" maxlength="20">
                    </div>

                    <div style="margin-top:12px; display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="is_active" value="1" id="addShopIsActive" <?php echo e(old('is_active') ? 'checked' : ''); ?>>
                        <label for="addShopIsActive" style="font-weight:700; cursor:pointer; color:#f1f5f9;">Активен</label>
                    </div>

                    <div style="margin-top: 18px; display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">
                            <i class="bi bi-save"></i> Создать
                        </button>
                        <button type="button" class="btn btn-outline" onclick="hideAddShopModal()" style="flex:1; justify-content:center;">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="editShopModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"><i class="bi bi-pencil"></i> Редактировать магазин</div>
                <button type="button" class="btn-close" onclick="hideEditShopModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editShopForm" action="#" method="POST">
                    <?php echo csrf_field(); ?>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Название *</label>
                            <input class="input" type="text" name="name" id="editShopName" value="" required maxlength="255">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Code *</label>
                            <input class="input" type="text" name="code" id="editShopCode" value="" required maxlength="50">
                        </div>
                    </div>

                    <div style="margin-top:12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Адрес</label>
                        <input class="input" type="text" name="address" id="editShopAddress" value="" maxlength="65535">
                    </div>

                    <div style="margin-top:12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Телефон</label>
                        <input class="input" type="text" name="phone" id="editShopPhone" value="" maxlength="20">
                    </div>

                    <div style="margin-top:12px; display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="is_active" value="1" id="editShopIsActive">
                        <label for="editShopIsActive" style="font-weight:700; cursor:pointer; color:#f1f5f9;">Активен</label>
                    </div>

                    <div style="margin-top: 18px; display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">
                            <i class="bi bi-save"></i> Сохранить
                        </button>
                        <button type="button" class="btn btn-outline" onclick="hideEditShopModal()" style="flex:1; justify-content:center;">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="transferMainShopModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"><i class="bi bi-arrow-left-right"></i> Перенос в новый магазин</div>
                <button type="button" class="btn-close" onclick="hideTransferMainShopModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 12px; color:#f1f5f9;">
                    Основной магазин нельзя удалить без переноса сотрудников и остатков в другой магазин.
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Перенести в магазин</label>
                    <select id="transferToShopSelect" class="input" style="appearance:none;" onchange="onTransferSelectChange()">
                        <?php if(isset($mainShop) && $mainShop): ?>
                            <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if((int)$s->id !== (int)$mainShop->id): ?>
                                    <option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div style="margin-bottom: 12px;">
                    <button type="button" class="btn btn-outline" style="width:100%; justify-content:center;" onclick="hideTransferMainShopModal(); showAddShopModal();">
                        <i class="bi bi-plus-lg"></i> Создать новый магазин
                    </button>
                </div>

                <form id="transferDeleteForm" action="#" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <input type="hidden" name="transfer_to_shop_id" id="transferToShopId" value="">
                    <button type="button" id="confirmTransferDeleteBtn" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="submitTransferDelete()">
                        <i class="bi bi-trash"></i> Перенести и деактивировать
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showAddShopModal() {
        const modal = document.getElementById('addShopModal');
        if (!modal) return;
        modal.style.display = 'block';
        modal.classList.add('show');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop addshop-backdrop';
        backdrop.onclick = hideAddShopModal;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    function hideAddShopModal() {
        const modal = document.getElementById('addShopModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.querySelectorAll('.addshop-backdrop').forEach(b => b.remove());
        document.body.style.overflow = '';
    }

    function openEditShopModal(shop) {
        const modal = document.getElementById('editShopModal');
        const form = document.getElementById('editShopForm');
        if (!modal || !form) return;

        document.getElementById('editShopName').value = shop.name ?? '';
        document.getElementById('editShopCode').value = shop.code ?? '';
        document.getElementById('editShopAddress').value = shop.address ?? '';
        document.getElementById('editShopPhone').value = shop.phone ?? '';
        document.getElementById('editShopIsActive').checked = !!shop.is_active;

        form.action = '<?php echo e(route('admin.shops.update', ['shop' => '__ID__'])); ?>'.replace('__ID__', shop.id);

        modal.style.display = 'block';
        modal.classList.add('show');

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop editshop-backdrop';
        backdrop.onclick = hideEditShopModal;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    function hideEditShopModal() {
        const modal = document.getElementById('editShopModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.querySelectorAll('.editshop-backdrop').forEach(b => b.remove());
        document.body.style.overflow = '';
    }

    function openTransferMainShopModal(mainShopId) {
        const modal = document.getElementById('transferMainShopModal');
        const select = document.getElementById('transferToShopSelect');
        const form = document.getElementById('transferDeleteForm');
        const hidden = document.getElementById('transferToShopId');
        const btn = document.getElementById('confirmTransferDeleteBtn');
        if (!modal || !select || !form || !hidden || !btn) return;

        form.action = '<?php echo e(route('admin.shops.delete', ['shop' => '__ID__'])); ?>'.replace('__ID__', mainShopId);
        hidden.value = select.value;

        modal.style.display = 'block';
        modal.classList.add('show');

        btn.disabled = !select.value;

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop transfer-backdrop';
        backdrop.onclick = hideTransferMainShopModal;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    function onTransferSelectChange() {
        const select = document.getElementById('transferToShopSelect');
        const hidden = document.getElementById('transferToShopId');
        const btn = document.getElementById('confirmTransferDeleteBtn');
        if (!select || !hidden || !btn) return;
        hidden.value = select.value;
        btn.disabled = !select.value;
    }

    function submitTransferDelete() {
        const hidden = document.getElementById('transferToShopId');
        if (!hidden || !hidden.value) {
            alert('Выберите магазин для переноса.');
            return;
        }
        document.getElementById('transferDeleteForm').submit();
    }

    function hideTransferMainShopModal() {
        const modal = document.getElementById('transferMainShopModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.querySelectorAll('.transfer-backdrop').forEach(b => b.remove());
        document.body.style.overflow = '';
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/shops/index.blade.php ENDPATH**/ ?>