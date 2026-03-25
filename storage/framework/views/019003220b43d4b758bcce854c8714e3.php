<?php $__env->startSection('page-title', 'Генерация QR-кодов'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code"></i></div>
    <div>
        <h1 class="page-title">Генерация QR-кодов</h1>
        <p class="page-subtitle"><?php echo e($product->name); ?></p>
    </div>
</div>

<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<div class="card">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-box"></i> <?php echo e($product->name); ?></h3>
        <a href="<?php echo e(route('admin.qr.manage')); ?>" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
    <div class="card-body">
        <p class="text-muted">Артикул: <?php echo e($product->article); ?></p>
        
        <?php if($product->variants->count() > 0): ?>
            <div class="mb-3">
                <h5>Варианты товара:</h5>
                <ul class="list-group">
                    <?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo e($variant->size ?? 'Стандарт'); ?></strong>
                                <span class="text-muted">(<?php echo e($variant->barcode ?? '—'); ?>)</span>
                            </div>
                            <span class="badge <?php echo e($variant->qrCodes->count() > 0 ? 'badge-success' : 'badge-danger'); ?>">
                                <?php echo e($variant->qrCodes->count() > 0 ? 'QR есть' : 'Нет QR'); ?>

                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            
            <form id="generateForm">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-qr-code-scan"></i> Сгенерировать QR-коды
                </button>
            </form>
        <?php else: ?>
            <p class="text-warning">У этого товара нет вариантов</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Генерация...';
    
    try {
        const response = await fetch('<?php echo e(route('admin.qr.generate')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: <?php echo e($product->id); ?> })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('QR-коды успешно сгенерированы!');
            window.location.href = '<?php echo e(route('admin.qr.manage')); ?>';
        } else {
            alert('Ошибка: ' + result.message);
        }
    } catch (error) {
        alert('Ошибка: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-qr-code-scan"></i> Сгенерировать QR-коды';
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/qr/generate.blade.php ENDPATH**/ ?>