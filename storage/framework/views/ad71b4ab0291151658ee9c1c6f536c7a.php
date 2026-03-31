<?php $__env->startSection('page-title', 'Перемещения'); ?>
<?php $__env->startSection('page-subtitle', 'Перемещения товаров между магазинами'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <a href="<?php echo e(route('warehouse.transfers.create')); ?>" class="btn btn-primary w-full sm:w-auto px-8 py-3 rounded-2xl gap-2 font-black shadow-glow">
        <i class="bi bi-plus-lg"></i> НОВОЕ ПЕРЕМЕЩЕНИЕ
    </a>
    <div class="glass p-2 rounded-2xl flex items-center gap-2 border-white/5 w-full sm:w-80">
        <i class="bi bi-calendar text-muted ml-3"></i>
        <input type="date" class="bg-transparent border-none text-white px-2 py-1 focus:ring-0 text-sm w-full cursor-pointer">
    </div>
</div>

<div class="glass rounded-[2rem] overflow-hidden border border-white/5 shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] font-black uppercase tracking-widest text-muted">
                    <th class="px-6 py-5">№</th>
                    <th class="px-6 py-5">Дата</th>
                    <th class="px-6 py-5">Откуда</th>
                    <th class="px-6 py-5">Куда</th>
                    <th class="px-6 py-5 text-center">Товаров</th>
                    <th class="px-6 py-5 text-right">Статус</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php $__empty_1 = true; $__currentLoopData = $transfers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-white/[0.02]">
                    <td class="px-6 py-4">
                        <div class="font-bold text-white text-sm">#<?php echo e($transfer->id); ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-white text-sm"><?php echo e($transfer->created_at->format('d.m.Y')); ?></div>
                        <div class="text-[10px] text-muted"><?php echo e($transfer->created_at->format('H:i')); ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-white text-sm"><?php echo e($transfer->fromShop->name ?? 'Склад'); ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-white text-sm"><?php echo e($transfer->toShop->name ?? 'Магазин'); ?></div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-white text-sm"><?php echo e($transfer->items_count ?? 0); ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="badge badge-<?php echo e($transfer->status === 'completed' ? 'success' : 'warning'); ?>">
                            <?php echo e($transfer->status === 'completed' ? 'Выполнено' : 'В пути'); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-muted">
                            <i class="bi bi-inbox text-3xl mb-3"></i>
                            <p class="text-sm">Перемещений пока нет</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.warehouse', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\Modules\Warehouse\Providers/../resources/views/transfers/index.blade.php ENDPATH**/ ?>