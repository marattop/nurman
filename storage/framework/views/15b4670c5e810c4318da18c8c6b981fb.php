<?php $__env->startSection('page-title','Продавец'); ?>
<?php $__env->startSection('content'); ?>
<style>
.stats-card{background:var(--bg-card);border-radius:16px;padding:1.5rem;box-shadow:var(--card-shadow);transition:all 0.3s ease;border:1px solid var(--border-color);height:100%;display:flex;align-items:center;gap:1rem}
.stats-card:hover{transform:translateY(-4px);box-shadow:var(--card-shadow-hover)}
.stats-card-icon{width:64px;height:64px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:var(--gradient-warm);color:white;font-size:1.75rem;box-shadow:0 4px 16px rgba(16,185,129,0.35);flex-shrink:0}
.stats-card h4{font-size:1.5rem;font-weight:700;margin:0;color:var(--text-primary)}
.stats-card p{font-size:0.875rem;color:var(--text-secondary);margin:0.25rem 0 0 0}
@media(max-width: 768px){.stats-card{padding:1.25rem}.stats-card-icon{width:52px;height:52px;font-size:1.4rem}.stats-card h4{font-size:1.25rem}.stats-card p{font-size:0.8rem}}
@media(max-width: 576px){.stats-card{padding:1rem}.stats-card-icon{width:46px;height:46px;font-size:1.2rem}.stats-card h4{font-size:1.1rem}.stats-card p{font-size:0.75rem}.col-12,.col-12.col-md-6,.col-12.col-md-4,.col-12.col-md-3{flex:0 0 100%;max-width:100%}}
</style>
<div class="row g-3">
    <div class="col-12 col-md-6">
        <a href="<?php echo e(route('seller.pos.index')); ?>" style="text-decoration:none">
            <div class="stats-card" style="background:linear-gradient(135deg,var(--accent-primary),var(--accent-primary-dark));cursor:pointer">
                <div class="stats-card-icon"><i class="bi bi-cart3"></i></div>
                <div>
                    <h4 style="color:white">POS-терминал</h4>
                    <p style="color:rgba(255,255,255,0.9)">Оформление продаж</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-3">
        <div class="stats-card">
            <div class="stats-card-icon"><i class="bi bi-cart"></i></div>
            <div><h4><?php echo e($todayCount); ?></h4><p>Продаж сегодня</p></div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="stats-card">
            <div class="stats-card-icon" style="background:linear-gradient(135deg,#28a745,#218838)"><i class="bi bi-cash"></i></div>
            <div><h4><?php echo e(number_format($todaySales,0,'.',' ')); ?> сом</h4><p>Выручка сегодня</p></div>
        </div>
    </div>
</div>
<div class="card mt-3">
    <div class="card-header"><i class="bi bi-receipt me-2"></i>Последние продажи</div>
    <div class="card-body p-2">
        <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="list-group-item d-flex justify-content-between align-items-center py-2" style="border:none;border-bottom:1px solid var(--border-color)">
            <div>
                <strong><?php echo e(Str::limit($sale->transaction_number, 15)); ?></strong><br>
                <small style="color:var(--text-muted)"><?php echo e($sale->created_at->format('d.m H:i')); ?></small>
            </div>
            <strong style="color:var(--accent-primary)"><?php echo e(number_format($sale->total_amount,0,'.',' ')); ?> сом</strong>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-center text-muted py-4">Нет продаж</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/seller/dashboard.blade.php ENDPATH**/ ?>