<?php $__env->startSection('page-title', 'QR-коды'); ?>
<?php $__env->startSection('content'); ?>
<style>
:root { --accent-green: #10b981; --accent-blue: #3b82f6; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); --card-hover: rgba(255, 255, 255, 0.85); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.1); --glass-bg: rgba(30, 30, 30, 0.8); --glass-border: rgba(255, 255, 255, 0.08); --card-hover: rgba(40, 40, 40, 0.9); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.data-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--glass-border); }
.data-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; }
.table th { padding: 14px 20px; font-size: 12px; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid var(--border-color); }
.table td { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); color: var(--text-main); }
.btn { padding: 8px 16px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-primary { background: var(--gradient); color: white; }
.btn-outline { background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-main); }
.product-article code { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); padding: 2px 6px; border-radius: 4px; }
small { color: var(--text-muted) !important; }
strong { color: var(--text-main); }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;"><div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--gradient); color: white; font-size: 1.25rem;"><i class="bi bi-qr-code"></i></div><div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">QR-коды</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Управление QR-кодами товаров</p></div></div>
    <div class="data-card">
        <div class="data-card-header">
            <span style="font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0;"><i class="bi bi-list-ul me-2"></i>Список товаров</span>
            <a href="<?php echo e(route('admin.qr.scan')); ?>" class="btn btn-primary"><i class="bi bi-qr-code-scan"></i>Сканировать</a>
        </div>
        <table class="table">
            <thead><tr><th>Товар</th><th>Артикул</th><th>QR-код</th><th>Действия</th></tr></thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e(Str::limit($product->name, 50)); ?></strong></td>
                    <td><div class="product-article">Артикул: <code><?php echo e($product->article); ?></code></div></td>
                    <td><?php if($product->qr_code): ?><i class="bi bi-check-circle" style="color: var(--accent-green);"></i> Сгенерирован<?php else: ?><i class="bi bi-x-circle" style="color: var(--accent-red);"></i> Нет<?php endif; ?></td>
                    <td><button class="btn btn-outline"><i class="bi bi-printer"></i>Печать</button></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-muted);">Товары не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/qr/index.blade.php ENDPATH**/ ?>