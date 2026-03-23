<?php $__env->startSection('page-title', 'Товары'); ?>
<?php $__env->startSection('content'); ?>
<style>
:root { --accent-green: #10b981; --accent-blue: #3b82f6; --accent-red: #ef4444; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); --card-hover: rgba(255, 255, 255, 0.85); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.1); --glass-bg: rgba(30, 30, 30, 0.8); --glass-border: rgba(255, 255, 255, 0.08); --card-hover: rgba(40, 40, 40, 0.9); --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.data-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--glass-border); overflow: hidden; }
.data-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
.table { width: 100%; }
.table th { padding: 14px 20px; font-size: 12px; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid var(--border-color); }
.table td { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); color: var(--text-main); }
.table tr:hover { background: var(--card-hover); }
.product-thumbnail { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; }
.product-placeholder { width: 48px; height: 48px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-green); }
.btn { padding: 8px 16px; border-radius: 10px; font-weight: 600; transition: all 0.3s; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-primary { background: var(--gradient); color: white; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4); color: white; }
.btn-outline { background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-main); }
.btn-outline:hover { border-color: var(--accent-green); color: var(--accent-green); }
.btn-danger { background: rgba(239, 68, 68, 0.15); color: var(--accent-red); border: 1px solid rgba(239, 68, 68, 0.3); }
.stock-badge { background: rgba(16, 185, 129, 0.15); color: var(--accent-green); padding: 6px 12px; border-radius: 8px; font-size: 12px; }
.stock-badge-danger { background: rgba(239, 68, 68, 0.15); color: var(--accent-red); padding: 6px 12px; border-radius: 8px; font-size: 12px; }
.search-box { display: flex; align-items: center; gap: 8px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; padding: 8px 12px; }
.search-box input { background: transparent; border: none; color: var(--text-main); outline: none; }
small { color: var(--text-muted) !important; }
strong { color: var(--text-main); }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;"><div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--gradient); color: white; font-size: 1.25rem;"><i class="bi bi-box-seam"></i></div><div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">Товары</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Управление ассортиментом</p></div></div>
    <div class="data-card">
        <div class="data-card-header">
            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0;"><i class="bi bi-list-ul me-2"></i>Список товаров</h3>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <form method="GET" class="search-box"><i class="bi bi-search" style="color: var(--text-muted);"></i><input type="text" name="search" placeholder="Поиск товаров..." value="<?php echo e(request('search')); ?>"></form>
                <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i>Добавить</a>
            </div>
        </div>
        <table class="table">
            <thead><tr><th>Фото</th><th>Название</th><th>Артикул</th><th>Цена</th><th>Остаток</th><th>Действия</th></tr></thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php if($product->photo): ?><img src="<?php echo e(asset('storage/' . $product->photo)); ?>" class="product-thumbnail"><?php else: ?><div class="product-placeholder"><i class="bi bi-box"></i></div><?php endif; ?></td>
                    <td><strong><?php echo e(Str::limit($product->name, 50)); ?></strong><br><small><?php echo e($product->category?->name ?? ''); ?></small></td>
                    <td><small><?php echo e($product->article); ?></small></td>
                    <td><strong style="color: var(--accent-green);"><?php echo e(number_format($product->price, 0, '.', ' ')); ?> сом</strong></td>
                    <td><?php if($product->stock_quantity <= 5): ?><span class="stock-badge-danger"><?php echo e($product->stock_quantity); ?> шт</span><?php else: ?><span class="stock-badge"><?php echo e($product->stock_quantity); ?> шт</span><?php endif; ?></td>
                    <td style="display: flex; gap: 8px;">
                        <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" class="btn btn-outline"><i class="bi bi-pencil"></i></a>
                        <form action="<?php echo e(route('admin.products.delete', $product->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Удалить?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">Товары не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/products/index.blade.php ENDPATH**/ ?>