<?php $__env->startSection('title', 'История покупок - NURMAN'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .purchases-page { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .filters-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }
    .purchases-table { width: 100%; border-collapse: collapse; }
    .purchases-table th, .purchases-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }
    .purchases-table th {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    .purchases-table tr:hover { background: var(--bg-secondary); }
    .btn-view { color: var(--primary); text-decoration: none; }
    .btn-view:hover { text-decoration: underline; }
</style>

<div class="purchases-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🛒 История покупок</h1>
        <a href="<?php echo e(route('client.dashboard')); ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> На главную
        </a>
    </div>

    <!-- Фильтры -->
    <div class="filters-card">
        <form method="GET" action="<?php echo e(route('client.purchases.index')); ?>">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">С даты</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">По дату</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Магазин</label>
                    <select name="shop_id" class="form-select">
                        <option value="">Все магазины</option>
                        <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($shop->id); ?>" <?php echo e(request('shop_id') == $shop->id ? 'selected' : ''); ?>>
                                <?php echo e($shop->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Фильтр
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Таблица -->
    <?php if($purchases->count() > 0): ?>
        <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="table-responsive">
                <table class="purchases-table">
                    <thead>
                        <tr>
                            <th>№ заказа</th>
                            <th>Дата</th>
                            <th>Магазин</th>
                            <th>Товаров</th>
                            <th>Сумма</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('client.purchases.show', $purchase->id)); ?>" class="btn-view">
                                    <?php echo e($purchase->transaction_number); ?>

                                </a>
                            </td>
                            <td><?php echo e($purchase->created_at->format('d.m.Y H:i')); ?></td>
                            <td><?php echo e($purchase->shop?->name ?? '—'); ?></td>
                            <td><?php echo e($purchase->items->count()); ?></td>
                            <td style="font-weight: 600; color: var(--primary);">
                                <?php echo e(number_format($purchase->total_amount, 0, ',', ' ')); ?> с
                            </td>
                            <td>
                                <a href="<?php echo e(route('client.purchases.show', $purchase->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Подробнее
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php echo e($purchases->links()); ?>

    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Покупок не найдено
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/client/purchases/index.blade.php ENDPATH**/ ?>