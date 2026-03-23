<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'NURMAN'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* === MODERN GLASSMORPHISM / APPLE UI === */
        :root {
            --accent-blue: #007AFF;
            --accent-green: #34C759;
            --accent-orange: #FF9500;
            --accent-red: #FF3B30;
            --accent-purple: #AF52DE;
            --accent-cyan: var(--accent-cyan);
            
            /* Тени */
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.12);
            --shadow-xl: 0 30px 70px rgba(0, 0, 0, 0.15);
            
            /* Скругления */
            --radius-lg: 20px;
            --radius-xl: 28px;
        }

        /* Светлая тема */
        [data-theme="light"] {
            --bg-primary: #F2F2F7;
            --bg-secondary: var(--text-main);
            --bg-glass: rgba(255, 255, 255, 0.75);
            --bg-glass-heavy: rgba(255, 255, 255, 0.85);
            --text-primary: #1C1C1E;
            --text-secondary: #8E8E93;
            --text-muted: #C7C7CC;
            --border-color: rgba(0, 0, 0, 0.05);
            --border-light: rgba(0, 0, 0, 0.1);
            --sidebar-bg: rgba(255, 255, 255, 0.6);
            --sidebar-hover: rgba(0, 0, 0, 0.05);
            --sidebar-active: rgba(0, 122, 255, 0.15);
            
            /* Алиасы для совместимости */
            --bg-card: var(--bg-glass);
            --bg-card-hover: var(--bg-glass-heavy);
            --bg-tertiary: var(--bg-secondary);
            --card-shadow: var(--shadow-md);
            --card-shadow-hover: var(--shadow-lg);
            --gradient-warm: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-primary-dark) 100%);
            --gradient: var(--gradient-warm);
        }
        
        /* Тёмная тема (по умолчанию) */
        [data-theme="dark"] {
            --bg-primary: var(--bg-color);
            --bg-secondary: #1C1C1E;
            --bg-glass: rgba(28, 28, 30, 0.6);
            --bg-glass-heavy: rgba(28, 28, 30, 0.8);
            --text-primary: var(--text-main);
            --text-secondary: #98989D;
            --text-muted: #636366;
            --border-color: rgba(255, 255, 255, 0.08);
            --border-light: rgba(255, 255, 255, 0.12);
            --sidebar-bg: rgba(28, 28, 30, 0.7);
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --sidebar-active: rgba(0, 122, 255, 0.2);
            
            /* Алиасы для совместимости */
            --bg-card: var(--bg-glass);
            --bg-card-hover: var(--bg-glass-heavy);
            --bg-tertiary: var(--bg-secondary);
            --card-shadow: var(--shadow-md);
            --card-shadow-hover: var(--shadow-lg);
            --gradient-warm: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-primary-dark) 100%);
            --gradient: var(--gradient-warm);
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* === WRAPPER === */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        @media (max-width: 991px) {
            .wrapper {
                flex-direction: column;
            }
        }

        /* === SIDEBAR === */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid var(--border-color);
            padding: 24px 16px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (max-width: 991px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -280px;
                height: 100vh;
                overflow-y: auto;
                box-shadow: var(--shadow-xl);
            }
            .sidebar.show {
                left: 0;
            }
        }

        .sidebar-brand {
            padding: 8px 12px;
            margin-bottom: 24px;
        }

        .sidebar-brand h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 4px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: var(--radius-lg);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            font-size: 15px;
        }

        .sidebar-menu a:hover {
            background: var(--sidebar-hover);
            color: var(--text-primary);
        }

        .sidebar-menu a.active {
            background: var(--sidebar-active);
            color: var(--accent-blue);
        }

        .sidebar-menu a i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
        }

        /* === MAIN CONTENT === */
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* === TOPBAR === */
        .topbar {
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--sidebar-hover);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-icon:hover {
            background: var(--sidebar-hover);
            transform: scale(1.05);
            color: var(--text-primary);
        }

        .btn-logout {
            background: rgba(255, 59, 48, 0.15);
            border: 1px solid rgba(255, 59, 48, 0.3);
            color: #FF3B30;
        }

        .btn-logout:hover {
            background: rgba(255, 59, 48, 0.25);
        }

        @media (max-width: 991px) {
            .topbar-title {
                display: none;
            }
        }

        /* === MAIN CONTENT AREA === */
        .content {
            flex: 1;
            padding: 24px;
        }

        @media (max-width: 768px) {
            .content {
                padding: 16px;
            }
        }

        .container-fluid {
            max-width: 1400px;
        }

        /* === CARDS & PANELS === */
        .glass-card {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
            padding: 24px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .glass-card:hover {
            background: var(--bg-glass-heavy);
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }

        /* === BUTTONS === */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue) 0%, #0056CC 100%);
            box-shadow: 0 4px 20px rgba(0, 122, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 122, 255, 0.4);
        }

        /* === FORMS === */
        .form-control, .form-select {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            padding: 10px 14px;
        }

        .form-control:focus, .form-select:focus {
            background: var(--bg-glass-heavy);
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
            color: var(--text-primary);
        }

        /* === TABLES === */
        .table {
            color: var(--text-primary);
        }

        .table thead th {
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .table-hover tbody tr:hover {
            background: var(--sidebar-hover);
        }

        /* === BADGES === */
        .badge {
            border-radius: 10px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
        }

        /* === THEME TOGGLE === */
        .theme-toggle {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--sidebar-hover);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            transform: scale(1.05);
            background: var(--sidebar-hover);
        }

        /* === MOBILE OVERLAY === */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 998;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* === ANIMATIONS === */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .glass-card, .sidebar-menu a {
            animation: fadeIn 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* === SCROLLBAR === */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <h3>NURMAN</h3>
            </div>
            <ul class="sidebar-menu">
                <?php if(auth()->user()->isOwner()): ?>
                    <li><a href="<?php echo e(route('owner.module.dashboard')); ?>" class="<?php echo e(request()->routeIs('owner.module.dashboard') ? 'active' : ''); ?>"><i class="bi bi-speedometer2"></i> Дашборд</a></li>
                    <li><a href="<?php echo e(route('owner.module.reports.profit')); ?>" class="<?php echo e(request()->routeIs('owner.module.reports.*') ? 'active' : ''); ?>"><i class="bi bi-cash-stack"></i> Отчёты</a></li>
                    <li><a href="<?php echo e(route('owner.module.analytics')); ?>" class="<?php echo e(request()->routeIs('owner.module.analytics*') ? 'active' : ''); ?>"><i class="bi bi-graph-up"></i> Аналитика</a></li>
                <?php elseif(auth()->user()->isAdmin()): ?>
                    <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"><i class="bi bi-speedometer2"></i> Дашборд</a></li>
                    <li><a href="<?php echo e(route('admin.products.index')); ?>" class="<?php echo e(request()->routeIs('admin.products.*') ? 'active' : ''); ?>"><i class="bi bi-box-seam"></i> Товары</a></li>
                    <li><a href="<?php echo e(route('admin.qr.scan')); ?>" class="<?php echo e(request()->routeIs('admin.qr.scan') ? 'active' : ''); ?>"><i class="bi bi-qr-code-scan"></i> QR-сканер</a></li>
                    <li><a href="<?php echo e(route('admin.qr.manage')); ?>" class="<?php echo e(request()->routeIs('admin.qr.manage') ? 'active' : ''); ?>"><i class="bi bi-qr-code"></i> QR-коды</a></li>
                    <li><a href="<?php echo e(route('admin.users.index')); ?>" class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>"><i class="bi bi-person-badge"></i> Сотрудники</a></li>
                    <li><a href="<?php echo e(route('admin.shops.index')); ?>" class="<?php echo e(request()->routeIs('admin.shops.*') ? 'active' : ''); ?>"><i class="bi bi-shop"></i> Магазины</a></li>
                    <li><a href="<?php echo e(route('admin.sellers.shops')); ?>" class="<?php echo e(request()->routeIs('admin.sellers.*') ? 'active' : ''); ?>"><i class="bi bi-person-badge"></i> Продавцы</a></li>
                    <li><a href="<?php echo e(route('admin.customers.index')); ?>" class="<?php echo e(request()->routeIs('admin.customers.*') ? 'active' : ''); ?>"><i class="bi bi-person-heart"></i> Клиенты</a></li>
                    <li><a href="<?php echo e(route('admin.reports.index')); ?>" class="<?php echo e(request()->routeIs('admin.reports.*') ? 'active' : ''); ?>"><i class="bi bi-graph-up"></i> Отчёты</a></li>
                    <li><a href="<?php echo e(url('admin/settings')); ?>" class="<?php echo e(request()->is('admin/settings*') ? 'active' : ''); ?>"><i class="bi bi-gear"></i> Настройки</a></li>
                    <li><a href="<?php echo e(route('admin.activity-log')); ?>" class="<?php echo e(request()->routeIs('admin.activity-log') ? 'active' : ''); ?>"><i class="bi bi-journal-text"></i> Журнал</a></li>
                <?php elseif(auth()->user()->isSeller()): ?>
                    <li><a href="<?php echo e(route('seller.dashboard')); ?>" class="<?php echo e(request()->routeIs('seller.dashboard') ? 'active' : ''); ?>"><i class="bi bi-speedometer2"></i> Дашборд</a></li>
                    <li><a href="<?php echo e(route('seller.pos.index')); ?>" class="<?php echo e(request()->routeIs('seller.pos.*') ? 'active' : ''); ?>"><i class="bi bi-cart"></i> POS-терминал</a></li>
                    <li><a href="<?php echo e(route('seller.customers.index')); ?>" class="<?php echo e(request()->routeIs('seller.customers.*') ? 'active' : ''); ?>"><i class="bi bi-people"></i> Клиенты</a></li>
                    <li><a href="<?php echo e(route('seller.my-sales.index')); ?>" class="<?php echo e(request()->routeIs('seller.my-sales.*') ? 'active' : ''); ?>"><i class="bi bi-receipt"></i> Мои продажи</a></li>
                <?php elseif(auth()->user()->isWarehouse()): ?>
                    <li><a href="<?php echo e(route('warehouse.dashboard')); ?>" class="<?php echo e(request()->routeIs('warehouse.dashboard') ? 'active' : ''); ?>"><i class="bi bi-speedometer2"></i> Дашборд</a></li>
                    <li><a href="<?php echo e(route('warehouse.purchases.index')); ?>" class="<?php echo e(request()->routeIs('warehouse.purchases.*') ? 'active' : ''); ?>"><i class="bi bi-truck"></i> Приёмка</a></li>
                    <li><a href="<?php echo e(route('warehouse.stock.index')); ?>" class="<?php echo e(request()->routeIs('warehouse.stock.*') ? 'active' : ''); ?>"><i class="bi bi-layers"></i> Остатки</a></li>
                <?php elseif(auth()->user()->isClient()): ?>
                    <li><a href="<?php echo e(route('client.dashboard')); ?>" class="<?php echo e(request()->routeIs('client.dashboard') ? 'active' : ''); ?>"><i class="bi bi-speedometer2"></i> Главная</a></li>
                    <li><a href="<?php echo e(route('client.profile.index')); ?>" class="<?php echo e(request()->routeIs('client.profile.*') ? 'active' : ''); ?>"><i class="bi bi-person"></i> Профиль</a></li>
                    <li><a href="<?php echo e(route('client.purchases.index')); ?>" class="<?php echo e(request()->routeIs('client.purchases.*') ? 'active' : ''); ?>"><i class="bi bi-receipt"></i> Покупки</a></li>
                    <li><a href="<?php echo e(route('client.coupons.index')); ?>" class="<?php echo e(request()->routeIs('client.coupons.*') ? 'active' : ''); ?>"><i class="bi bi-ticket"></i> Купоны</a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="btn-icon" id="sidebarToggle" style="display: none;">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="topbar-title"><?php echo $__env->yieldContent('page-title', 'NURMAN'); ?></div>
                </div>
                <div class="topbar-right">
                    <button class="btn-icon" id="themeToggle">
                        <i class="bi bi-moon-stars" id="themeIconMoon"></i>
                        <i class="bi bi-sunny" id="themeIconSun" style="display: none;"></i>
                    </button>
                    <a href="<?php echo e(route('logout')); ?>" class="btn-icon btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <main class="content">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle (Mobile)
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        // Показываем кнопку только на мобильных
        function checkMobile() {
            if (window.innerWidth < 992) {
                if (sidebarToggle) sidebarToggle.style.display = 'flex';
            } else {
                if (sidebarToggle) sidebarToggle.style.display = 'none';
            }
        }
        checkMobile();
        window.addEventListener('resize', checkMobile);

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIconSun = document.getElementById('themeIconSun');
        const themeIconMoon = document.getElementById('themeIconMoon');
        const html = document.documentElement;

        function updateThemeIcon(theme) {
            if (theme === 'light') {
                themeIconSun.style.display = 'block';
                themeIconMoon.style.display = 'none';
            } else {
                themeIconSun.style.display = 'none';
                themeIconMoon.style.display = 'block';
            }
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
            });
        }
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\nurman\resources\views/layouts/app.blade.php ENDPATH**/ ?>