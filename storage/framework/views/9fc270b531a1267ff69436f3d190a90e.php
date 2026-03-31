<?php $__env->startSection('page-title', 'Дашборд'); ?>
<?php $__env->startSection('content'); ?>
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- Товаров -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Товаров</div>
                <div style="font-size: 32px; font-weight: 700; color: #10b981;"><?php echo e($stats['products_count'] ?? 0); ?></div>
                <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                    <i class="bi bi-box-seam"></i> Активных: <?php echo e($stats['products_active'] ?? 0); ?>

                </div>
            </div>
            <div class="stats-icon" style="width: 56px; height: 56px; background: rgba(16,185,129,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
                <i class="bi bi-box"></i>
            </div>
        </div>
    </div>

    <!-- Продаж сегодня -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Продаж сегодня</div>
                <div style="font-size: 32px; font-weight: 700; color: #38bdf8;"><?php echo e($stats['today_sales'] ?? 0); ?></div>
                <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                    <i class="bi bi-cash"></i> <?php echo e(number_format($stats['today_revenue'] ?? 0, 0, '.', ' ')); ?> сом
                </div>
            </div>
            <div class="stats-icon" style="width: 56px; height: 56px; background: rgba(56,189,248,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 24px;">
                <i class="bi bi-cart"></i>
            </div>
        </div>
    </div>

    <!-- Сотрудников -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Сотрудников</div>
                <div style="font-size: 32px; font-weight: 700; color: #818cf8;"><?php echo e($stats['active_users'] ?? 0); ?></div>
                <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                    <i class="bi bi-people"></i> Всего: <?php echo e($stats['users_count'] ?? 0); ?>

                </div>
            </div>
            <div class="stats-icon" style="width: 56px; height: 56px; background: rgba(129,140,248,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 24px;">
                <i class="bi bi-person-badge"></i>
            </div>
        </div>
    </div>

    <!-- Магазинов -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Магазинов</div>
                <div style="font-size: 32px; font-weight: 700; color: #f59e0b;"><?php echo e($stats['shops_count'] ?? 0); ?></div>
                <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                    <i class="bi bi-shop"></i> Работает
                </div>
            </div>
            <div class="stats-icon" style="width: 56px; height: 56px; background: rgba(245,158,11,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 24px;">
                <i class="bi bi-shop"></i>
            </div>
        </div>
    </div>
</div>

<div class="main-grid grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Последние товары -->
    <div class="lg:col-span-2 card !p-0 overflow-hidden border-white/5 shadow-lg">
        <div class="px-5 py-4 border-b border-white/5 bg-white/5 font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-clock-history text-accent"></i> 
                <span>Последние товары</span>
            </div>
            <a href="<?php echo e(route('admin.products.index')); ?>" class="text-xs text-accent hover:underline">Все товары →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="table responsive-table w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-left px-5">Товар</th>
                        <th>Артикул</th>
                        <th>Категория</th>
                        <th class="text-right px-5">Дата</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-white/[0.02] transition-colors border-b border-white/5 last:border-0">
                        <td data-label="Товар" class="px-5">
                            <div class="flex items-center gap-3 py-1">
                                <?php if($product->photo): ?>
                                    <img src="<?php echo e(asset('storage/' . $product->photo)); ?>" class="w-10 h-10 rounded-lg object-cover border border-white/10 shadow-sm">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-lg bg-success/15 flex items-center justify-center text-success border border-success/10">
                                        <i class="bi bi-box"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex flex-col">
                                    <span class="font-bold text-text truncate max-w-[150px] sm:max-w-none"><?php echo e($product->name); ?></span>
                                    <span class="text-[10px] text-muted md:hidden"><?php echo e($product->category?->name ?? '—'); ?></span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Артикул" class="text-muted font-mono text-xs"><?php echo e($product->article); ?></td>
                        <td data-label="Категория" class="text-muted text-xs hidden md:table-cell"><?php echo e($product->category?->name ?? '—'); ?></td>
                        <td data-label="Дата" class="text-muted text-xs text-right px-5"><?php echo e($product->created_at->format('d.m H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center py-12 text-muted italic">Нет новых товаров</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Заканчиваются -->
    <div class="card !p-0 overflow-hidden border-white/5 shadow-lg border-danger/10">
        <div class="px-5 py-4 border-b border-white/5 bg-danger/5 font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-exclamation-triangle text-danger"></i> 
                <span>Дефицит</span>
            </div>
            <?php if($deficitCount > 0): ?>
                <span class="badge badge-danger"><?php echo e($deficitCount); ?> поз.</span>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="table responsive-table w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-left px-5">Товар</th>
                        <th class="text-right px-5">Остаток</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-white/[0.02] transition-colors border-b border-white/5 last:border-0">
                        <td data-label="Товар" class="px-5 py-3">
                            <div class="font-bold text-text text-sm truncate max-w-[120px] sm:max-w-none"><?php echo e($product->name); ?></div>
                            <div class="text-[10px] text-muted font-mono"><?php echo e($product->article); ?></div>
                        </td>
                        <td data-label="Остаток" class="text-right px-5">
                            <?php if($product->quantity <= 2): ?>
                                <span class="text-danger font-bold"><?php echo e($product->quantity); ?> шт</span>
                            <?php elseif($product->quantity <= 5): ?>
                                <span class="text-warning font-bold"><?php echo e($product->quantity); ?> шт</span>
                            <?php else: ?>
                                <span class="text-success font-bold"><?php echo e($product->quantity); ?> шт</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="2" class="text-center py-12 text-muted italic">Всё в наличии</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($deficitCount > 5): ?>
        <div class="p-3 bg-white/5 border-t border-white/5 text-center">
            <a href="<?php echo e(route('admin.purchases.deficit')); ?>" class="text-xs text-accent hover:underline">Смотреть весь дефицит</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
@media (max-width: 768px) {
    .stats-icon {
        width: 48px !important;
        height: 48px !important;
        font-size: 20px !important;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>