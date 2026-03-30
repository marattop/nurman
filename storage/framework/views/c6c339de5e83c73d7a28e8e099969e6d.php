<?php $__env->startSection('page-title', 'Журнал действий'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-icon"><i class="bi bi-journal-text"></i></div>
    <div>
        <h1 class="page-title">Журнал действий</h1>
        <p class="page-subtitle">История операций</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Последние действия</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Пользователь</th>
                    <th>Действие</th>
                    <th>Объект</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="font-weight: 500;"><?php echo e($activity->user?->full_name ?? '—'); ?></div>
                    </td>
                    <td style="color: #94a3b8;"><?php echo e($activity->description ?? '—'); ?></td>
                    <td style="color: #94a3b8;"><?php echo e($activity->subject_type ?? '—'); ?></td>
                    <td style="color: #94a3b8;"><?php echo e($activity->created_at?->format('d.m.Y H:i') ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" style="text-align: center; padding: 3rem; color: #64748b;">Записей не найдено</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/activity-log.blade.php ENDPATH**/ ?>