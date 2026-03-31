<?php $__env->startSection('page-title', 'Дашборд'); ?>
<?php $__env->startSection('page-subtitle', 'Обзор состояния склада и операций'); ?>

<?php $__env->startSection('content'); ?>
<!-- Stats Grid -->
<div class="stats-grid mb-8">
    <div class="glass p-6 rounded-2xl border border-white/5 shadow-xl">
        <div class="flex items-center gap-4">
            <div class="stats-icon bg-accent/15 text-accent">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-muted uppercase tracking-widest mb-1">Товары</p>
                <h4 class="text-2xl font-black text-white"><?php echo e($totalProducts); ?></h4>
            </div>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl border border-danger/20 shadow-xl">
        <div class="flex items-center gap-4">
            <div class="stats-icon bg-danger/15 text-danger">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-muted uppercase tracking-widest mb-1">Дефицит</p>
                <h4 class="text-2xl font-black text-danger"><?php echo e($lowStock); ?></h4>
            </div>
        </div>
    </div>

    <a href="<?php echo e(route('warehouse.purchases.create')); ?>" class="glass p-6 rounded-2xl border border-success/20 shadow-xl hover:bg-success/5 transition-colors">
        <div class="flex items-center gap-4">
            <div class="stats-icon bg-success/15 text-success">
                <i class="bi bi-plus-circle"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-muted uppercase tracking-widest mb-1">Приёмка</p>
                <h4 class="text-lg font-black text-success">Новая закупка</h4>
            </div>
        </div>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Purchases -->
    <div class="glass rounded-2xl border border-white/5 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <i class="bi bi-truck text-accent"></i> Последние приёмки
            </h3>
            <a href="<?php echo e(route('warehouse.purchases.index')); ?>" class="text-xs text-accent hover:underline">Все</a>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left responsive-table">
                    <tbody class="divide-y divide-white/5">
                        <?php $__empty_1 = true; $__currentLoopData = $recentPurchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white text-sm"><?php echo e($purchase->variant->product->name); ?></div>
                                <div class="text-[10px] text-muted"><?php echo e($purchase->variant->size); ?> <?php echo e($purchase->variant->color); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="badge bg-success/15 text-success font-black">+<?php echo e($purchase->quantity); ?></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-xs text-muted"><?php echo e($purchase->created_at->format('d.m H:i')); ?></div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-muted italic">Нет новых приёмок</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- New Products -->
    <div class="glass rounded-2xl border border-white/5 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <i class="bi bi-star text-warning"></i> Новые товары
            </h3>
            <a href="<?php echo e(route('warehouse.products.index')); ?>" class="text-xs text-accent hover:underline">Все</a>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left responsive-table">
                    <tbody class="divide-y divide-white/5">
                        <?php $__empty_1 = true; $__currentLoopData = $newProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white text-sm"><?php echo e($product->name); ?></div>
                                <div class="text-[10px] text-muted font-mono uppercase"><?php echo e($product->article); ?></div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-[10px] text-muted mb-1"><?php echo e($product->category->name ?? 'Без категории'); ?></div>
                                <div class="text-[9px] text-muted/50"><?php echo e($product->createdBy->full_name ?? 'Система'); ?></div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center text-muted italic">Нет новых товаров</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.warehouse', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\Modules\Warehouse\Providers/../resources/views/dashboard.blade.php ENDPATH**/ ?>