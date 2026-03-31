<?php $__env->startSection('page-title', 'Команда'); ?>
<?php $__env->startSection('page-subtitle', 'Управление пользователями и правами доступа'); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('owner.users.create')); ?>" class="btn glass bg-accent/20 hover:bg-accent/30 text-accent px-4 py-2 rounded-xl flex items-center gap-2 transition-all">
        <i class="bi bi-person-plus-fill"></i>
        <span>Добавить пользователя</span>
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Filters -->
    <div class="glass p-6 rounded-2xl">
        <form method="GET" action="<?php echo e(route('owner.users.index')); ?>" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-muted"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Поиск по имени, email или телефону..." 
                    class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 pl-11 pr-4 focus:outline-none focus:border-accent/50 transition-colors">
            </div>
            <div class="w-full md:w-64">
                <select name="role" onchange="this.form.submit()" 
                    class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                    <option value="">Все роли</option>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->id); ?>" <?php echo e(request('role') == $role->id ? 'selected' : ''); ?>><?php echo e($role->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.02]">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Пользователь</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Роль</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Статус</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Дата создания</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4" data-label="Пользователь">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold">
                                        <?php echo e(strtoupper(substr($user->username, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div class="font-semibold text-white"><?php echo e($user->username); ?></div>
                                        <div class="text-xs text-muted"><?php echo e($user->email ?? 'Нет email'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4" data-label="Роль">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-accent/10 text-accent">
                                    <?php echo e($user->role?->name ?? 'Без роли'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4" data-label="Статус">
                                <?php if($user->is_active): ?>
                                    <span class="flex items-center gap-1.5 text-success text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                                        Активен
                                    </span>
                                <?php else: ?>
                                    <span class="flex items-center gap-1.5 text-danger text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-danger"></span>
                                        Не активен
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-muted" data-label="Дата создания">
                                <?php echo e($user->created_at->format('d.m.Y')); ?>

                            </td>
                            <td class="px-6 py-4 text-right" data-label="Действия">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('owner.users.edit', $user)); ?>" class="p-2 hover:bg-white/5 rounded-lg text-muted hover:text-white transition-colors">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('owner.users.delete', $user)); ?>" class="inline-block" onsubmit="return confirm('Вы уверены?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="p-2 hover:bg-danger/10 rounded-lg text-muted hover:text-danger transition-colors">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-muted">
                                <i class="bi bi-people text-4xl mb-4 block opacity-20"></i>
                                <p>Пользователи не найдены</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($users->hasPages()): ?>
            <div class="px-6 py-4 border-t border-white/5">
                <?php echo e($users->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/owner/users/index.blade.php ENDPATH**/ ?>