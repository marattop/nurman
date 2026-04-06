<!DOCTYPE html>
<html lang="ru" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NURMAN OWNER')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        bg:           '#0f172a',
                        sidebar:      '#1e293b',
                        card:         '#1e293b',
                        accent:       '#a855f7',
                        'accent-hover': '#9333ea',
                        text:         '#f8fafc',
                        muted:        '#94a3b8',
                        border:       'rgba(255,255,255,0.06)',
                        success:      '#10b981',
                        danger:       '#ef4444',
                        warning:      '#f59e0b',
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(0,0,0,0.37)',
                        'glow':  '0 0 20px rgba(168,85,247,0.3)',
                    }
                }
            }
        }
    </script>
    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
            min-height: 100vh;
        }

        /* ── Glass utility ── */
        .glass {
            background: rgba(15, 23, 42, 0.80);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        /* ── Date input calendar icon visibility ── */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            filter: brightness(0) saturate(100%) invert(100%) !important;
            opacity: 1 !important;
            cursor: pointer;
        }

        /* ── Active nav item ── */
        .active-nav-item {
            background: rgba(168, 85, 247, 0.15) !important;
            color: #a855f7 !important;
            border-left: 3px solid #a855f7;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(168,85,247,0.3); border-radius: 4px; }

        /* ── Stats grid ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stats-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }

        /* ═══════════════════════════════════════
           DESKTOP layout (≥769px)
        ═══════════════════════════════════════ */
        @media (min-width: 769px) {
            .mobile-header  { display: none !important; }
            .mobile-bottom-nav { display: none !important; }
            .mobile-overlay { display: none !important; }
        }

        /* ═══════════════════════════════════════
           MOBILE layout (≤768px)
        ═══════════════════════════════════════ */
        @media (max-width: 768px) {
            /* Reserve space for fixed header & bottom nav */
            body {
                padding-top: 64px;
                padding-bottom: calc(72px + env(safe-area-inset-bottom, 0px));
            }

            /* Sidebar slide-in */
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease-in-out;
                z-index: 60 !important;
                top: 0 !important;
                padding-top: 64px; /* so content isn't behind header */
            }
            .sidebar.open { transform: translateX(0); }

            /* Main content full width */
            .main-content {
                margin-left: 0 !important;
                padding: 1rem !important;
            }

            /* Mobile overlay backdrop */
            .mobile-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.6);
                backdrop-filter: blur(4px);
                z-index: 50;
            }
            .mobile-overlay.show { display: block; }

            /* Page headings smaller on mobile */
            .page-title-mobile h1 { font-size: 1.25rem !important; }
            .page-title-mobile p  { font-size: 0.75rem !important; }

            /* Page actions collapse on mobile */
            .page-actions-mobile { flex-wrap: wrap; }
        }

        /* ── Table → Cards on small screens ── */
        @media (max-width: 640px) {
            .responsive-table thead { display: none; }
            .responsive-table tr {
                display: block;
                margin-bottom: 0.75rem;
                background: rgba(30,41,59,0.6);
                border-radius: 16px;
                padding: 12px 16px;
                border: 1px solid rgba(255,255,255,0.06);
            }
            .responsive-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 6px 0;
                border: none !important;
                text-align: right !important;
                font-size: 0.8125rem;
            }
            .responsive-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #94a3b8;
                text-align: left;
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                white-space: nowrap;
                margin-right: 8px;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans">

    {{-- ══════════════════════════ MOBILE OVERLAY ══════════════════════════ --}}
    <div id="mobile-overlay" class="mobile-overlay" onclick="closeSidebar()"></div>

    {{-- ══════════════════════════ MOBILE HEADER ══════════════════════════ --}}
    <header class="mobile-header md:hidden fixed top-0 left-0 right-0 z-[55]"
            style="height:64px; background:rgba(15,23,42,0.88); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border-bottom:1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center justify-between h-full px-4">

            {{-- Left: burger + logo --}}
            <div class="flex items-center gap-3">
                <button id="burger-btn" onclick="toggleSidebar()"
                    class="w-10 h-10 rounded-xl flex items-center justify-center text-muted hover:text-white hover:bg-white/10 transition-all active:scale-95">
                    <i class="bi bi-list text-2xl"></i>
                </button>
                <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-1.5">
                    <span class="font-extrabold text-lg tracking-tight text-white">NURMAN</span>
                    <span class="font-extrabold text-lg tracking-tight text-accent">OWNER</span>
                </a>
            </div>

            {{-- Right: user info --}}
            <div class="flex items-center gap-2">
                {{-- User avatar / initials --}}
                @php
                    $authUser = auth()->user();
                    $displayName = $authUser->full_name ?: $authUser->username;
                    $initials = collect(explode(' ', trim($displayName)))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
                    $avatarUrl  = $authUser->avatar ? '/storage/' . $authUser->avatar : null;
                @endphp
                <div class="flex items-center gap-2 py-1 px-2 rounded-xl bg-white/5 border border-white/10">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $displayName }}"
                             class="w-8 h-8 rounded-full object-cover border-2 border-accent/30">
                    @else
                        <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center border-2 border-accent/30 text-accent text-xs font-extrabold">
                            {{ $initials ?: 'O' }}
                        </div>
                    @endif
                    <span class="text-sm font-semibold text-white max-w-[100px] truncate hidden xs:block">
                        {{ Str::limit($displayName, 14) }}
                    </span>
                </div>
            </div>
        </div>
    </header>

    {{-- ══════════════════════════ DESKTOP SIDEBAR + MAIN ══════════════════════════ --}}
    <div class="flex min-h-screen">

        {{-- ── Sidebar ── --}}
        <aside id="sidebar"
               class="sidebar fixed left-0 top-0 bottom-0 w-64 bg-sidebar border-r border-border transition-transform duration-300 ease-in-out md:translate-x-0 flex flex-col"
               style="z-index:60; overflow-y:auto;">

            {{-- Logo --}}
            <div class="p-6 border-b border-white/5 flex-shrink-0">
                <div class="text-2xl font-extrabold tracking-tighter">
                    NURMAN<span class="text-accent">OWNER</span>
                </div>
                {{-- Owner info in sidebar --}}
                @php $authUser = $authUser ?? auth()->user(); @endphp
                <div class="mt-4 flex items-center gap-3">
                    @if($avatarUrl ?? (auth()->user()->avatar ? '/storage/' . auth()->user()->avatar : null))
                        <img src="{{ '/storage/' . auth()->user()->avatar }}" alt=""
                             class="w-10 h-10 rounded-full object-cover border-2 border-accent/30 flex-shrink-0">
                    @else
                        @php
                            $su = auth()->user();
                            $sn = $su->full_name ?: $su->username;
                            $si = collect(explode(' ', trim($sn)))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
                        @endphp
                        <div class="w-10 h-10 rounded-full bg-accent/20 flex items-center justify-center border-2 border-accent/30 text-accent font-extrabold text-sm flex-shrink-0">
                            {{ $si ?: 'O' }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-white truncate">{{ auth()->user()->full_name ?: auth()->user()->username }}</div>
                        <div class="text-xs text-accent font-medium">Владелец</div>
                    </div>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-2">Главное</p>
                <a href="{{ route('owner.dashboard') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.dashboard') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-grid-1x2-fill mr-3 text-lg"></i> Дашборд
                </a>

                <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-5">Управление</p>
                <a href="{{ route('owner.module.products') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.module.products*') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-box-seam mr-3 text-lg"></i> Товары
                </a>
                <a href="{{ route('owner.users.index') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.users.*') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-people-fill mr-3 text-lg"></i> Пользователи
                </a>

                <p class="px-3 text-[10px] font-bold text-muted uppercase tracking-widest mb-2 mt-5">Аналитика и Отчёты</p>
                <a href="{{ route('owner.scan-product') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.scan-product*') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-qr-code-scan mr-3 text-lg"></i> Сканер товаров
                </a>
                <a href="{{ route('owner.reports.sales') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.reports.sales') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-graph-up mr-3 text-lg"></i> Продажи
                </a>
                <a href="{{ route('owner.reports.purchases') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.reports.purchases') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-cart-fill mr-3 text-lg"></i> Закупки
                </a>
                <a href="{{ route('owner.reports.profit') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.reports.profit') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-cash-stack mr-3 text-lg"></i> Прибыль
                </a>
                <a href="{{ route('owner.reports.expenses') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.reports.expenses') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-wallet2 mr-3 text-lg"></i> Расходы
                </a>
                <a href="{{ route('owner.reports.analytics') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-muted hover:text-white hover:bg-white/5 transition-all {{ request()->routeIs('owner.reports.analytics') ? 'active-nav-item' : '' }}">
                    <i class="bi bi-graph-up-arrow mr-3 text-lg"></i> Аналитика
                </a>
            </nav>

            {{-- Logout --}}
            <div class="p-4 border-t border-white/5 flex-shrink-0">
                <button type="button"
                    class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-danger/80 hover:text-danger hover:bg-danger/10 transition-all"
                    onclick="showLogoutModal()">
                    <i class="bi bi-box-arrow-right mr-3 text-lg"></i> Выйти
                </button>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
            </div>
        </aside>

        {{-- ── Main Content ── --}}
        <main class="main-content flex-1 md:ml-64 min-h-screen p-5 md:p-10">
            <div class="max-w-7xl mx-auto">

                {{-- Page header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4 page-title-mobile">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">@yield('page-title')</h1>
                        <p class="text-muted text-sm mt-1">@yield('page-subtitle')</p>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap page-actions-mobile">
                        @yield('page-actions')
                    </div>
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    {{-- ══════════════════════════ MOBILE BOTTOM NAV ══════════════════════════ --}}
    <nav class="mobile-bottom-nav md:hidden fixed bottom-0 left-0 right-0 z-[55]"
         style="background:rgba(15,23,42,0.90); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
                border-top:1px solid rgba(255,255,255,0.07);
                height: calc(64px + env(safe-area-inset-bottom, 0px));
                padding-bottom: env(safe-area-inset-bottom, 0px);">
        <div class="flex items-center justify-around h-16 px-2">

            <a href="{{ route('owner.dashboard') }}"
               class="flex flex-col items-center justify-center gap-0.5 w-1/5 transition-colors {{ request()->routeIs('owner.dashboard') ? 'text-accent' : 'text-muted hover:text-white' }}">
                <i class="bi bi-grid-1x2{{ request()->routeIs('owner.dashboard') ? '-fill' : '' }} text-xl"></i>
                <span class="text-[9px] font-semibold">Главная</span>
            </a>

            <a href="{{ route('owner.reports.sales') }}"
               class="flex flex-col items-center justify-center gap-0.5 w-1/5 transition-colors {{ request()->routeIs('owner.reports.*') ? 'text-accent' : 'text-muted hover:text-white' }}">
                <i class="bi bi-bar-chart{{ request()->routeIs('owner.reports.*') ? '-fill' : '' }} text-xl"></i>
                <span class="text-[9px] font-semibold">Отчёты</span>
            </a>

            {{-- Centre FAB: QR scanner --}}
            <div class="relative w-1/5 flex justify-center">
                <a href="{{ route('owner.scan-product') }}"
                   class="absolute -top-7 w-14 h-14 rounded-full flex items-center justify-center
                          {{ request()->routeIs('owner.scan-product*') ? 'bg-accent/90' : 'bg-accent' }}
                          text-white border-4 border-[#0f172a] shadow-[0_4px_24px_rgba(168,85,247,0.5)]
                          hover:scale-105 active:scale-95 transition-transform">
                    <i class="bi bi-qr-code-scan text-2xl"></i>
                </a>
            </div>

            <a href="{{ route('owner.users.index') }}"
               class="flex flex-col items-center justify-center gap-0.5 w-1/5 transition-colors {{ request()->routeIs('owner.users.*') ? 'text-accent' : 'text-muted hover:text-white' }}">
                <i class="bi bi-people{{ request()->routeIs('owner.users.*') ? '-fill' : '' }} text-xl"></i>
                <span class="text-[9px] font-semibold">Команда</span>
            </a>

            <button onclick="toggleSidebar()"
                class="flex flex-col items-center justify-center gap-0.5 w-1/5 text-muted hover:text-white transition-colors">
                <i class="bi bi-list text-xl"></i>
                <span class="text-[9px] font-semibold">Меню</span>
            </button>
        </div>
    </nav>

    {{-- ══════════════════════════ LOGOUT MODAL ══════════════════════════ --}}
    <div id="logoutModal" style="display:none; position:fixed; inset:0; z-index:2000; overflow-y:auto;">
        <div style="position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);" onclick="hideLogoutModal()"></div>
        <div style="display:flex; align-items:center; justify-content:center; min-height:100vh; padding:1rem;">
            <div style="position:relative; background:#1e293b; border:1px solid rgba(239,68,68,0.2); border-radius:20px; padding:36px 32px; text-align:center; max-width:400px; width:100%; z-index:1;">
                <div style="width:64px; height:64px; background:rgba(239,68,68,0.12); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#ef4444; font-size:32px; margin:0 auto 20px;">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <h3 style="font-size:20px; font-weight:700; color:#f1f5f9; margin-bottom:8px;">Завершение сеанса</h3>
                <p style="color:#94a3b8; margin-bottom:28px; font-size:14px;">Вы действительно хотите выйти из системы?</p>
                <div style="display:flex; gap:12px;">
                    <button onclick="hideLogoutModal()" style="flex:1; padding:12px; background:rgba(255,255,255,0.05); color:#f1f5f9; border:1px solid rgba(255,255,255,0.1); border-radius:12px; font-weight:600; cursor:pointer;">
                        Отмена
                    </button>
                    <button onclick="document.getElementById('logout-form').submit()" style="flex:1; padding:12px; background:#ef4444; color:white; border:none; border-radius:12px; font-weight:700; cursor:pointer;">
                        Выйти
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── Sidebar toggle ──
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const burger  = document.getElementById('burger-btn');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            if (burger) burger.classList.toggle('text-accent');
        }
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const burger  = document.getElementById('burger-btn');
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            if (burger) burger.classList.remove('text-accent');
        }

        // Close sidebar when a link inside it is tapped (mobile)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });

        // ── Logout modal ──
        function showLogoutModal() { document.getElementById('logoutModal').style.display = 'block'; }
        function hideLogoutModal() { document.getElementById('logoutModal').style.display = 'none'; }

        // Close on Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                hideLogoutModal();
                closeSidebar();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
