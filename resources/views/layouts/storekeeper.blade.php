<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NURMAN - Склад')</title>
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
                        accent: '#22c55e',
                        'accent-hover': '#16a34a',
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
        .menu-item.active { background: rgba(34,197,94,0.1); color: #22c55e; }
        .menu-item i { width: 22px; margin-right: 12px; font-size: 18px; }
        .group-title { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; padding: 16px 16px 8px; margin-top: 8px; }
        .logo { padding: 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .logo h2 { margin: 0; font-size: 22px; font-weight: 700; color: #f1f5f9; letter-spacing: -0.5px; }
        .logo span { color: #22c55e; }
        .card { background: #1e293b; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 24px; }
        .table th { color: #64748b; font-size: 12px; text-transform: uppercase; padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .table td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #f1f5f9; }
        .table tr:hover { background: rgba(255,255,255,0.02); }
        .btn { padding: 10px 20px; border-radius: 10px; font-weight: 500; border: none; cursor: pointer; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary { background: #22c55e; color: #0f172a; }
        .btn-primary:hover { background: #16a34a; }
        .btn-success { background: #10b981; color: #0f172a; }
        .btn-success:hover { background: #059669; }
        .btn-success.shadow-lg { box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3); }
        .shadow-success\\/20 { box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2); }
        .btn-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }
        .btn-outline { background: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2); }
        .btn-outline:hover { background: rgba(34,197,94,0.15); }
        .btn-sm { padding: 6px 12px; font-size: 13px; border-radius: 8px; }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        .badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
        .badge-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .badge-blue { background: rgba(56,189,248,0.15); color: #38bdf8; }
        .badge-purple { background: rgba(139,92,246,0.15); color: #8b5cf6; }
        .input { background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 14px; color: #f1f5f9; width: 100%; box-sizing: border-box; }
        .input:focus { outline: none; border-color: #22c55e; }
        .page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .page-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(34,197,94,0.15); display: flex; align-items: center; justify-content: center; color: #22c55e; font-size: 24px; }
        .page-title { font-size: 28px; font-weight: 700; margin: 0; }
        .page-subtitle { font-size: 14px; color: #94a3b8; margin: 4px 0 0 0; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .card-header-title { font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px; }

        /* Responsive Table */
        @media (max-width: 768px) {
            .responsive-table thead { display: none; }
            .responsive-table tr { display: block; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; background: rgba(255,255,255,0.02); padding: 8px; }
            .responsive-table td { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important; text-align: right !important; }
            .responsive-table td:last-child { border-bottom: 0 !important; }
            .responsive-table td::before { content: attr(data-label); font-weight: 600; color: #64748b; font-size: 11px; text-transform: uppercase; text-align: left; }

            .main-content > div { padding: 16px !important; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 8px; }
            .page-icon { width: 40px; height: 40px; font-size: 20px; }
            .page-title { font-size: 22px; }
        }

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
            .sidebar { transform: translateX(-100%); transition: 0.3s; z-index: 1001; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-toggle { display: flex !important; z-index: 1002; }
            .desktop-only { display: none !important; }
            #overlay { z-index: 1000 !important; }
            .topbar { padding: 12px 16px !important; }
            .user-info-name { display: none !important; }
            .user-info-role { display: none !important; }
            .user-avatar { margin-right: 0 !important; }
        }
        .mobile-toggle { display: none; }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Overlay для мобильного меню -->
    <div id="overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999;" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <h2>NURMAN <span>СКЛАД</span></h2>
        </div>

        {{-- Профиль пользователя в sidebar --}}
        <a href="{{ route('storekeeper.profile.index') }}" class="menu-item" style="margin: 8px; border: 1px solid rgba(34,197,94,0.2); background: rgba(34,197,94,0.05);">
            <div style="width: 36px; height: 36px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0f172a; font-weight: 700; font-size: 16px; margin-right: 12px; overflow: hidden;">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->full_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username, 0, 1)) }}
                @endif
            </div>
            <div style="display: flex; flex-direction: column; flex: 1;">
                <span style="color: #f1f5f9; font-size: 14px; font-weight: 600; line-height: 1.2;">{{ auth()->user()->full_name ?? auth()->user()->username }}</span>
                <span style="color: #94a3b8; font-size: 11px; line-height: 1.2;">{{ auth()->user()->role->display_name ?? 'Кладовщик' }}</span>
            </div>
            <i class="bi bi-chevron-right" style="margin-left: auto; font-size: 14px; color: #64748b;"></i>
        </a>

        <nav style="padding: 8px;">
            <div class="group-title">Главное</div>
            <a href="{{ route('storekeeper.dashboard') }}" class="menu-item {{ request()->routeIs('storekeeper.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Дашборд
            </a>

            <div class="group-title">Склад</div>
            <a href="{{ route('storekeeper.stock.index') }}" class="menu-item {{ request()->routeIs('storekeeper.stock.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Наличие
            </a>
            <a href="{{ route('storekeeper.products.index') }}" class="menu-item {{ request()->routeIs('storekeeper.products.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Товары
            </a>

            <div class="group-title">Операции</div>
            <a href="{{ route('storekeeper.inventory.index') }}" class="menu-item {{ request()->routeIs('storekeeper.inventory.*') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i> Инвентаризация
            </a>

            <div class="group-title" style="margin-top: 16px;"></div>
            <a href="{{ route('storekeeper.profile.index') }}" class="menu-item" style="margin-top: 8px;">
                <i class="bi bi-person"></i> Профиль
            </a>
            <button type="button" class="menu-item text-danger w-full" onclick="showLogoutModal()" style="margin-top: 4px;">
                <i class="bi bi-box-arrow-right"></i> Выйти
            </button>
        </nav>
    </aside>

    <!-- Mobile Toggle -->
    <button class="mobile-toggle" onclick="toggleSidebar()" style="position: fixed; top: 12px; left: 12px; z-index: 1002; background: #22c55e; border: none; border-radius: 8px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: #0f172a; cursor: pointer;">
        <i class="bi bi-list" style="font-size: 22px;"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <div style="padding: 24px;">
            @yield('content')
        </div>
    </main>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Выход из системы</h5>
                    <button type="button" class="btn-close" onclick="hideLogoutModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p style="color: #94a3b8;">Вы действительно хотите выйти?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="hideLogoutModal()">Отмена</button>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Выйти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('show');
            overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
        }

        function showLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.add('show');
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop';
            backdrop.onclick = hideLogoutModal;
            document.body.appendChild(backdrop);
        }

        function hideLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.remove('show');
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        }

        // Закрытие по ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideLogoutModal();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
