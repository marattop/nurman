<?php $__env->startSection('title', 'Продавцы'); ?>
<?php $__env->startSection('content'); ?>
<style>
:root { --accent-primary: #10b981; --accent-blue: #3b82f6; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.08); --glass-bg: rgba(20, 20, 20, 0.85); --glass-border: rgba(255, 255, 255, 0.1); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.data-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--glass-border); }
.data-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); font-weight: 700; color: var(--text-main); }
.table th { padding: 14px 20px; font-size: 12px; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
.table td { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); color: var(--text-main); }
.btn { padding: 8px 16px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
.btn-outline { background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-main); }
small { color: var(--text-muted) !important; }
strong { color: var(--text-main); }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-size: 1.25rem;"><i class="bi bi-person-badge"></i></div>
        <div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">Продавцы</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Привязка к магазинам</p></div>
    </div>
    <div class="data-card">
        <div class="data-card-header"><i class="bi bi-list-ul me-2"></i>Список продавцов</div>
        <table class="table">
            <thead><tr><th>Имя</th><th>Магазины</th><th>Действия</th></tr></thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($seller->full_name ?? $seller->username); ?></strong></td>
                    <td><small><?php echo e($seller->shop?->name ?? 'Не назначен'); ?></small></td>
                    <td><a href="<?php echo e(route('admin.sellers.settings', $seller->id)); ?>" class="btn btn-outline"><i class="bi bi-gear"></i>Настройки</a></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="3" style="text-align: center; padding: 3rem; color: var(--text-muted);">Продавцы не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/sellers/shops.blade.php ENDPATH**/ ?>