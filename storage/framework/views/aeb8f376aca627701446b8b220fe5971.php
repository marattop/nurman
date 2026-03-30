<?php $__env->startSection('page-title', 'Редактирование сотрудника'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-icon"><i class="bi bi-pencil"></i></div>
    <div>
        <h1 class="page-title">Редактирование сотрудника</h1>
        <p class="page-subtitle">Обновление данных и роли</p>
    </div>
    <div style="margin-left:auto; display:flex; gap:10px;">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
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

<?php if($errors->any()): ?>
    <div class="card" style="padding: 16px; border: 1px solid rgba(239,68,68,0.25); margin-bottom: 16px;">
        <div style="font-weight:700; color:#ef4444; margin-bottom: 8px;">
            <i class="bi bi-exclamation-triangle"></i> Исправьте ошибки перед сохранением
        </div>
        <ul style="margin:0; padding-left: 18px; color:#f1f5f9; font-size:14px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="bi bi-person-badge" style="color:#38bdf8;"></i> Информация о сотруднике
        </h3>
        <div>
            <span class="badge badge-blue"><?php echo e($user->full_name ?? $user->username); ?></span>
        </div>
    </div>

    <div style="padding: 24px;">
        <div class="users-edit-grid" style="display:grid; grid-template-columns: 1fr 340px; gap:16px;">
            <div>
                <form action="<?php echo e(route('admin.users.update', $user)); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                        <div>
                            <div style="font-size: 13px; color:#f1f5f9; margin-bottom: 6px; font-weight: 600;">
                                Логин <span style="color:#ef4444;">*</span>
                            </div>
                            <input class="input" type="text" name="username" value="<?php echo e(old('username', $user->username)); ?>" required>
                        </div>
                        <div>
                            <div style="font-size: 13px; color:#f1f5f9; margin-bottom: 6px; font-weight: 600;">
                                Email
                            </div>
                            <input class="input" type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" />
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top: 12px;">
                        <div>
                            <div style="font-size: 13px; color:#f1f5f9; margin-bottom: 6px; font-weight: 600;">
                                Телефон
                            </div>
                            <input class="input" type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>">
                        </div>
                        <div>
                            <div style="font-size: 13px; color:#f1f5f9; margin-bottom: 6px; font-weight: 600;">
                                ФИО
                            </div>
                            <input class="input" type="text" name="full_name" value="<?php echo e(old('full_name', $user->full_name)); ?>">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top: 12px;">
                        <div>
                            <div style="font-size: 13px; color:#f1f5f9; margin-bottom: 6px; font-weight: 600;">
                                Роль <span style="color:#ef4444;">*</span>
                            </div>
                            <select name="role_id" class="input" style="appearance: none;" required>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php echo e((int)$user->role_id === (int)$role->id ? 'selected' : ''); ?>>
                                        <?php echo e($role->display_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <div style="font-size: 13px; color:#f1f5f9; margin-bottom: 6px; font-weight: 600;">
                                Магазин
                            </div>
                            <select name="shop_id" class="input" style="appearance: none;">
                                <option value="">Не выбран</option>
                                <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($shop->id); ?>" <?php echo e((int)($user->shop_id ?? 0) === (int)$shop->id ? 'selected' : ''); ?>>
                                        <?php echo e($shop->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div style="font-size: 12px; color:#94a3b8; margin-top: 6px;">
                                Магазин, к которому прикреплён сотрудник
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <div style="font-size: 13px; color:#f1f5f9; margin-bottom: 6px; font-weight: 600;">
                            Новый пароль
                        </div>
                        <input class="input" type="password" name="password" placeholder="Оставьте пустым, чтобы не менять">
                        <div style="font-size: 12px; color:#94a3b8; margin-top: 6px;">
                            Оставьте пустым, если не хотите менять пароль
                        </div>
                    </div>

                    <div style="margin-top: 14px; display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="is_active" value="1" id="isActive" <?php echo e($user->is_active ? 'checked' : ''); ?>>
                        <label for="isActive" style="font-weight:700; color:#f1f5f9; cursor:pointer;">Активен</label>
                        <span style="font-size:12px; color:#94a3b8;">Сотрудник сможет входить в систему</span>
                    </div>

                    <div style="margin-top: 18px; display:flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Сохранить
                        </button>
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline">
                            <i class="bi bi-x-lg"></i> Отмена
                        </a>
                    </div>
                </form>
            </div>

            <div>
                <div class="card" style="background: transparent; border: 1px solid rgba(255,255,255,0.05); padding: 16px;">
                    <div style="font-weight:700; margin-bottom: 12px; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-info-circle" style="color:#38bdf8;"></i> Информация
                    </div>
                    <table class="table" style="width:100%; border-collapse: collapse; margin:0;">
                        <tr>
                            <td style="color:#94a3b8; padding: 8px 0;">ID</td>
                            <td style="padding: 8px 0; font-weight:700;"><?php echo e($user->id); ?></td>
                        </tr>
                        <tr>
                            <td style="color:#94a3b8; padding: 8px 0;">Email</td>
                            <td style="padding: 8px 0;"><?php echo e($user->email ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td style="color:#94a3b8; padding: 8px 0;">Дата регистрации</td>
                            <td style="padding: 8px 0;"><?php echo e($user->created_at?->format('d.m.Y H:i') ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td style="color:#94a3b8; padding: 8px 0;">Последний вход</td>
                            <td style="padding: 8px 0;"><?php echo e($user->last_login_at?->format('d.m.Y H:i') ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td style="color:#94a3b8; padding: 8px 0;">Статус</td>
                            <td style="padding: 8px 0;">
                                <?php if($user->is_active): ?>
                                    <span class="badge badge-success"><i class="bi bi-check-circle"></i> Активен</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="bi bi-x-circle"></i> Заблокирован</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="height: 12px;"></div>

                <div class="card" style="background: transparent; border: 1px solid rgba(255,255,255,0.05); padding: 16px;">
                    <div style="font-weight:700; margin-bottom: 12px; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-exclamation-triangle" style="color:#f59e0b;"></i> Опасная зона
                    </div>
                    <form action="<?php echo e(route('admin.users.delete', $user)); ?>" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить сотрудника?')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-danger" style="width:100%; justify-content:center;">
                            <i class="bi bi-trash"></i> Удалить сотрудника
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 992px) {
        .users-edit-grid { grid-template-columns: 1fr !important; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>