<?php $__env->startSection('page-title', 'Аналитика и Отчёты'); ?>
<?php $__env->startSection('page-subtitle', 'Комплексный анализ деятельности вашего бизнеса'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Sales Report -->
    <a href="<?php echo e(route('owner.reports.sales')); ?>" class="glass p-6 rounded-2xl group hover:bg-white/5 transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-graph-up text-2xl"></i>
            </div>
            <i class="bi bi-arrow-right text-muted group-hover:text-white transition-colors"></i>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">Продажи</h3>
        <p class="text-sm text-muted">Детальный отчёт по продажам, выручке и транзакциям</p>
    </a>

    <!-- Products Report -->
    <a href="<?php echo e(route('owner.reports.products')); ?>" class="glass p-6 rounded-2xl group hover:bg-white/5 transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-box-seam text-2xl"></i>
            </div>
            <i class="bi bi-arrow-right text-muted group-hover:text-white transition-colors"></i>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">Товары</h3>
        <p class="text-sm text-muted">Анализ популярности товаров и складских остатков</p>
    </a>

    <!-- Purchases Report -->
    <a href="<?php echo e(route('owner.reports.purchases')); ?>" class="glass p-6 rounded-2xl group hover:bg-white/5 transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-cart-fill text-2xl"></i>
            </div>
            <i class="bi bi-arrow-right text-muted group-hover:text-white transition-colors"></i>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">Закупки</h3>
        <p class="text-sm text-muted">История поступлений товаров и затрат на закупку</p>
    </a>

    <!-- Profit Report -->
    <a href="<?php echo e(route('owner.reports.profit')); ?>" class="glass p-6 rounded-2xl group hover:bg-white/5 transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-cash-stack text-2xl"></i>
            </div>
            <i class="bi bi-arrow-right text-muted group-hover:text-white transition-colors"></i>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">Прибыль и Маржа</h3>
        <p class="text-sm text-muted">Расчёт чистой прибыли, валовой маржи и рентабельности</p>
    </a>

    <!-- Expenses Report -->
    <a href="<?php echo e(route('owner.reports.expenses')); ?>" class="glass p-6 rounded-2xl group hover:bg-white/5 transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-wallet2 text-2xl"></i>
            </div>
            <i class="bi bi-arrow-right text-muted group-hover:text-white transition-colors"></i>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">Расходы</h3>
        <p class="text-sm text-muted">Классификация и анализ всех операционных расходов</p>
    </a>

    <!-- Finance Report -->
    <a href="<?php echo e(route('owner.reports.finance')); ?>" class="glass p-6 rounded-2xl group hover:bg-white/5 transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-file-earmark-text text-2xl"></i>
            </div>
            <i class="bi bi-arrow-right text-muted group-hover:text-white transition-colors"></i>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">Финансовый итог</h3>
        <p class="text-sm text-muted">Сводный финансовый отчёт за выбранный период</p>
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/owner/reports/index.blade.php ENDPATH**/ ?>