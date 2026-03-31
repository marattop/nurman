<?php $__env->startSection('page-title', 'Дефицит'); ?>
<?php $__env->startSection('content'); ?>
<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-left responsive-table">
        <thead>
            <tr class="bg-white/[0.03] text-[10px] uppercase text-muted">
                <th>Товар</th>
                <th>Артикул</th>
                <th class="text-right">Остаток</th>
                <th>Статус</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $stock ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->variant?->product?->name ?? '—'); ?></td>
                    <td><?php echo e($item->variant?->product?->article ?? '—'); ?></td>
                    <td class="text-right font-bold text-danger"><?php echo e($item->quantity); ?> шт</td>
                    <td><span class="px-2 py-1 bg-danger/20 text-danger text-[10px] font-bold rounded">Дефицит</span></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="px-6 py-20 text-center text-muted">Дефицита нет</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.warehouse', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\Modules\Warehouse\Providers/../resources/views/stock/low.blade.php ENDPATH**/ ?>