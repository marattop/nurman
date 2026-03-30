<?php $__env->startSection('title', 'Мои купоны - NURMAN'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .coupons-page { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .coupons-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
    .coupon-card {
        background: var(--bg-card);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        transition: 0.2s;
    }
    .coupon-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.2);
        border-color: var(--primary);
    }
    .coupon-header {
        background: linear-gradient(135deg, #8B4513, #A0522D);
        padding: 1.5rem;
        color: white;
        position: relative;
    }
    .coupon-header::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        right: 0;
        height: 20px;
        background: var(--bg-card);
        border-radius: 50% 50% 0 0;
    }
    .coupon-body { padding: 1.5rem; }
    .coupon-code {
        font-family: monospace;
        font-size: 1.2rem;
        color: var(--primary);
        background: var(--bg-secondary);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 1rem;
    }
    .coupon-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .coupon-qr {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        display: inline-block;
        margin: 1rem 0;
    }
    .coupon-qr img { width: 150px; height: 150px; }
    .coupon-meta {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    .status-active { color: var(--success); }
    .status-expired { color: var(--accent-red); }
</style>

<div class="coupons-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🎫 Мои купоны</h1>
        <a href="<?php echo e(route('client.dashboard')); ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> На главную
        </a>
    </div>

    <?php if($activeCoupons->count() > 0): ?>
        <h3 class="mb-3"><i class="bi bi-check-circle me-2"></i>Активные купоны</h3>
        <div class="coupons-grid mb-5">
            <?php $__currentLoopData = $activeCoupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="coupon-card">
                <div class="coupon-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size: 0.8rem; opacity: 0.8;">NURMAN</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">
                                <?php if($coupon->discount_type === 'percent'): ?>
                                    <?php echo e($coupon->discount_value); ?>%
                                <?php elseif($coupon->discount_type === 'wholesale'): ?>
                                    ОПТ
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="client-card-chip" style="width: 40px; height: 30px; background: rgba(255,255,255,0.3); border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="coupon-body">
                    <div class="coupon-code"><?php echo e($coupon->code); ?></div>
                    <div class="coupon-value">
                        <?php if($coupon->discount_type === 'percent'): ?>
                            Скидка <?php echo e($coupon->discount_value); ?>%
                        <?php elseif($coupon->discount_type === 'wholesale'): ?>
                            Оптовая цена
                        <?php endif; ?>
                    </div>
                    
                    <?php
                        $qrData = json_encode(['coupon_id' => $coupon->id, 'code' => $coupon->code]);
                        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrData);
                    ?>
                    
                    <div class="coupon-qr">
                        <img src="<?php echo e($qrCodeUrl); ?>" alt="QR код">
                    </div>
                    
                    <div class="coupon-meta">
                        <?php if($coupon->valid_until): ?>
                            <div><i class="bi bi-calendar"></i> Действует до <?php echo e($coupon->valid_until->format('d.m.Y')); ?></div>
                        <?php else: ?>
                            <div><i class="bi bi-infinity"></i> Бессрочно</div>
                        <?php endif; ?>
                        <div><i class="bi bi-receipt"></i> Использовано: <?php echo e($coupon->usage_count); ?>/<?php echo e($coupon->usage_limit); ?></div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="<?php echo e(route('client.coupons.print', $coupon->id)); ?>" target="_blank" class="btn btn-primary w-100">
                            <i class="bi bi-printer"></i> Печать купона
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <?php if($expiredCoupons->count() > 0): ?>
        <h3 class="mb-3"><i class="bi bi-calendar-x me-2"></i>Истёкшие купоны</h3>
        <div class="coupons-grid">
            <?php $__currentLoopData = $expiredCoupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="coupon-card" style="opacity: 0.6;">
                <div class="coupon-header" style="background: linear-gradient(135deg, #6b7280, #4b5563);">
                    <div style="font-size: 0.8rem; opacity: 0.8;">NURMAN</div>
                    <div style="font-size: 1.5rem; font-weight: 700;">
                        <?php if($coupon->discount_type === 'percent'): ?>
                            <?php echo e($coupon->discount_value); ?>%
                        <?php endif; ?>
                    </div>
                </div>
                <div class="coupon-body">
                    <div class="coupon-code"><?php echo e($coupon->code); ?></div>
                    <div class="status-expired"><i class="bi bi-x-circle"></i> Истёк</div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/client/coupons/index.blade.php ENDPATH**/ ?>