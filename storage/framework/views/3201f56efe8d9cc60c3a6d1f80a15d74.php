<?php $__env->startSection('title', 'История продаж - Продавец'); ?>

<?php $__env->startSection('content'); ?>
<style>
    :root {
        --pos-header-height: 60px;
    }

    .history-container {
        padding: 1.5rem;
        background: var(--bg-secondary);
        min-height: calc(100vh - var(--pos-header-height));
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .history-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid var(--border-color);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--primary);
    }

    .stat-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .filters-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }

    .filters-form {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 600;
    }

    .filter-input {
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-card);
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--primary);
    }

    .btn-filter {
        padding: 0.75rem 1.5rem;
        background: var(--gradient);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .sales-table {
        background: var(--bg-card);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .sales-table-header {
        padding: 1rem 1.5rem;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
        font-weight: 700;
        color: var(--text-primary);
    }

    .sales-table-row {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr 1fr 1fr 1fr;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        align-items: center;
        transition: 0.2s;
    }

    .sales-table-row:last-child {
        border-bottom: none;
    }

    .sales-table-row:hover {
        background: var(--bg-secondary);
    }

    .sales-table-cell {
        color: var(--text-primary);
    }

    .sales-table-cell.muted {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.success {
        background: rgba(16, 185, 129, 0.15);
        color: var(--success);
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.85rem;
        color: var(--text-primary);
    }

    .btn-view {
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-view:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        text-decoration: none;
        transition: 0.2s;
    }

    .pagination a:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .pagination .active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .sales-table-row {
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.5rem;
        }
    }
</style>

<div class="history-container">
    <div class="history-header">
        <h1 class="history-title">
            История продаж
        </h1>
        <a href="<?php echo e(route('seller.pos.index')); ?>" class="btn-view">
            Назад в POS
        </a>
    </div>

    <!-- Статистика -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Продаж сегодня</div>
            <div class="stat-value"><?php echo e($stats['today_count']); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Сумма сегодня</div>
            <div class="stat-value"><?php echo e(number_format($stats['today'], 0, ',', ' ')); ?> с</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Всего продаж</div>
            <div class="stat-value"><?php echo e(number_format($stats['total'], 0, ',', ' ')); ?> с</div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="filters-card">
        <form class="filters-form" method="GET" action="<?php echo e(route('seller.my-sales.index')); ?>">
            <div class="filter-group">
                <label class="filter-label">С даты</label>
                <input type="date" name="date_from" class="filter-input" value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">По дату</label>
                <input type="date" name="date_to" class="filter-input" value="<?php echo e(request('date_to')); ?>">
            </div>
            <button type="submit" class="btn-filter">
                Фильтр
            </button>
            <?php if(request('date_from') || request('date_to')): ?>
                <a href="<?php echo e(route('seller.my-sales.index')); ?>" class="btn-filter" style="background: var(--bg-secondary);">
                    Сброс
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Таблица продаж -->
    <div class="sales-table">
        <div class="sales-table-header">
            <div class="sales-table-row" style="border-bottom: none;">
                <div>№</div>
                <div>Товары</div>
                <div>Клиент</div>
                <div>Сумма</div>
                <div>Оплата</div>
                <div>Действие</div>
            </div>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="sales-table-row">
                <div class="sales-table-cell muted">
                    <div style="font-weight: 600;"><?php echo e($sale->transaction_number); ?></div>
                    <div style="font-size: 0.75rem;"><?php echo e($sale->created_at->format('d.m.Y H:i')); ?></div>
                </div>
                <div class="sales-table-cell">
                    <?php
                        $itemsCount = $sale->items->count();
                        $itemsSummary = $sale->items->take(2)->map(function($item) {
                            return $item->variant->product->name ?? 'Товар';
                        })->join(', ');
                    ?>
                    <?php echo e($itemsSummary); ?>

                    <?php if($itemsCount > 2): ?>
                        <span style="color: var(--text-secondary);">+<?php echo e($itemsCount - 2); ?> ещё</span>
                    <?php endif; ?>
                </div>
                <div class="sales-table-cell">
                    <?php if($sale->customer): ?>
                        <div><?php echo e($sale->customer->full_name ?? 'Клиент'); ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);"><?php echo e($sale->customer->phone ?? ''); ?></div>
                    <?php else: ?>
                        <span style="color: var(--text-secondary);">Без клиента</span>
                    <?php endif; ?>
                </div>
                <div class="sales-table-cell" style="font-weight: 700; color: var(--primary);">
                    <?php echo e(number_format($sale->total_amount, 0, ',', ' ')); ?> с
                </div>
                <div class="sales-table-cell">
                    <span class="payment-badge">
                        <?php if($sale->payment_method === 'cash'): ?>
                            Наличные
                        <?php elseif($sale->payment_method === 'card'): ?>
                            Карта
                        <?php elseif($sale->payment_method === 'transfer'): ?>
                            Перевод
                        <?php endif; ?>
                    </span>
                </div>
                <div class="sales-table-cell">
                    <button class="btn-view" onclick="viewSale(<?php echo e($sale->id); ?>)">
                        Просмотр
                    </button>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-box"></i>
                </div>
                <p>Продажей пока нет</p>
                <small>Оформите первую продажу через POS-терминал</small>
            </div>
        <?php endif; ?>
    </div>

    <!-- Пагинация -->
    <?php if($sales->hasPages()): ?>
        <div class="pagination">
            <?php echo e($sales->links()); ?>

        </div>
    <?php endif; ?>
</div>

<script>
function viewSale(id) {
    const baseUrl = window.location.origin;
    window.open(baseUrl + '/seller/pos/receipt/' + id + '?print=1', '_blank');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/seller/pos/history.blade.php ENDPATH**/ ?>