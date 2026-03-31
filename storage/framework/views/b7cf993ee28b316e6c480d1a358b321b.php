<?php $__env->startSection('page-title', 'Отчёт по расходам'); ?>
<?php $__env->startSection('page-subtitle', 'Контроль операционных затрат и издержек'); ?>

<?php $__env->startSection('page-actions'); ?>
    <div class="flex flex-col md:flex-row gap-3">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <select name="period" onchange="this.form.submit()" 
                class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                <option value="today" <?php echo e(request('period') === 'today' ? 'selected' : ''); ?>>Сегодня</option>
                <option value="week" <?php echo e(request('period') === 'week' ? 'selected' : ''); ?>>Неделя</option>
                <option value="month" <?php echo e(request('period', 'month') === 'month' ? 'selected' : ''); ?>>Месяц</option>
                <option value="year" <?php echo e(request('period') === 'year' ? 'selected' : ''); ?>>Год</option>
            </select>

            <select name="category" onchange="this.form.submit()" 
                class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                <option value="">Все категории</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat); ?>" <?php echo e($category == $cat ? 'selected' : ''); ?>>
                        <?php if($cat === 'rent'): ?> Аренда <?php elseif($cat === 'utilities'): ?> Коммуналка <?php elseif($cat === 'salary'): ?> Зарплата
                        <?php elseif($cat === 'taxes'): ?> Налоги <?php elseif($cat === 'marketing'): ?> Маркетинг <?php else: ?> <?php echo e(ucfirst($cat)); ?> <?php endif; ?>
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            
            <select name="shop_id" onchange="this.form.submit()" 
                class="bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                <option value="">Все магазины</option>
                <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($shop->id); ?>" <?php echo e($shopId == $shop->id ? 'selected' : ''); ?>><?php echo e($shop->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
        <a href="<?php echo e(route('owner.reports.index')); ?>" class="btn glass bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-xl flex items-center justify-center gap-2 transition-all border border-white/10">
            <i class="bi bi-arrow-left"></i>
            <span>Назад</span>
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Total Expenses Card -->
    <div class="glass p-8 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6 group">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-receipt-cutoff text-4xl"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-muted uppercase tracking-widest">Общие расходы за период</p>
                <h3 class="text-4xl font-black text-rose-500 mt-1"><?php echo e(number_format($totalAmount, 0, '.', ' ')); ?> <span class="text-xl font-medium opacity-60">сом</span></h3>
            </div>
        </div>
        <div class="hidden md:block h-12 w-px bg-white/10"></div>
        <div class="flex gap-4">
            <div class="text-center">
                <p class="text-[10px] text-muted uppercase font-bold">Активных статей</p>
                <p class="text-xl font-bold text-white"><?php echo e(count($categories)); ?></p>
            </div>
            <div class="text-center">
                <p class="text-[10px] text-muted uppercase font-bold">Транзакций</p>
                <p class="text-xl font-bold text-white"><?php echo e($expenses->total()); ?></p>
            </div>
        </div>
    </div>

    <!-- Expenses Details Table -->
    <div class="glass rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
            <h3 class="font-bold text-white flex items-center gap-2 uppercase tracking-tight text-sm">
                <i class="bi bi-list-check text-rose-500"></i>
                Детализация расходов
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-muted">
                        <th class="px-6 py-4">№</th>
                        <th class="px-6 py-4">Дата</th>
                        <th class="px-6 py-4">Категория</th>
                        <th class="px-6 py-4">Описание</th>
                        <th class="px-6 py-4">Магазин</th>
                        <th class="px-6 py-4 text-right">Сумма</th>
                        <th class="px-6 py-4 text-right">Автор</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4" data-label="№">
                                <span class="text-[10px] font-mono text-muted">#<?php echo e($expense->id); ?></span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-white/90" data-label="Дата">
                                <?php echo e($expense->expense_date->format('d.m.Y')); ?>

                            </td>
                            <td class="px-6 py-4" data-label="Категория">
                                <?php if($expense->category === 'rent'): ?> 
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-500 text-[10px] font-bold border border-amber-500/20">Аренда</span>
                                <?php elseif($expense->category === 'utilities'): ?> 
                                    <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-500 text-[10px] font-bold border border-cyan-500/20">Коммуналка</span>
                                <?php elseif($expense->category === 'salary'): ?> 
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold border border-emerald-500/20">Зарплата</span>
                                <?php elseif($expense->category === 'taxes'): ?> 
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-500 text-[10px] font-bold border border-rose-500/20">Налоги</span>
                                <?php elseif($expense->category === 'marketing'): ?> 
                                    <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">Маркетинг</span>
                                <?php else: ?> 
                                    <span class="px-2 py-0.5 rounded-full bg-white/10 text-white/60 text-[10px] font-bold border border-white/20"><?php echo e(ucfirst($expense->category)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-xs text-muted max-w-[200px] truncate" data-label="Описание">
                                <?php echo e($expense->description ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4" data-label="Магазин">
                                <span class="text-xs text-white/80"><?php echo e($expense->shop?->name ?? '-'); ?></span>
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Сумма">
                                <span class="font-black text-rose-500 text-sm"><?php echo e(number_format($expense->amount, 0, '.', ' ')); ?> сом</span>
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Автор">
                                <span class="text-[10px] text-muted italic"><?php echo e($expense->user?->username ?? '-'); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-muted">
                                <div class="flex flex-col items-center">
                                    <i class="bi bi-wallet2 text-5xl mb-4 opacity-10"></i>
                                    <p class="text-sm font-medium">Расходы не найдены за этот период</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($expenses->hasPages()): ?>
            <div class="px-6 py-4 border-t border-white/5">
                <?php echo e($expenses->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/owner/reports/expenses.blade.php ENDPATH**/ ?>