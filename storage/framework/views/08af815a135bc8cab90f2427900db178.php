<?php $__env->startSection('page-title', 'Отчёт по продажам'); ?>
<?php $__env->startSection('page-subtitle', 'Анализ выручки за период: ' . $periodLabel); ?>

<?php $__env->startSection('page-actions'); ?>
    <form method="GET" action="<?php echo e(route('owner.reports.sales')); ?>" class="flex items-center gap-2">
        <select name="period" onchange="this.form.submit()" 
            class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
            <option value="today" <?php echo e($period === 'today' ? 'selected' : ''); ?>>Сегодня</option>
            <option value="week" <?php echo e($period === 'week' ? 'selected' : ''); ?>>Неделя</option>
            <option value="month" <?php echo e($period === 'month' ? 'selected' : ''); ?>>Месяц</option>
            <option value="year" <?php echo e($period === 'year' ? 'selected' : ''); ?>>Год</option>
        </select>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted uppercase tracking-wider">Всего продаж</p>
                <h3 class="text-3xl font-extrabold text-blue-400 mt-1"><?php echo e($totalTransactions); ?></h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-blue-400/10 flex items-center justify-center text-blue-400">
                <i class="bi bi-cart-check-fill text-3xl"></i>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted uppercase tracking-wider">Общая сумма</p>
                <h3 class="text-3xl font-extrabold text-emerald-400 mt-1"><?php echo e(number_format($totalSales, 0, '.', ' ')); ?> <span class="text-lg font-medium opacity-70">сом</span></h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-400/10 flex items-center justify-center text-emerald-400">
                <i class="bi bi-currency-dollar text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
            <h3 class="font-bold text-white flex items-center gap-2">
                <i class="bi bi-list-ul text-accent"></i>
                История транзакций
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.02]">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">ID</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Магазин</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Продавец</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted text-right">Сумма</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Дата</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4" data-label="ID">
                                <span class="text-xs font-mono text-muted">#<?php echo e($sale->id); ?></span>
                            </td>
                            <td class="px-6 py-4 font-medium text-white" data-label="Магазин">
                                <?php echo e($sale->shop?->name ?? 'N/A'); ?>

                            </td>
                            <td class="px-6 py-4 text-sm text-muted" data-label="Продавец">
                                <?php echo e($sale->user?->username ?? 'N/A'); ?>

                            </td>
                            <td class="px-6 py-4 text-right" data-label="Сумма">
                                <span class="font-bold text-emerald-400"><?php echo e(number_format($sale->total_amount, 0, '.', ' ')); ?> сом</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-muted" data-label="Дата">
                                <?php echo e($sale->created_at->format('d.m.Y H:i')); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-muted">
                                <i class="bi bi-inbox text-4xl mb-4 block opacity-20"></i>
                                <p>За выбранный период продаж не было</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($sales->hasPages()): ?>
            <div class="px-6 py-4 border-t border-white/5">
                <?php echo e($sales->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/owner/reports/sales.blade.php ENDPATH**/ ?>