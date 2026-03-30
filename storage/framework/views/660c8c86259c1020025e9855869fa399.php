<?php $__env->startSection('page-title', 'Списание'); ?>
<?php $__env->startSection('content'); ?>
<div class="text-center py-20">
    <i class="bi bi-trash text-6xl text-muted/30 mb-4"></i>
    <p class="text-muted">Списание товаров</p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.warehouse', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/warehouse/writeoff/index.blade.php ENDPATH**/ ?>