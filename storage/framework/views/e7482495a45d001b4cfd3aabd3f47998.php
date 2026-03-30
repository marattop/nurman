<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'NURMAN'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#1e293b',
                        sidebar: '#0f172a',
                        card: '#1e293b',
                        accent: '#38bdf8',
                        'accent-hover': '#0ea5e9',
                        text: '#f1f5f9',
                        muted: '#94a3b8',
                        border: 'rgba(255, 255, 255, 0.05)',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #1e293b; color: #f1f5f9; padding-bottom: 0; }
        .sidebar { width: 260px; height: 100vh; position: fixed; left: 0; top: 0; background: #0f172a; border-right: 1px solid rgba(255,255,255,0.05); overflow-y: auto; z-index: 100; }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .menu-item { display: flex; align-items: center; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 10px; margin: 4px 8px; transition: 0.2s; }
        .menu-item:hover { background: rgba(255,255,255,0.03); color: #f1f5f9; }
        .menu-item.active { background: rgba(56,189,248,0.1); color: #38bdf8; }
        .menu-item i { width: 22px; margin-right: 12px; font-size: 18px; }
        .group-title { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; padding: 16px 16px 8px; margin-top: 8px; }
        .logo { padding: 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .logo h2 { margin: 0; font-size: 22px; font-weight: 700; color: #f1f5f9; letter-spacing: -0.5px; }
        .logo span { color: #38bdf8; }
        .card { background: #1e293b; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 24px; }
        .table th { color: #64748b; font-size: 12px; text-transform: uppercase; padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .table td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #f1f5f9; }
        .table tr:hover { background: rgba(255,255,255,0.02); }
        .btn { padding: 10px 20px; border-radius: 10px; font-weight: 500; border: none; cursor: pointer; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary { background: #38bdf8; color: #0f172a; }
        .btn-primary:hover { background: #0ea5e9; }
        .btn-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }
        .btn-outline { background: rgba(56,189,248,0.1); color: #38bdf8; border: 1px solid rgba(56,189,248,0.2); }
        .btn-outline:hover { background: rgba(56,189,248,0.15); }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        .badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
        .badge-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .badge-blue { background: rgba(56,189,248,0.15); color: #38bdf8; }
        .input { background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 14px; color: #f1f5f9; width: 100%; box-sizing: border-box; }
        .input:focus { outline: none; border-color: #38bdf8; }
        .page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .page-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 24px; }
        .page-title { font-size: 28px; font-weight: 700; margin: 0; }
        .page-subtitle { font-size: 14px; color: #94a3b8; margin: 4px 0 0 0; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .card-header-title { font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px; }
        
        /* Bottom Navigation for Mobile */
        .bottom-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; height: 64px; background: #0f172a; border-top: 1px solid rgba(255,255,255,0.05); z-index: 1000; padding: 0 10px; }
        .bottom-nav-item { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-decoration: none; font-size: 11px; gap: 4px; transition: 0.2s; }
        .bottom-nav-item i { font-size: 20px; }
        .bottom-nav-item.active { color: #38bdf8; }
        .bottom-nav-center { width: 56px; height: 56px; background: #38bdf8; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0f172a; font-size: 24px; margin-top: -24px; border: 5px solid #1e293b; box-shadow: 0 4px 12px rgba(56,189,248,0.3); }

        /* Modal styles */
        .modal { display: none; position: fixed; inset: 0; z-index: 1000; overflow-y: auto; }
        .modal.show { display: block; }
        .modal-dialog { margin: 1.75rem auto; max-width: 500px; }
        .modal-dialog.modal-lg { max-width: 800px; }
        .modal-dialog.modal-dialog-centered { display: flex; align-items: center; min-height: calc(100% - 3.5rem); }
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; }
        .modal-content { background: #1e293b; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; margin: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding: 16px 20px; }
        .modal-body { padding: 20px; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid rgba(255,255,255,0.05); padding: 16px 20px; }
        .modal-title { color: #f1f5f9; font-size: 18px; font-weight: 600; margin: 0; }
        .btn-close { background: transparent; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; padding: 0; line-height: 1; }
        .btn-close:hover { color: #f1f5f9; }
        
        @media (max-width: 768px) {
            body { padding-bottom: 64px; }
            .sidebar { transform: translateX(-100%); transition: 0.3s; z-index: 1001; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-toggle { display: flex !important; z-index: 1002; }
            .bottom-nav { display: flex; z-index: 1000; }
            .desktop-only { display: none !important; }
            #overlay { z-index: 1000 !important; }
            .topbar { padding: 12px 16px !important; }
            
            /* Скрываем текст имени на мобильном, показываем только аватар */
            .user-info-name { display: none !important; }
            .user-info-role { display: none !important; }
            .user-avatar { margin-right: 0 !important; }
        }
        .mobile-toggle { display: none; }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="<?php echo $__env->yieldContent('body-class'); ?>">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <h2>NURMAN <span>ERP</span></h2>
        </div>
        <nav>
            <?php if(auth()->user()->isAdmin()): ?>
                <div class="group-title">Главное</div>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <i class="bi bi-speedometer2"></i> Дашборд
                </a>
                
                <div class="group-title">Товары</div>
                <a href="<?php echo e(route('admin.products.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.products.*') ? 'active' : ''); ?>">
                    <i class="bi bi-box-seam"></i> Все товары
                </a>
                <a href="<?php echo e(route('admin.purchases.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.purchases.*') ? 'active' : ''); ?>">
                    <i class="bi bi-cart-plus"></i> Закупки
                </a>
                <a href="<?php echo e(route('admin.qr.scan')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.qr.*') ? 'active' : ''); ?>">
                    <i class="bi bi-qr-code-scan"></i> QR-сканер
                </a>
                <a href="<?php echo e(route('admin.qr.manage')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.qr.manage') ? 'active' : ''); ?>">
                    <i class="bi bi-qr-code"></i> QR-коды
                </a>
                
                <div class="group-title">Люди</div>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <i class="bi bi-people"></i> Сотрудники
                </a>
                <a href="<?php echo e(route('admin.roles.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">
                    <i class="bi bi-shield-lock"></i> Роли и разрешения
                </a>
                <a href="<?php echo e(route('admin.shops.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.shops.*') ? 'active' : ''); ?>">
                    <i class="bi bi-shop"></i> Магазины
                </a>
                <a href="<?php echo e(route('admin.customers.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.customers.*') ? 'active' : ''); ?>">
                    <i class="bi bi-heart"></i> Клиенты
                </a>
                
                <div class="group-title">Отчёты</div>
                <a href="<?php echo e(route('admin.reports.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.reports.*') ? 'active' : ''); ?>">
                    <i class="bi bi-graph-up"></i> Все отчёты
                </a>
                <a href="<?php echo e(route('admin.reports.stock')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.reports.stock') ? 'active' : ''); ?>">
                    <i class="bi bi-box"></i> Остатки
                </a>
                
                <div class="group-title">Система</div>
                <a href="<?php echo e(url('admin/settings')); ?>" class="menu-item <?php echo e(request()->is('admin/settings*') ? 'active' : ''); ?>">
                    <i class="bi bi-gear"></i> Настройки
                </a>
                <a href="<?php echo e(route('admin.activity-log')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.activity-log') ? 'active' : ''); ?>">
                    <i class="bi bi-journal-text"></i> Журнал
                </a>
                
                
                <div class="md:hidden border-t border-white/5 mt-4 pt-4">
                    <button type="button" class="menu-item text-danger w-full text-left" 
                       onclick="showLogoutModal()">
                        <i class="bi bi-box-arrow-right"></i> Выход
                    </button>
                </div>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #1e293b; position: sticky; top: 0; z-index: 50;" class="topbar">
            <div style="display: flex; align-items: center; gap: 16px;">
                <button class="mobile-toggle btn" onclick="toggleSidebar()" style="background: transparent; color: #94a3b8; padding: 8px; z-index: 1002; position: relative;">
                    <i class="bi bi-list" style="font-size: 24px;"></i>
                </button>
                <h1 style="margin: 0; font-size: 20px; font-weight: 600;"><?php echo $__env->yieldContent('page-title', 'NURMAN'); ?></h1>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                
                <div style="display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: rgba(56,189,248,0.1); border-radius: 12px; border: 1px solid rgba(56,189,248,0.2);">
                    <div class="user-avatar" style="width: 32px; height: 32px; background: #38bdf8; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0f172a; font-weight: 700; font-size: 14px;">
                        <?php echo e(strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username, 0, 1))); ?>

                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span class="user-info-name" style="color: #f1f5f9; font-size: 13px; font-weight: 600; line-height: 1.2;"><?php echo e(auth()->user()->full_name ?? auth()->user()->username); ?></span>
                        <span class="user-info-role" style="color: #94a3b8; font-size: 11px; line-height: 1.2;"><?php echo e(auth()->user()->role->name ?? 'Пользователь'); ?></span>
                    </div>
                </div>
                
                <button type="button" class="btn btn-danger"
                   onclick="showLogoutModal()"
                   style="padding: 8px 16px;">
                    <i class="bi bi-box-arrow-right"></i> <span class="desktop-only">Выход</span>
                </button>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;"><?php echo csrf_field(); ?></form>
            </div>
        </div>

        <!-- Page Content -->
        <div style="padding: 24px;">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Главная</span>
        </a>
        <a href="<?php echo e(route('admin.products.index')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs('admin.products.*') ? 'active' : ''); ?>">
            <i class="bi bi-box-seam"></i>
            <span>Товары</span>
        </a>
        <a href="<?php echo e(route('admin.qr.scan')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs('admin.qr.scan') ? 'active' : ''); ?>">
            <div class="bottom-nav-center">
                <i class="bi bi-qr-code-scan"></i>
            </div>
        </a>
        <a href="<?php echo e(url('admin/settings')); ?>" class="bottom-nav-item <?php echo e(request()->is('admin/settings*') ? 'active' : ''); ?>">
            <i class="bi bi-person-gear"></i>
            <span>Профиль</span>
        </a>
    </div>

    <!-- Overlay for mobile -->
    <div id="overlay" onclick="toggleSidebar()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99;"></div>

    
    <div id="logoutModal" class="modal">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content" style="border: 1px solid rgba(239, 68, 68, 0.2);">
                <div class="modal-body" style="padding: 32px; text-align: center;">
                    <div style="width: 64px; height: 64px; background: rgba(239, 68, 68, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 32px; margin: 0 auto 20px;">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 700; color: #f1f5f9; margin-bottom: 8px;">Завершение сеанса</h3>
                    <p style="color: #94a3b8; margin-bottom: 24px;">Вы действительно хотите выйти из системы управления NURMAN?</p>
                    
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="btn btn-outline" onclick="hideLogoutModal()" style="flex: 1; justify-content: center;">
                            Отмена
                        </button>
                        <button type="button" class="btn btn-danger" onclick="document.getElementById('logout-form').submit()" style="flex: 1; justify-content: center; background: #ef4444; color: white;">
                            Выйти
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.add('show');
            modal.style.display = 'block';
            
            // Create backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop logout-backdrop';
            backdrop.onclick = hideLogoutModal;
            document.body.appendChild(backdrop);
            document.body.style.overflow = 'hidden';
        }

        function hideLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.querySelectorAll('.logout-backdrop').forEach(b => b.remove());
            document.body.style.overflow = '';
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('show');
            overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
        }
        
        // Modal functions
        window.bootstrap = {
            Modal: function(element) {
                this.element = element;
                this.backdrop = null;
            }
        };
        
        bootstrap.Modal.prototype.show = function() {
            this.element.classList.add('show');
            this.element.style.display = 'block';
            
            // Create backdrop
            this.backdrop = document.createElement('div');
            this.backdrop.className = 'modal-backdrop';
            this.backdrop.onclick = () => this.hide();
            document.body.appendChild(this.backdrop);
            document.body.style.overflow = 'hidden';
        };
        
        bootstrap.Modal.prototype.hide = function() {
            this.element.classList.remove('show');
            this.element.style.display = 'none';

            // На проекте модалки могут открываться/закрываться разными инстансами.
            // Поэтому безопасно чистим все backdrops на странице.
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            this.backdrop = null;

            document.body.style.overflow = '';
        };
        
        bootstrap.Modal.getInstance = function(element) {
            return element.modalInstance || null;
        };
        
        // Store instance on element
        document.querySelectorAll('.modal').forEach(modal => {
            modal.modalInstance = new bootstrap.Modal(modal);
        });

        // Поддержка закрытия модалок через data-bs-dismiss="modal"
        // (на проекте нет полного bootstrap JS, поэтому нужен минимальный обработчик).
        document.addEventListener('click', function(e) {
            const dismissEl = e.target.closest('[data-bs-dismiss="modal"]');
            if (!dismissEl) return;

            const modalEl = dismissEl.closest('.modal');
            if (!modalEl) return;

            const instance = bootstrap.Modal.getInstance(modalEl);
            if (instance) instance.hide();
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\nurman\resources\views/layouts/admin.blade.php ENDPATH**/ ?>