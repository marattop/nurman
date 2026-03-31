<?php $__env->startSection('page-title', 'Складские остатки'); ?>
<?php $__env->startSection('page-subtitle', 'Контроль наличия товаров на складе и в магазине'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div class="flex gap-2 w-full sm:w-auto">
        <a href="<?php echo e(route('warehouse.stock.index')); ?>" class="btn <?php echo e(!request('low') ? 'btn-primary' : 'btn-outline'); ?> py-2 px-4 text-xs">Все</a>
        <a href="<?php echo e(route('warehouse.stock.low')); ?>" class="btn <?php echo e(request('low') ? 'btn-danger' : 'btn-outline'); ?> py-2 px-4 text-xs">Дефицит</a>
    </div>
    <div class="glass p-2 rounded-2xl flex items-center gap-2 border-white/5 w-full sm:w-80">
        <i class="bi bi-filter text-muted ml-3"></i>
        <select class="bg-transparent border-none text-white px-2 py-1 focus:ring-0 text-sm w-full cursor-pointer">
            <option value="">Все локации</option>
            <option value="warehouse">Только Склад</option>
            <option value="shop">Только Магазин</option>
        </select>
    </div>
</div>

<div class="glass rounded-[2rem] overflow-hidden border border-white/5 shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] font-black uppercase tracking-widest text-muted">
                    <th class="px-6 py-5">Товар</th>
                    <th class="px-6 py-5">Артикул</th>
                    <th class="px-6 py-5 text-center">Остаток</th>
                    <th class="px-6 py-5">Локация</th>
                    <th class="px-6 py-5 text-right">Статус</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php $__empty_1 = true; $__currentLoopData = $stock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isLow = $item->quantity <= $item->min_stock;
                ?>
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td data-label="Товар" class="px-6 py-4">
                        <div class="font-bold text-white text-sm"><?php echo e($item->variant?->product?->name ?? '—'); ?></div>
                        <div class="text-[10px] text-muted"><?php echo e($item->variant?->size); ?> <?php echo e($item->variant?->color); ?></div>
                    </td>
                    <td data-label="Артикул" class="px-6 py-4 font-mono text-xs text-muted"><?php echo e($item->variant?->product?->article ?? '—'); ?></td>
                    <td data-label="Остаток" class="px-6 py-4 text-center">
                        <span class="text-base font-black <?php echo e($isLow ? 'text-danger' : 'text-white'); ?>">
                            <?php echo e($item->quantity); ?> шт
                        </span>
                    </td>
                    <td data-label="Локация" class="px-6 py-4">
                        <?php if($item->shop_id): ?>
                            <span class="badge bg-accent/10 text-accent text-[10px]">МАГАЗИН</span>
                        <?php else: ?>
                            <span class="badge bg-warning/10 text-warning text-[10px]">СКЛАД</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Статус" class="px-6 py-4 text-right">
                        <?php if($isLow): ?>
                            <span class="text-[10px] font-black text-danger uppercase tracking-tighter">Критично</span>
                        <?php else: ?>
                            <span class="text-[10px] font-black text-success uppercase tracking-tighter">Норма</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-24 text-center text-muted italic">Остатки не найдены</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($stock->hasPages()): ?>
    <div class="px-6 py-6 border-t border-white/5">
        <?php echo e($stock->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.warehouse', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\Modules\Warehouse\Providers/../resources/views/stock/index.blade.php ENDPATH**/ ?>