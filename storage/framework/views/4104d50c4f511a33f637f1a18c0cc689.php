<?php $__env->startSection('page-title', 'Закупки товаров'); ?>
<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
<div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
    <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<div class="page-header">
    <div class="page-icon"><i class="bi bi-cart-plus"></i></div>
    <div>
        <h1 class="page-title">Закупки товаров</h1>
        <p class="page-subtitle">Управление закупками товаров</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0 sm:ml-auto w-full sm:w-auto">
        <a href="<?php echo e(route('admin.purchases.create')); ?>" class="btn btn-primary justify-center">
            <i class="bi bi-plus-lg"></i> Создать закупку
        </a>
    </div>
</div>


<div class="card" style="padding: 16px 20px; margin-bottom: 24px;">
    <form action="<?php echo e(route('admin.purchases.index')); ?>" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="w-full sm:flex-1">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">С даты</label>
            <input type="date" name="date_from" class="input" value="<?php echo e(request('date_from')); ?>">
        </div>
        <div class="w-full sm:flex-1">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">По дату</label>
            <input type="date" name="date_to" class="input" value="<?php echo e(request('date_to')); ?>">
        </div>
        <button type="submit" class="btn btn-primary w-full sm:w-auto justify-center">
            <i class="bi bi-search"></i> Фильтр
        </button>
    </form>
</div>


<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список закупок</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Дата</th>
                    <th style="padding: 12px 16px;">№</th>
                    <th style="text-align: left; padding: 12px 16px;">Товар</th>
                    <th style="text-align: center; padding: 12px 16px;">Размер</th>
                    <th style="text-align: center; padding: 12px 16px;">Кол-во</th>
                    <th style="text-align: right; padding: 12px 16px;">Цена закупки</th>
                    <th style="text-align: right; padding: 12px 16px;">Сумма</th>
                    <th style="text-align: left; padding: 12px 16px;">Место</th>
                    <th style="text-align: left; padding: 12px 16px;">Принял</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="Дата" style="padding: 12px 16px;">
                        <div style="font-weight: 500;"><?php echo e($purchase->purchase_date->format('d.m.Y')); ?></div>
                    </td>
                    <td data-label="№" style="padding: 12px 16px; text-align: center; color: #94a3b8;"><?php echo e($purchase->invoice_number); ?></td>
                    <td data-label="Товар" style="padding: 12px 16px;">
                        <div style="font-weight: 500;"><?php echo e($purchase->variant->product->name ?? '—'); ?></div>
                    </td>
                    <td data-label="Размер" style="padding: 12px 16px; text-align: center;">
                        <span class="badge badge-blue"><?php echo e($purchase->variant->size ?? '—'); ?></span>
                    </td>
                    <td data-label="Кол-во" style="padding: 12px 16px; text-align: center;">
                        <span style="font-weight: 600;"><?php echo e($purchase->quantity); ?> шт</span>
                    </td>
                    <td data-label="Цена закупки" style="padding: 12px 16px; text-align: right; color: #94a3b8;">
                        <?php echo e(number_format($purchase->supplier_price, 2, '.', ' ')); ?> сом
                    </td>
                    <td data-label="Сумма" style="padding: 12px 16px; text-align: right; color: #10b981; font-weight: 600;">
                        <?php echo e(number_format($purchase->total_amount, 2, '.', ' ')); ?> сом
                    </td>
                    <td data-label="Место" style="padding: 12px 16px;">
                        <?php if($purchase->shop_id): ?>
                            <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                <i class="bi bi-shop"></i> <?php echo e($purchase->shop->name ?? 'Магазин'); ?>

                            </span>
                        <?php elseif($purchase->warehouse_id): ?>
                            <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                                <i class="bi bi-building"></i> <?php echo e($purchase->warehouse->name ?? 'Склад'); ?>

                            </span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Принял" style="padding: 12px 16px; color: #94a3b8;">
                        <?php echo e($purchase->receivedBy->username ?? '—'); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                        Нет закупок
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="padding: 16px; border-top: 1px solid rgba(148, 163, 184, 0.1);">
        <?php echo e($purchases->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/purchases/index.blade.php ENDPATH**/ ?>