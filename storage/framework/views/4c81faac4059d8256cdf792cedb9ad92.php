<?php $__env->startSection('title', 'Настройки'); ?>
<?php $__env->startSection('content'); ?>
<style>
:root { --accent-primary: #10b981; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.08); --glass-bg: rgba(20, 20, 20, 0.85); --glass-border: rgba(255, 255, 255, 0.1); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.settings-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--glass-border); padding: 2rem; }
.form-group { margin-bottom: 1.5rem; }
.form-label { display: block; font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; }
.form-control { width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--glass-border); background: var(--glass-bg); color: var(--text-main); }
.btn { padding: 10px 20px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; }
.btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-size: 1.25rem;"><i class="bi bi-gear"></i></div>
        <div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">Настройки</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Конфигурация системы</p></div>
    </div>
    <div class="settings-card">
        <form method="POST" action="<?php echo e(route('admin.settings.save')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="form-label">Название компании</label>
                <input type="text" name="company_name" class="form-control" value="<?php echo e($settings['company_name'] ?? 'NURMAN'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Валюта</label>
                <input type="text" name="currency" class="form-control" value="<?php echo e($settings['currency'] ?? 'сом'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Язык</label>
                <select name="language" class="form-control">
                    <option value="ru" <?php echo e(($settings['language'] ?? 'ru') === 'ru' ? 'selected' : ''); ?>>Русский</option>
                    <option value="kg" <?php echo e(($settings['language'] ?? 'ru') === 'kg' ? 'selected' : ''); ?>>Кыргызский</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Сохранить</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/settings.blade.php ENDPATH**/ ?>