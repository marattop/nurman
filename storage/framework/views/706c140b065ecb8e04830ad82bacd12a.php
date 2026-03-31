<?php $__env->startSection('page-title', 'Мой профиль'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div class="page-icon"><i class="bi bi-person-circle"></i></div>
    <div>
        <h1 class="page-title">Мой профиль</h1>
        <p class="page-subtitle">Настройки профиля и аватар</p>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="bg-success/15 border border-success/30 text-success rounded-xl p-4 mb-6 flex items-center gap-3">
        <i class="bi bi-check-circle-fill text-xl"></i>
        <span class="font-medium"><?php echo e(session('success')); ?></span>
    </div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="bg-danger/15 border border-danger/30 text-danger rounded-xl p-4 mb-6">
        <div class="flex items-center gap-3 mb-2">
            <i class="bi bi-exclamation-triangle-fill text-xl"></i>
            <span class="font-bold">Ошибка</span>
        </div>
        <ul class="list-disc list-inside text-sm opacity-90 space-y-1 ml-2">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="card !p-0 overflow-hidden border-white/5">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5">
            <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                <i class="bi bi-person text-accent"></i> Информация
            </h3>
        </div>
        <div class="p-5">
            <div class="text-center mb-6">
                
                <div class="relative inline-block">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-accent to-accent-hover flex items-center justify-center text-bg font-black text-5xl mx-auto mb-4 border-4 border-white/10 shadow-xl overflow-hidden">
                        <?php if($user->avatar): ?>
                            <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->full_name); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?php echo e(strtoupper(substr($user->full_name ?? $user->username, 0, 1))); ?>

                        <?php endif; ?>
                    </div>
                    <div class="absolute bottom-0 right-0 w-10 h-10 bg-success border-4 border-bg rounded-full flex items-center justify-center">
                        <i class="bi bi-check text-white text-xs"></i>
                    </div>
                </div>
                
                <h2 class="text-2xl font-black text-white mb-1"><?php echo e($user->full_name ?? $user->username); ?></h2>
                <p class="text-muted text-sm"><?php echo e($user->role->display_name ?? 'Пользователь'); ?></p>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex items-center gap-3 text-muted">
                    <i class="bi bi-person text-accent"></i>
                    <span class="text-text"><?php echo e($user->username); ?></span>
                </div>
                <?php if($user->email): ?>
                <div class="flex items-center gap-3 text-muted">
                    <i class="bi bi-envelope text-accent"></i>
                    <span class="text-text"><?php echo e($user->email); ?></span>
                </div>
                <?php endif; ?>
                <?php if($user->phone): ?>
                <div class="flex items-center gap-3 text-muted">
                    <i class="bi bi-phone text-accent"></i>
                    <span class="text-text"><?php echo e($user->phone); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex items-center gap-3 text-muted">
                    <i class="bi bi-shield-check text-accent"></i>
                    <span class="text-text"><?php echo e($user->role->name ?? '—'); ?></span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="lg:col-span-2 card !p-0 overflow-hidden border-white/5">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5">
            <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                <i class="bi bi-pencil text-accent"></i> Редактировать профиль
            </h3>
        </div>
        <div class="p-5">
            <form method="POST" action="<?php echo e(route('admin.profile.update')); ?>" enctype="multipart/form-data" class="space-y-5">
                <?php echo csrf_field(); ?>

                
                <div>
                    <label class="block font-semibold mb-3 text-text text-sm">Аватар</label>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-accent to-accent-hover flex items-center justify-center text-bg font-black text-3xl border-4 border-white/10 overflow-hidden shadow-lg flex-shrink-0">
                            <?php if($user->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->full_name); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?php echo e(strtoupper(substr($user->full_name ?? $user->username, 0, 1))); ?>

                            <?php endif; ?>
                        </div>
                        <div class="flex-1 w-full text-center sm:text-left">
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <label for="avatar" class="btn btn-outline py-2.5 px-4 text-sm cursor-pointer justify-center">
                                    <i class="bi bi-upload"></i> Загрузить фото
                                </label>
                                <?php if($user->avatar): ?>
                                    <button type="button" onclick="removeAvatar()" class="btn btn-danger py-2.5 px-4 text-sm justify-center">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                <?php endif; ?>
                            </div>
                            <p class="text-[10px] text-muted mt-3">Рекомендуемый размер: PNG, JPG до 2MB. Квадратное изображение (1:1) выглядит лучше всего.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-semibold mb-2 text-text text-sm">ФИО</label>
                        <input type="text" name="full_name" class="input" value="<?php echo e(old('full_name', $user->full_name)); ?>" placeholder="Иванов Иван">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-text text-sm">Логин</label>
                        <input type="text" name="username" class="input" value="<?php echo e(old('username', $user->username)); ?>" disabled>
                        <p class="text-[10px] text-muted mt-1">Логин нельзя изменить</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-semibold mb-2 text-text text-sm">Email</label>
                        <input type="email" name="email" class="input" value="<?php echo e(old('email', $user->email)); ?>" placeholder="example@mail.ru">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-text text-sm">Телефон</label>
                        <input type="tel" name="phone" class="input" value="<?php echo e(old('phone', $user->phone)); ?>" placeholder="+996 555 123 456">
                    </div>
                </div>

                <div class="border-t border-white/5 pt-5 mt-5">
                    <h4 class="text-sm font-semibold text-text mb-4 flex items-center gap-2">
                        <i class="bi bi-lock text-accent"></i> Смена пароля
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-semibold mb-2 text-text text-sm">Новый пароль</label>
                            <input type="password" name="password" class="input" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-text text-sm">Подтверждение</label>
                            <input type="password" name="password_confirmation" class="input" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="submit" class="btn btn-primary flex-1 justify-center">
                        <i class="bi bi-check-lg"></i> Сохранить
                    </button>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline justify-center">
                        <i class="bi bi-x-lg"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.w-20.h-20 img')?.remove();
            const avatarDiv = document.querySelector('.w-20.h-20');
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';
            avatarDiv.appendChild(img);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeAvatar() {
    if (confirm('Вы уверены, что хотите удалить аватар?')) {
        // Здесь можно добавить AJAX запрос для удаления
        document.querySelector('input[type="file"]').value = '';
        location.reload();
    }
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/profile.blade.php ENDPATH**/ ?>