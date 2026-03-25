<?php $__env->startSection('page-title', 'QR-коды'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code"></i></div>
    <div>
        <h1 class="page-title">QR-коды</h1>
        <p class="page-subtitle">Управление QR-кодами товаров</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список товаров</h3>
        <a href="<?php echo e(route('admin.qr.scan')); ?>" class="btn btn-primary"><i class="bi bi-qr-code-scan"></i> Сканировать</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Товар</th>
                    <th>Артикул</th>
                    <th>QR-код</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <?php if($product->photo): ?>
                                <img src="<?php echo e(asset('storage/' . $product->photo)); ?>" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 44px; height: 44px; border-radius: 8px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
                                    <i class="bi bi-box"></i>
                                </div>
                            <?php endif; ?>
                            <span style="font-weight: 500;"><?php echo e(Str::limit($product->name, 40)); ?></span>
                        </div>
                    </td>
                    <td style="color: #94a3b8;"><?php echo e($product->article); ?></td>
                    <td>
                        <?php
                            // В контроллере загружается: variants.qrCodes
                            $qrCount = $product->variants->sum(fn($v) => $v->qrCodes->count());
                        ?>

                        <?php if($qrCount > 0): ?>
                            <span class="badge badge-success"><?php echo e($qrCount); ?> шт</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Нет QR</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo e(route('admin.qr.generate.page', $product->id)); ?>" class="btn btn-outline">
                            <i class="bi bi-plus-lg"></i> Генерировать
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" style="text-align: center; padding: 3rem; color: #64748b;">Товары не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/qr/index.blade.php ENDPATH**/ ?>