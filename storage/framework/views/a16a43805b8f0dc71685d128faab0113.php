<?php $__env->startSection('page-title', 'Инвентаризация'); ?>
<?php $__env->startSection('page-subtitle', 'Сверка фактических остатков с базой данных'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto">
    <!-- Quick Action -->
    <div class="glass p-8 rounded-[2rem] border border-white/5 shadow-2xl text-center mb-8 bg-gradient-to-br from-accent/5 to-transparent">
        <div class="w-20 h-20 rounded-full bg-accent/10 flex items-center justify-center text-accent mx-auto mb-6">
            <i class="bi bi-qr-code-scan text-4xl"></i>
        </div>
        <h3 class="text-2xl font-black text-white mb-2">Быстрая проверка</h3>
        <p class="text-muted text-sm mb-8 max-w-sm mx-auto">Используйте сканер для мгновенной проверки остатков или внесения корректировок</p>
        <a href="<?php echo e(route('warehouse.qrcodes.scan')); ?>" class="btn btn-primary btn-lg w-full sm:w-auto px-12 py-4 rounded-2xl gap-2 font-black shadow-glow">
            <i class="bi bi-camera"></i> ОТКРЫТЬ СКАНЕР
        </a>
    </div>

    <!-- Inventory History/Sessions -->
    <div class="glass rounded-2xl border border-white/5 overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
            <h3 class="font-bold text-white text-sm uppercase tracking-widest flex items-center gap-2">
                <i class="bi bi-history text-muted"></i> История сверок
            </h3>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left responsive-table">
                    <thead>
                        <tr class="bg-white/[0.02] text-[10px] text-muted uppercase font-black">
                            <th class="px-6 py-4">Дата</th>
                            <th class="px-6 py-4">Сотрудник</th>
                            <th class="px-6 py-4">Товары</th>
                            <th class="px-6 py-4 text-right">Результат</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-muted italic">
                                <i class="bi bi-info-circle mb-2 block opacity-20 text-3xl"></i>
                                История пуста. Начните сканирование для первой проверки.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.warehouse', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\Modules\Warehouse\Providers/../resources/views/inventory/index.blade.php ENDPATH**/ ?>