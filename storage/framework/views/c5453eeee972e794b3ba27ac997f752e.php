<?php $__env->startSection('page-title', 'Сотрудники'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-icon"><i class="bi bi-people"></i></div>
    <div>
        <h1 class="page-title">Сотрудники</h1>
        <p class="page-subtitle">Управление командой</p>
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
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список сотрудников</h3>
        <button
            type="button"
            class="btn btn-outline"
            onclick="showAddUserModal()"
        >
            <i class="bi bi-plus-lg"></i> Добавить
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Имя</th>
                    <th>Роль</th>
                    <th>Телефон</th>
                    <th>Магазин</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="Имя">
                        <div style="font-weight: 500;"><?php echo e($user->full_name ?? '—'); ?></div>
                        <div style="color: #94a3b8; font-size: 13px;"><?php echo e($user->email ?? ''); ?></div>
                    </td>
                    <td data-label="Роль">
                        <?php if($user->isOwner()): ?>
                            <span class="badge badge-blue">Владелец</span>
                        <?php elseif($user->isAdmin()): ?>
                            <span class="badge badge-blue">Админ</span>
                        <?php elseif($user->isSeller()): ?>
                            <span class="badge badge-success">Продавец</span>
                        <?php elseif($user->isWarehouse()): ?>
                            <span class="badge badge-warning">Склад</span>
                        <?php else: ?>
                            <span class="badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">Пользователь</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Телефон" style="color: #94a3b8;"><?php echo e($user->phone ?? '—'); ?></td>
                    <td data-label="Магазин" style="color: #94a3b8;"><?php echo e($user->shop?->name ?? '—'); ?></td>
                    <td data-label="Действия">
                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-outline" style="width: 100%; justify-content: center;">
                            <i class="bi bi-pencil"></i> Ред.
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Сотрудники не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div id="addUserModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="bi bi-person-plus"></i> Добавить сотрудника
                </div>
                <button type="button" class="btn-close" onclick="hideAddUserModal()">&times;</button>
            </div>
            <div class="modal-body">
                <?php if($errors->any()): ?>
                    <div style="margin-bottom: 12px; padding: 12px; border-radius: 12px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25);">
                        <div style="font-weight:700; color:#ef4444; margin-bottom: 6px;">
                            <i class="bi bi-exclamation-triangle"></i> Проверьте поля
                        </div>
                        <ul style="margin:0; padding-left: 18px;">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li style="margin: 4px 0;"><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" autocomplete="off">
                    <?php echo csrf_field(); ?>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                                Логин <span style="color:#ef4444">*</span>
                            </label>
                            <input
                                type="text"
                                name="username"
                                class="input"
                                value="<?php echo e(old('username')); ?>"
                                required
                                maxlength="50"
                            >
                        </div>

                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                                Телефон
                            </label>
                            <input
                                type="text"
                                name="phone"
                                class="input"
                                value="<?php echo e(old('phone')); ?>"
                                maxlength="20"
                            >
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            class="input"
                            value="<?php echo e(old('email')); ?>"
                            maxlength="255"
                        >
                    </div>

                    <div style="margin-top: 12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                            ФИО
                        </label>
                        <input
                            type="text"
                            name="full_name"
                            class="input"
                            value="<?php echo e(old('full_name')); ?>"
                            maxlength="255"
                        >
                    </div>

                    <div style="margin-top: 12px; display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                                Роль <span style="color:#ef4444">*</span>
                            </label>
                            <select name="role_id" class="input" required>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php echo e((string)old('role_id') === (string)$role->id ? 'selected' : ''); ?>>
                                        <?php echo e($role->display_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                                Магазин
                            </label>
                            <select name="shop_id" class="input">
                                <option value="">Не выбран</option>
                                <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($shop->id); ?>" <?php echo e((string)old('shop_id') === (string)$shop->id ? 'selected' : ''); ?>>
                                        <?php echo e($shop->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                            Пароль <span style="color:#ef4444">*</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            class="input"
                            required
                            minlength="6"
                            maxlength="255"
                        >
                        <div style="color:#94a3b8; font-size:12px; margin-top:6px;">
                            Минимум 6 символов
                        </div>
                    </div>

                    <div style="margin-top: 12px; display:flex; align-items:center; gap: 10px;">
                        <input type="checkbox" name="is_active" value="1" id="addUserIsActive" <?php echo e(old('is_active') ? 'checked' : ''); ?>>
                        <label for="addUserIsActive" style="font-weight:700; cursor:pointer;">Активен</label>
                    </div>

                    <div style="margin-top: 18px; display:flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; justify-content:center;">
                            <i class="bi bi-save"></i> Создать
                        </button>
                        <button type="button" class="btn btn-outline" onclick="hideAddUserModal()" style="flex: 1; justify-content:center;">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showAddUserModal() {
        const modal = document.getElementById('addUserModal');
        if (!modal) return;
        modal.classList.add('show');
        modal.style.display = 'block';

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop adduser-backdrop';
        backdrop.onclick = hideAddUserModal;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    function hideAddUserModal() {
        const modal = document.getElementById('addUserModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.querySelectorAll('.adduser-backdrop').forEach(b => b.remove());
        document.body.style.overflow = '';
    }

    <?php if($errors->any()): ?>
        // Если валидация не прошла — открываем модалку с ошибками
        showAddUserModal();
    <?php endif; ?>
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/users/index.blade.php ENDPATH**/ ?>