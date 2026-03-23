<?php $__env->startSection('page-title', 'Клиенты'); ?>
<?php $__env->startSection('content'); ?>
<style>
:root { --accent-green: #10b981; --accent-blue: #3b82f6; --accent-orange: #f59e0b; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); --card-hover: rgba(255, 255, 255, 0.85); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.1); --glass-bg: rgba(30, 30, 30, 0.8); --glass-border: rgba(255, 255, 255, 0.08); --card-hover: rgba(40, 40, 40, 0.9); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.data-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--glass-border); }
.data-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; }
.table th { padding: 14px 20px; font-size: 12px; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid var(--border-color); }
.table td { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); color: var(--text-main); }
.table tr:hover { background: var(--card-hover); }
.badge { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 12px; }
.badge-gold { background: rgba(245, 158, 11, 0.15); color: var(--accent-orange); }
.btn-outline { padding: 8px 16px; border-radius: 10px; font-weight: 600; background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-main); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-outline:hover { border-color: var(--accent-green); color: var(--accent-green); }
small { color: var(--text-muted) !important; }
strong { color: var(--text-main); }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;"><div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--gradient); color: white; font-size: 1.25rem;"><i class="bi bi-people"></i></div><div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">Клиенты</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Управление клиентами</p></div></div>
    <div class="data-card">
        <div class="data-card-header">
            <span style="font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0;"><i class="bi bi-list-ul me-2"></i>Список клиентов</span>
            <button class="btn-outline"><i class="bi bi-plus-lg"></i>Добавить</button>
        </div>
        <table class="table">
            <thead><tr><th>Имя</th><th>Телефон</th><th>Баланс</th><th>Статус</th><th>Действия</th></tr></thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($customer->full_name ?? '—'); ?></strong></td>
                    <td><small><?php echo e($customer->phone ?? '—'); ?></small></td>
                    <td><strong style="color: var(--accent-green);"><?php echo e(number_format($customer->balance ?? 0, 2, '.', ' ')); ?> сом</strong></td>
                    <td><span class="badge badge-gold"><i class="bi bi-star"></i> Обычный</span></td>
                    <td><button class="btn-outline"><i class="bi bi-pencil"></i></button></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">Клиенты не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/customers/index.blade.php ENDPATH**/ ?>