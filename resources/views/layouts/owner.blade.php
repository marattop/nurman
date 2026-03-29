<!DOCTYPE html>
<html lang="ru" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NURMAN - OWNER')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        bg: '#0f172a',
                        sidebar: '#1e293b',
                        card: '#1e293b',
                        accent: '#a855f7',
                        'accent-hover': '#9333ea',
                        text: '#f8fafc',
                        muted: '#94a3b8',
                        border: 'rgba(255, 255, 255, 0.06)',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
                        'glow': '0 0 20px rgba(168, 85, 247, 0.2)',
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            background-color: #0f172a; 
            color: #f8fafc; 
            -webkit-tap-highlight-color: transparent;
        }

        /* Mobile specific body padding to account for fixed header/footer */
        @media (max-width: 768px) {
            body {
                padding-top: 64px; /* Height of mobile header */
                padding-bottom: 64px; /* Height of mobile bottom nav */
            }
            .main-content { margin-left: 0 !important; } /* Removed padding-top and padding-bottom from .main-content here */
            .sidebar { transform: translateX(-100%); z-index: 50; }
            .sidebar.open { transform: translateX(0); }
            .mobile-overlay { display: none; }
            .mobile-overlay.show { display: block; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 40; }
        }

        /* Table to Cards on mobile */
        @media (max-width: 640px) {
            .responsive-table thead { display: none; }
            .responsive-table tr { 
                display: block; 
                margin-bottom: 1rem; 
                background: rgba(30, 41, 59, 0.5); 
                border-radius: 12px;
                padding: 12px;
                border: 1px solid rgba(255, 255, 255, 0.06);
            }
            .responsive-table td { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                padding: 8px 0;
                border: none !important;
                text-align: right !important;
            }
            .responsive-table td::before { 
                content: attr(data-label); 
                font-weight: 600; 
                color: #94a3b8; 
                text-align: left;
                font-size: 0.75rem;
                text-transform: uppercase;
            }
        }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stats-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    </style>
    @stack('styles')
</head>
<body class="font-sans">
    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="mobile-overlay" onclick="toggleSidebar()"></div>

    <!-- Mobile Header -->
    <header class="md:hidden fixed top-0 left-0 right-0 h-16 glass flex items-center justify-between px-4 z-[45]">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="p-2 text-muted hover:text-white transition-colors">
                <i class="bi bi-list text-2xl"></i>
            </button>
            <div class="font-bold text-xl tracking-tight">NURMAN<span class="text-accent">.</span></div>
        </div>
        <div class="flex items-center gap-3">
            <button class="w-10 h-10 rounded-full glass flex items-center justify-center relative">
                <i class="bi bi-bell"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-danger rounded-full"></span>
            </button>
            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-accent/20">
                <img src="https://ui-avatars.com/api/?name=Owner&background=a855f7&color=fff" alt="Avatar">
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed left-0 top-0 bottom-0 w-64 bg-sidebar border-r border-border transition-transform duration-300 ease-in-out md:translate-x-0 overflow-y-auto">
            <div class="p-6">
                <div class="text-2xl font-extrabold tracking-tighter mb-8">
                    NURMAN<span class="text-accent">OWNER</span>
                </div>

                <nav class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-6">Главное</p>
                    <a href="{{ route('owner.dashboard') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('owner.dashboard') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-grid-1x2-fill mr-3 text-lg"></i> Дашборд
                    </a>

                    <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-6">Управление</p>
                    <a href="{{ route('owner.users.index') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('owner.users.*') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-people-fill mr-3 text-lg"></i> Пользователи
                    </a>
                    
                    <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-6">Аналитика и Отчеты</p>
                    <a href="{{ route('owner.scan-product') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('owner.scan-product*') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-qr-code-scan mr-3 text-lg"></i> Сканер товаров
                    </a>
                    <a href="{{ route('owner.reports.sales') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('owner.reports.sales') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-graph-up mr-3 text-lg"></i> Продажи
                    </a>
                    <a href="{{ route('owner.reports.purchases') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('owner.reports.purchases') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-cart-fill mr-3 text-lg"></i> Закупки
                    </a>
                    <a href="{{ route('owner.reports.profit') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('owner.reports.profit') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-cash-stack mr-3 text-lg"></i> Прибыль
                    </a>
                    <a href="{{ route('owner.reports.expenses') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('owner.reports.expenses') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-wallet2 mr-3 text-lg"></i> Расходы
                    </a>

                    <div class="pt-10 mt-10 border-t border-border">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-danger/80 hover:text-danger hover:bg-danger/10">
                                <i class="bi bi-box-arrow-right mr-3 text-lg"></i> Выйти
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content flex-1 ml-64 min-h-screen p-6 md:p-10">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">@yield('page-title')</h1>
                        <p class="text-muted text-sm mt-1">@yield('page-subtitle')</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @yield('page-actions')
                    </div>
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bottom Nav for Mobile -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 glass flex items-center justify-around px-2 z-[45] border-t border-white/5">
        <a href="{{ route('owner.dashboard') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] {{ request()->routeIs('owner.dashboard') ? 'text-accent' : 'text-muted' }}">
            <i class="bi bi-grid-1x2 text-xl"></i>
            <span>Главная</span>
        </a>
        <a href="{{ route('owner.reports.sales') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] {{ request()->routeIs('owner.reports.*') ? 'text-accent' : 'text-muted' }}">
            <i class="bi bi-bar-chart text-xl"></i>
            <span>Отчеты</span>
        </a>
        <div class="relative -top-5">
            <div class="w-14 h-14 bg-accent rounded-full flex items-center justify-center text-bg shadow-glow border-4 border-bg">
                <i class="bi bi-lightning-charge-fill text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('owner.users.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] {{ request()->routeIs('owner.users.*') ? 'text-accent' : 'text-muted' }}">
            <i class="bi bi-people text-xl"></i>
            <span>Команда</span>
        </a>
        <button onclick="toggleSidebar()" class="flex flex-col items-center justify-center gap-1 text-[10px] text-muted">
            <i class="bi bi-list text-xl"></i>
            <span>Меню</span>
        </button>
    </nav>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
