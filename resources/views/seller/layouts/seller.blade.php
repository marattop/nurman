<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NURMAN - POS')</title>
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
                        accent: '#10b981',
                        'accent-hover': '#059669',
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
        body { background-color: #1e293b; color: #f1f5f9; }
        .sidebar { width: 260px; height: 100vh; position: fixed; left: 0; top: 0; background: #0f172a; border-right: 1px solid rgba(255,255,255,0.05); overflow-y: auto; z-index: 100; }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .menu-item { display: flex; align-items: center; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 10px; margin: 4px 8px; transition: 0.2s; }
        .menu-item:hover { background: rgba(255,255,255,0.03); color: #f1f5f9; }
        .menu-item.active { background: rgba(16,185,129,0.1); color: #10b981; }
        .menu-item i { width: 22px; margin-right: 12px; font-size: 18px; }
        .group-title { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; padding: 16px 16px 8px; margin-top: 8px; }
        .logo { padding: 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .logo h2 { margin: 0; font-size: 22px; font-weight: 700; color: #f1f5f9; letter-spacing: -0.5px; }
        .logo span { color: #10b981; }
        .card { background: #1e293b; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 24px; }
        .table th { color: #64748b; font-size: 12px; text-transform: uppercase; padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .table td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #f1f5f9; }
        .table tr:hover { background: rgba(255,255,255,0.02); }
        .btn { padding: 10px 20px; border-radius: 10px; font-weight: 500; border: none; cursor: pointer; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary { background: #10b981; color: #0f172a; }
        .btn-primary:hover { background: #059669; }
        .btn-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }
        .btn-outline { background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
        .btn-outline:hover { background: rgba(16,185,129,0.15); }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        .badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
        .badge-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .badge-blue { background: rgba(56,189,248,0.15); color: #38bdf8; }
        .input { background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 14px; color: #f1f5f9; width: 100%; box-sizing: border-box; }
        .input:focus { outline: none; border-color: #10b981; }
        .page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .page-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px; }
        .page-title { font-size: 28px; font-weight: 700; margin: 0; }
        .page-subtitle { font-size: 14px; color: #94a3b8; margin: 4px 0 0 0; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .card-header-title { font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: 0.3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-toggle { display: block !important; }
        }
        .mobile-toggle { display: none; }
        .stat-card { background: #1e293b; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 20px; }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <h2>NURMAN <span>SELLER</span></h2>
        </div>
        
        {{-- Профиль пользователя в sidebar --}}
        <a href="{{ route('admin.profile.index') }}" class="menu-item" style="margin: 8px; border: 1px solid rgba(16,185,129,0.2); background: rgba(16,185,129,0.05); display: flex; align-items: center; text-decoration: none;">
            <div style="width: 36px; height: 36px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0f172a; font-weight: 700; font-size: 16px; margin-right: 12px; overflow: hidden; flex-shrink: 0;">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->full_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username, 0, 1)) }}
                @endif
            </div>
            <div style="display: flex; flex-direction: column; flex: 1; min-width: 0;">
                <span style="color: #f1f5f9; font-size: 14px; font-weight: 600; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->full_name ?? auth()->user()->username }}</span>
                <span style="color: #94a3b8; font-size: 11px; line-height: 1.2;">{{ auth()->user()->role->display_name ?? auth()->user()->role->name ?? 'Пользователь' }}</span>
            </div>
            <i class="bi bi-chevron-right" style="margin-left: auto; font-size: 14px; color: #64748b; flex-shrink: 0;"></i>
        </a>
        
        <nav style="padding: 8px;">
            <div class="group-title">Главное</div>
            <a href="{{ route('seller.dashboard') }}" class="menu-item {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Дашборд
            </a>
            <a href="{{ route('seller.pos.index') }}" class="menu-item {{ request()->routeIs('seller.pos.*') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i> Продажа
            </a>
            <a href="{{ route('seller.my-sales.index') }}" class="menu-item {{ request()->routeIs('seller.my-sales*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> История продаж
            </a>
            
            <div class="group-title">Клиенты</div>
            <a href="{{ route('seller.customers.index') }}" class="menu-item {{ request()->routeIs('seller.customers.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Клиенты
            </a>
            
            <div class="group-title">Инструменты</div>
            <a href="{{ route('seller.products.scan') }}" class="menu-item {{ request()->routeIs('seller.products.scan') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i> Сканер товаров
            </a>
            <a href="{{ route('seller.pos.index') }}" class="menu-item {{ request()->routeIs('seller.pos.index') ? 'active' : '' }}">
                <i class="bi bi-ticket"></i> Сканер купонов
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #1e293b; position: sticky; top: 0; z-index: 50;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <button class="mobile-toggle btn" onclick="toggleSidebar()" style="background: transparent; color: #94a3b8; padding: 8px;">
                    <i class="bi bi-list" style="font-size: 24px;"></i>
                </button>
                <h1 style="margin: 0; font-size: 20px; font-weight: 600;">@yield('page-title', 'NURMAN')</h1>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('logout') }}" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="padding: 8px 16px;">
                    <i class="bi bi-box-arrow-right"></i> <span class="desktop-only">Выход</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            </div>
        </div>

        <!-- Page Content -->
        <div style="padding: 24px;">
            @yield('content')
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div id="overlay" onclick="toggleSidebar()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99;"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('show');
            overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
        }
    </script>
    @stack('scripts')
</body>
</html>
