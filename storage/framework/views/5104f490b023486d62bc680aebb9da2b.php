<?php $__env->startSection('title', 'Профиль - NURMAN'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4" style="max-width: 800px;">
    <h1 class="mb-4">👤 Профиль</h1>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('warning')): ?>
        <div class="alert alert-warning"><?php echo e(session('warning')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Основная информация -->
    <div class="card mb-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="card-header" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
            <h5 class="mb-0">📝 Личная информация</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('client.profile.update')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-3">
                    <label class="form-label">Телефон</label>
                    <input type="text" class="form-control" value="<?php echo e(auth()->user()->phone); ?>" disabled>
                    <small class="text-muted">Телефон нельзя изменить</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">ФИО *</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo e(auth()->user()->full_name); ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Дата рождения</label>
                        <input type="date" name="birth_date" class="form-control" value="<?php echo e($profile->birth_date?->format('Y-m-d') ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Пол</label>
                        <select name="gender" class="form-select">
                            <option value="">Не указано</option>
                            <option value="male" <?php echo e($profile->gender === 'male' ? 'selected' : ''); ?>>Мужской</option>
                            <option value="female" <?php echo e($profile->gender === 'female' ? 'selected' : ''); ?>>Женский</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Город</label>
                    <input type="text" name="city" class="form-control" value="<?php echo e($profile->city ?? ''); ?>" placeholder="Бишкек">
                </div>

                <div class="mb-3">
                    <label class="form-label">Адрес</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Улица, дом, квартира"><?php echo e($profile->address ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Сохранить изменения
                </button>
            </form>
        </div>
    </div>

    <!-- Смена пароля -->
    <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="card-header" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
            <h5 class="mb-0">🔐 Смена пароля</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('client.profile.password')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label class="form-label">Текущий пароль *</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Новый пароль *</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        <small class="text-muted">Минимум 8 символов</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Подтверждение пароля *</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-key"></i> Изменить пароль
                </button>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?php echo e(route('client.dashboard')); ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> На главную
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/client/profile/index.blade.php ENDPATH**/ ?>