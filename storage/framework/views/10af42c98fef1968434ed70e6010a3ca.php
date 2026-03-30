<?php $__env->startSection('code', '500'); ?>
<?php $__env->startSection('message', 'Внутренняя ошибка'); ?>
<?php $__env->startSection('description', 'Что-то пошло не на наших серверах. Попробуйте обновить страницу.'); ?>

<?php echo $__env->make('errors.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/errors/500.blade.php ENDPATH**/ ?>