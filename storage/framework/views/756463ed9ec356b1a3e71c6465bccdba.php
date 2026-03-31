<div class="md:hidden fixed bottom-0 left-0 right-0 bg-sidebar border-t border-white/5 px-2 py-2 flex justify-around items-center z-[100] safe-area-bottom">
    <a href="<?php echo e(route('seller.dashboard')); ?>" class="flex flex-col items-center gap-1 px-3 py-1 <?php echo e(request()->routeIs('seller.dashboard') ? 'text-accent' : 'text-muted'); ?>">
        <i class="bi bi-speedometer2 text-xl"></i>
        <span class="text-[10px] font-medium">Главная</span>
    </a>
    <a href="<?php echo e(route('seller.my-sales.index')); ?>" class="flex flex-col items-center gap-1 px-3 py-1 <?php echo e(request()->routeIs('seller.my-sales.*') ? 'text-accent' : 'text-muted'); ?>">
        <i class="bi bi-receipt text-xl"></i>
        <span class="text-[10px] font-medium">История</span>
    </a>
    <a href="<?php echo e(route('seller.pos.index')); ?>" class="flex flex-col items-center gap-1 px-3 py-2 bg-accent/10 rounded-2xl text-accent border border-accent/20 -translate-y-4 shadow-lg shadow-accent/20">
        <i class="bi bi-cart-plus text-2xl"></i>
    </a>
    <a href="<?php echo e(route('seller.customers.index')); ?>" class="flex flex-col items-center gap-1 px-3 py-1 <?php echo e(request()->routeIs('seller.customers.*') ? 'text-accent' : 'text-muted'); ?>">
        <i class="bi bi-people text-xl"></i>
        <span class="text-[10px] font-medium">Клиенты</span>
    </a>
    <a href="<?php echo e(route('seller.profile.index')); ?>" class="flex flex-col items-center gap-1 px-3 py-1 <?php echo e(request()->routeIs('seller.profile.*') ? 'text-accent' : 'text-muted'); ?>">
        <i class="bi bi-person text-xl"></i>
        <span class="text-[10px] font-medium">Профиль</span>
    </a>
</div>

<style>
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .safe-area-bottom {
            padding-bottom: calc(0.5rem + env(safe-area-inset-bottom));
        }
    }
    
    @media (max-width: 768px) {
        body { padding-bottom: 80px !important; }
    }
</style>
<?php /**PATH C:\laragon\www\nurman\Modules\Seller\Providers/../resources/views/layouts/partials/seller-bottom-nav.blade.php ENDPATH**/ ?>