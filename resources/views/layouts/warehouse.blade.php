<!DOCTYPE html>
<html lang="ru" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NURMAN - WAREHOUSE</title>
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
                        accent: '#38bdf8',
                        'accent-hover': '#0ea5e9',
                        text: '#f8fafc',
                        muted: '#94a3b8',
                        border: 'rgba(255, 255, 255, 0.06)',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
                        'glow': '0 0 20px rgba(56,189,248,0.2)',
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

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-item:hover {
            transform: translateX(4px);
        }

        .active-nav-item {
            background: linear-gradient(90deg, rgba(56,189,248,0.15) 0%, transparent 100%);
            border-left: 3px solid #38bdf8;
            color: #38bdf8 !important;
        }

        /* Mobile specific adjustments */
        @media (max-width: 768px) {
            .main-content { margin-left: 0 !important; padding-top: 64px; }
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
            <div class="font-bold text-xl tracking-tight">NURMAN<span class="text-accent">WAREHOUSE</span></div>
        </div>
        <div class="flex items-center gap-3">
            <button class="w-10 h-10 rounded-full glass flex items-center justify-center relative">
                <i class="bi bi-bell"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-danger rounded-full"></span>
            </button>
            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-accent/20">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->username) }}&background=38bdf8&color=fff" alt="Avatar">
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed left-0 top-0 bottom-0 w-64 bg-sidebar border-r border-white/5 transition-transform duration-300 ease-in-out md:translate-x-0 overflow-y-auto">
            <div class="p-6">
                <div class="text-2xl font-extrabold tracking-tighter mb-8">
                    NURMAN<span class="text-accent">WAREHOUSE</span>
                </div>

                <nav class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-6">Главное</p>
                    <a href="{{ route('warehouse.dashboard') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('warehouse.dashboard') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-speedometer2 mr-3 text-lg"></i> Дашборд
                    </a>

                    <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-6">Склад</p>
                    <a href="{{ route('warehouse.stock.index') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('warehouse.stock.*') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-box-seam mr-3 text-lg"></i> Остатки
                    </a>
                    <a href="{{ route('warehouse.stock.low') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('warehouse.stock.low') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-exclamation-triangle mr-3 text-lg"></i> Дефицит
                    </a>

                    <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-6">Операции</p>
                    <a href="{{ route('warehouse.purchases.index') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('warehouse.purchases.*') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-cart-plus mr-3 text-lg"></i> Приёмка
                    </a>
                    <a href="{{ route('warehouse.inventory.index') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('warehouse.inventory.*') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-clipboard-check mr-3 text-lg"></i> Инвентаризация
                    </a>
                    <a href="{{ route('warehouse.writeoff.index') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('warehouse.writeoff.*') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-trash mr-3 text-lg"></i> Списание
                    </a>
                    <a href="{{ route('warehouse.transfers.index') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('warehouse.transfers.*') ? 'active-nav-item' : '' }}">
                        <i class="bi bi-arrow-left-right mr-3 text-lg"></i> Перемещение
                    </a>

                    @if(auth()->user()->can('warehouse.qr'))
                        <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-6">QR-коды</p>
                        <a href="{{ route('warehouse.qrcodes.index') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 {{ request()->routeIs('warehouse.qrcodes*') ? 'active-nav-item' : '' }}">
                            <i class="bi bi-printer mr-3 text-lg"></i> Печать QR
                        </a>
                    @endif

                    <div class="pt-10 mt-10 border-t border-white/5">
                        {{-- Профиль пользователя --}}
                        <a href="{{ route('warehouse.profile.index') }}" class="sidebar-item group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-white hover:bg-white/10 mb-2">
                            <div style="width: 28px; height: 28px; background: #38bdf8; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0f172a; font-weight: 700; font-size: 12px; margin-right: 10px; overflow: hidden;">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->full_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username, 0, 1)) }}
                                @endif
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                <span style="color: #f8fafc; font-weight: 600;">{{ auth()->user()->full_name ?? auth()->user()->username }}</span>
                                <span style="color: #94a3b8; font-size: 10px;">{{ auth()->user()->role->name ?? 'Пользователь' }}</span>
                            </div>
                        </a>
                        
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
        <main class="main-content flex-1 ml-64 min-h-screen p-4 md:p-8">
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

                @if(session('success'))
                    <div class="glass p-4 rounded-2xl border border-success/30 text-success mb-6 flex items-center gap-3">
                        <i class="bi bi-check-circle-fill text-xl"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="glass p-4 rounded-2xl border border-danger/30 text-danger mb-6 flex items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bottom Nav for Mobile -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 glass flex items-center justify-around px-2 z-[45] border-t border-white/5">
        <a href="{{ route('warehouse.dashboard') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] {{ request()->routeIs('warehouse.dashboard') ? 'text-accent' : 'text-muted' }}">
            <i class="bi bi-speedometer2 text-xl"></i>
            <span>Главная</span>
        </a>
        <a href="{{ route('warehouse.stock.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] {{ request()->routeIs('warehouse.stock.*') ? 'text-accent' : 'text-muted' }}">
            <i class="bi bi-box-seam text-xl"></i>
            <span>Склад</span>
        </a>
        <div class="relative -top-5">
            <div class="w-14 h-14 bg-accent rounded-full flex items-center justify-center text-bg shadow-glow border-4 border-bg">
                <i class="bi bi-box text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('warehouse.purchases.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] {{ request()->routeIs('warehouse.purchases.*') ? 'text-accent' : 'text-muted' }}">
            <i class="bi bi-cart-plus text-xl"></i>
            <span>Приёмка</span>
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
