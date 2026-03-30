<?php $__env->startSection('page-title', 'Дашборд'); ?>
<?php $__env->startSection('page-subtitle', 'Обзор ключевых показателей вашего бизнеса'); ?>

<?php $__env->startSection('content'); ?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 md:gap-6 mb-8">
    <!-- Revenue -->
    <div class="glass p-5 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 flex items-center justify-center text-sky-500 text-xl">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <?php if($revenueGrowth > 0): ?>
                <span class="flex items-center text-success text-[10px] font-bold bg-success/10 px-1.5 py-0.5 rounded-lg">
                    <i class="bi bi-arrow-up-short"></i> <?php echo e(number_format(abs($revenueGrowth), 1)); ?>%
                </span>
            <?php elseif($revenueGrowth < 0): ?>
                <span class="flex items-center text-danger text-[10px] font-bold bg-danger/10 px-1.5 py-0.5 rounded-lg">
                    <i class="bi bi-arrow-down-short"></i> <?php echo e(number_format(abs($revenueGrowth), 1)); ?>%
                </span>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Выручка</p>
            <h3 class="text-xl font-extrabold mt-1 truncate"><?php echo e(number_format($revenue->total, 0, '.', ' ')); ?> <span class="text-xs font-normal opacity-60">сом</span></h3>
            <p class="text-[10px] text-muted mt-1"><i class="bi bi-cart"></i> <?php echo e($revenue->count); ?> продаж</p>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
            <i class="bi bi-currency-dollar text-7xl"></i>
        </div>
    </div>

    <!-- COGS -->
    <div class="glass p-5 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 text-xl">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Себестоимость</p>
            <h3 class="text-xl font-extrabold mt-1 truncate"><?php echo e(number_format($costOfGoods, 0, '.', ' ')); ?> <span class="text-xs font-normal opacity-60">сом</span></h3>
            <p class="text-[10px] text-muted mt-1">Закупки: <?php echo e(number_format($purchases->total, 0, '.', ' ')); ?> сом</p>
        </div>
    </div>

    <!-- Gross Profit -->
    <div class="glass p-5 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-xl">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <span class="text-muted text-[10px] font-bold bg-white/5 px-1.5 py-0.5 rounded-lg"><?php echo e(number_format($grossMargin, 1)); ?>%</span>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Вал. прибыль</p>
            <h3 class="text-xl font-extrabold mt-1 truncate text-emerald-500"><?php echo e(number_format($grossProfit, 0, '.', ' ')); ?> <span class="text-xs font-normal opacity-60">сом</span></h3>
            <p class="text-[10px] text-muted mt-1">Маржинальность</p>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="glass p-5 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-<?php echo e($netProfit >= 0 ? 'emerald' : 'rose'); ?>-500/10 flex items-center justify-center text-<?php echo e($netProfit >= 0 ? 'emerald' : 'rose'); ?>-500 text-xl">
                <i class="bi bi-piggy-bank"></i>
            </div>
            <?php if($profitGrowth > 0): ?>
                <span class="flex items-center text-success text-[10px] font-bold bg-success/10 px-1.5 py-0.5 rounded-lg">
                    <i class="bi bi-arrow-up-short"></i> <?php echo e(number_format(abs($profitGrowth), 1)); ?>%
                </span>
            <?php elseif($profitGrowth < 0): ?>
                <span class="flex items-center text-danger text-[10px] font-bold bg-danger/10 px-1.5 py-0.5 rounded-lg">
                    <i class="bi bi-arrow-down-short"></i> <?php echo e(number_format(abs($profitGrowth), 1)); ?>%
                </span>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Чистая прибыль</p>
            <h3 class="text-xl font-extrabold mt-1 truncate <?php echo e($netProfit >= 0 ? 'text-emerald-500' : 'text-rose-500'); ?>"><?php echo e(number_format(abs($netProfit), 0, '.', ' ')); ?> <span class="text-xs font-normal opacity-60">сом</span></h3>
            <p class="text-[10px] text-muted mt-1">Итого за период</p>
        </div>
    </div>

    <!-- Expenses -->
    <div class="glass p-5 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 text-xl">
                <i class="bi bi-credit-card"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Расходы</p>
            <h3 class="text-xl font-extrabold mt-1 truncate text-rose-400"><?php echo e(number_format($expenses->total, 0, '.', ' ')); ?> <span class="text-xs font-normal opacity-60">сом</span></h3>
            <p class="text-[10px] text-muted mt-1"><?php echo e($expenses->count); ?> операций</p>
        </div>
    </div>

    <!-- Purchases -->
    <div class="glass p-5 rounded-2xl relative overflow-hidden group hover:border-accent/30 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center text-accent text-xl">
                <i class="bi bi-truck"></i>
            </div>
        </div>
        <div>
            <p class="text-muted text-[10px] font-bold uppercase tracking-widest">Закупки</p>
            <h3 class="text-xl font-extrabold mt-1 truncate"><?php echo e(number_format($purchases->total, 0, '.', ' ')); ?> <span class="text-xs font-normal opacity-60">сом</span></h3>
            <p class="text-[10px] text-muted mt-1"><?php echo e(number_format($purchases->quantity ?? 0, 0, '.', ' ')); ?> ед. товара</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue By Shop -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-shop text-accent"></i> Выручка по магазинам
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                        <th class="px-6 py-3 font-bold">Магазин</th>
                        <th class="px-6 py-3 font-bold text-center">Продаж</th>
                        <th class="px-6 py-3 font-bold text-right">Сумма</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php $__empty_1 = true; $__currentLoopData = $revenueByShop; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4 text-sm font-semibold" data-label="Магазин"><?php echo e($shop->name); ?></td>
                        <td class="px-6 py-4 text-center" data-label="Продаж">
                            <span class="px-2 py-0.5 rounded-full bg-accent/10 text-accent text-xs font-bold"><?php echo e($shop->count); ?></span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-accent" data-label="Сумма"><?php echo e(number_format($shop->total, 0, '.', ' ')); ?> сом</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-muted text-sm italic">Нет данных</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expenses By Category -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="bi bi-pie-chart text-rose-500"></i> Расходы по категориям
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                        <th class="px-6 py-3 font-bold">Категория</th>
                        <th class="px-6 py-3 font-bold text-right">Сумма</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php $__empty_1 = true; $__currentLoopData = $expensesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4 text-sm font-semibold capitalize" data-label="Категория"><?php echo e($expense->category); ?></td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-rose-400" data-label="Сумма"><?php echo e(number_format($expense->total, 0, '.', ' ')); ?> сом</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center text-muted text-sm italic">Нет расходов</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Sales -->
<div class="glass rounded-2xl overflow-hidden mb-8">
    <div class="px-6 py-5 border-b border-white/5 bg-white/5 flex items-center justify-between">
        <h3 class="text-sm font-bold flex items-center gap-2">
            <i class="bi bi-clock-history text-accent"></i> Последние продажи
        </h3>
        <a href="<?php echo e(route('owner.reports.sales')); ?>" class="text-[11px] font-bold text-accent hover:underline">Смотреть все</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                    <th class="px-6 py-3 font-bold">ID</th>
                    <th class="px-6 py-3 font-bold">Магазин</th>
                    <th class="px-6 py-3 font-bold">Продавец</th>
                    <th class="px-6 py-3 font-bold text-right">Сумма</th>
                    <th class="px-6 py-3 font-bold text-right">Дата</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="ID">
                        <span class="px-2 py-0.5 rounded bg-accent/10 text-accent font-mono text-[10px]">#<?php echo e($sale->id); ?></span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium" data-label="Магазин"><?php echo e($sale->shop?->name ?? 'N/A'); ?></td>
                    <td class="px-6 py-4 text-sm text-muted" data-label="Продавец"><?php echo e($sale->user?->username ?? 'N/A'); ?></td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-emerald-500" data-label="Сумма"><?php echo e(number_format($sale->total_amount, 0, '.', ' ')); ?> сом</td>
                    <td class="px-6 py-4 text-right text-muted text-xs" data-label="Дата"><?php echo e($sale->created_at->format('d.m.Y H:i')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-muted text-sm italic">Нет продаж</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Top Products -->
<div class="glass rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-white/5 bg-white/5 flex items-center justify-between">
        <h3 class="text-sm font-bold flex items-center gap-2">
            <i class="bi bi-trophy text-amber-500"></i> Топ товаров по прибыли
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                    <th class="px-6 py-3 font-bold">Товар</th>
                    <th class="px-6 py-3 font-bold">Артикул</th>
                    <th class="px-6 py-3 font-bold text-center">Продано</th>
                    <th class="px-6 py-3 font-bold text-right">Выручка</th>
                    <th class="px-6 py-3 font-bold text-right">Прибыль</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="Товар">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-white/5 border border-white/10">
                                <?php if($product->photo): ?>
                                    <img src="<?php echo e(asset('storage/' . $product->photo)); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-muted">
                                        <i class="bi bi-box"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="text-sm font-medium leading-tight"><?php echo e(Str::limit($product->name, 40)); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4" data-label="Артикул">
                        <span class="px-2 py-0.5 rounded bg-white/5 text-muted text-[10px]"><?php echo e($product->article); ?></span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm" data-label="Продано"><?php echo e($product->sold_qty); ?></td>
                    <td class="px-6 py-4 text-right text-sm font-medium" data-label="Выручка"><?php echo e(number_format($product->revenue, 0, '.', ' ')); ?> сом</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-emerald-500" data-label="Прибыль"><?php echo e(number_format($product->profit, 0, '.', ' ')); ?> сом</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-muted text-sm italic">Нет данных</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\Modules/Owner\resources/views/dashboard.blade.php ENDPATH**/ ?>