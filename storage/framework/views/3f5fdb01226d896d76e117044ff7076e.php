<?php $__env->startSection('code', '404'); ?>
<?php $__env->startSection('message', 'Страница не найдена'); ?>
<?php $__env->startSection('description', 'Страница, которую вы ищете, не существует или была перемещена.'); ?>

<?php echo $__env->make('errors.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/errors/404.blade.php ENDPATH**/ ?>