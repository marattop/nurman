<?php $__env->startSection('page-title', 'Дашборд'); ?>
<?php $__env->startSection('content'); ?>
<style>
:root { --accent-green: #10b981; --accent-blue: #3b82f6; --accent-red: #ef4444; --accent-orange: #f59e0b; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.12); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --text-secondary: #636366; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); --card-hover: rgba(255, 255, 255, 0.85); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --text-secondary: rgba(255, 255, 255, 0.5); --border-color: rgba(255, 255, 255, 0.1); --glass-bg: rgba(30, 30, 30, 0.8); --glass-border: rgba(255, 255, 255, 0.08); --card-hover: rgba(40, 40, 40, 0.9); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.dashboard-header, .stat-card, .data-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); }
.dashboard-header { padding: 40px; margin-bottom: 32px; box-shadow: var(--shadow-lg); }
.dashboard-header h2 { font-size: 32px; font-weight: 700; color: var(--text-main); margin-bottom: 8px; }
.dashboard-header p { font-size: 15px; color: var(--text-muted); }
.stat-card { padding: 1.5rem; box-shadow: var(--shadow-md); transition: all 0.3s; border-left: 4px solid var(--accent-green); height: 100%; }
.stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); background: var(--card-hover); }
.stat-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--gradient); color: white; font-size: 1.5rem; }
.stat-label { color: var(--text-secondary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; }
.stat-value { font-size: 2rem; font-weight: 800; color: var(--accent-green); line-height: 1; }
.stat-subtitle { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem; display: flex; align-items: center; gap: 0.25rem; }
.data-card { box-shadow: var(--shadow-md); overflow: hidden; }
.data-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); font-weight: 700; color: var(--text-main); background: rgba(16, 185, 129, 0.08); }
.table th { padding: 14px 20px; font-size: 12px; color: var(--text-secondary); text-transform: uppercase; border-bottom: 1px solid var(--border-color); }
.table td { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); color: var(--text-main); }
.table tr:hover { background: var(--card-hover); }
.product-thumbnail { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; }
.product-placeholder { width: 48px; height: 48px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-green); }
.stock-badge { background: rgba(16, 185, 129, 0.15); color: var(--accent-green); padding: 6px 12px; border-radius: 8px; font-size: 12px; }
.stock-badge-danger { background: rgba(239, 68, 68, 0.15); color: var(--accent-red); padding: 6px 12px; border-radius: 8px; font-size: 12px; }
.empty-state { text-align: center; padding: 3rem; color: var(--text-muted); }
.empty-state-icon { font-size: 3rem; opacity: 0.4; margin-bottom: 1rem; }
small { color: var(--text-muted) !important; }
strong { color: var(--text-main); }
</style>
<div class="container-fluid py-4">
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div><h2 class="mb-2"><i class="bi bi-speedometer2 me-2"></i>Дашборд</h2><p class="mb-0">Панель администратора</p></div>
            <div class="d-flex gap-3"><div class="mini-stat"><div class="mini-stat-value" style="color: var(--text-main);"><?php echo e($stats['shops_count'] ?? 0); ?></div><div class="mini-stat-label" style="color: var(--text-muted);">Магазинов</div></div><div class="mini-stat"><div class="mini-stat-value" style="color: var(--text-main);"><?php echo e($stats['active_users'] ?? 0); ?></div><div class="mini-stat-label" style="color: var(--text-muted);">Сотрудников</div></div></div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card"><div class="d-flex align-items-start justify-content-between"><div><div class="stat-label">Товаров</div><div class="stat-value"><?php echo e($stats['products_count'] ?? 0); ?></div><div class="stat-subtitle"><i class="bi bi-box-seam"></i>Активных: <?php echo e($stats['products_active'] ?? 0); ?></div></div><div class="stat-icon"><i class="bi bi-box"></i></div></div></div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card"><div class="d-flex align-items-start justify-content-between"><div><div class="stat-label">Продаж сегодня</div><div class="stat-value"><?php echo e($stats['today_sales'] ?? 0); ?></div><div class="stat-subtitle"><i class="bi bi-cash"></i><?php echo e(number_format($stats['today_revenue'] ?? 0, 0, '.', ' ')); ?> сом</div></div><div class="stat-icon"><i class="bi bi-cart"></i></div></div></div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card"><div class="d-flex align-items-start justify-content-between"><div><div class="stat-label">Сотрудников</div><div class="stat-value"><?php echo e($stats['active_users'] ?? 0); ?></div><div class="stat-subtitle"><i class="bi bi-people"></i>Всего: <?php echo e($stats['users_count'] ?? 0); ?></div></div><div class="stat-icon"><i class="bi bi-person-badge"></i></div></div></div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card"><div class="d-flex align-items-start justify-content-between"><div><div class="stat-label">Магазинов</div><div class="stat-value"><?php echo e($stats['shops_count'] ?? 0); ?></div><div class="stat-subtitle"><i class="bi bi-shop"></i>Работает</div></div><div class="stat-icon"><i class="bi bi-shop"></i></div></div></div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="data-card">
                <div class="data-card-header"><i class="bi bi-clock-history me-2"></i>Последние товары</div>
                <table class="table"><thead><tr><th>Товар</th><th>Артикул</th><th>Категория</th><th>Дата</th></tr></thead><tbody><?php $__empty_1 = true; $__currentLoopData = $recentProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><div class="d-flex align-items-center gap-2"><?php if($product->photo): ?><img src="<?php echo e(asset('storage/' . $product->photo)); ?>" class="product-thumbnail"><?php else: ?><div class="product-placeholder"><i class="bi bi-box"></i></div><?php endif; ?><strong><?php echo e(Str::limit($product->name, 40)); ?></strong></div></td><td><small><?php echo e($product->article); ?></small></td><td><small><?php echo e($product->category?->name ?? '—'); ?></small></td><td><small><?php echo e($product->created_at->format('d.m H:i')); ?></small></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="4" class="text-center"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-box"></i></div><p>Нет новых товаров</p></div></td></tr><?php endif; ?></tbody></table>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="data-card">
                <div class="data-card-header"><i class="bi bi-exclamation-triangle me-2"></i>Заканчиваются</div>
                <table class="table"><thead><tr><th>Товар</th><th>Остаток</th></tr></thead><tbody><?php $__empty_1 = true; $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><strong><?php echo e(Str::limit($product->name, 35)); ?></strong><br><small><?php echo e($product->article); ?></small></td><td><?php if($product->quantity <= 5): ?><span class="stock-badge-danger"><?php echo e($product->quantity); ?> шт</span><?php else: ?><span class="stock-badge"><?php echo e($product->quantity); ?> шт</span><?php endif; ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="2" class="text-center"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-check-circle"></i></div><p>Все товары в наличии</p></div></td></tr><?php endif; ?></tbody></table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>