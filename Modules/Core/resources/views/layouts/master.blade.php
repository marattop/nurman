<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'NURMAN') - Система управления магазином</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            /* Color Palette - NURMAN Brand */
            --primary-50: #fff5f0;
            --primary-100: #ffe3d6;
            --primary-200: #ffc7ad;
            --primary-300: #ffa47a;
            --primary-400: #ff7d47;
            --primary-500: #FF6B35;
            --primary-600: #E85A2B;
            --primary-700: #C94A1F;
            --primary-800: #A83A16;
            --primary-900: #872A0F;
            
            --neutral-0: #ffffff;
            --neutral-50: #fafbfc;
            --neutral-100: #f4f6f8;
            --neutral-200: #e8ecf0;
            --neutral-300: #d1d8e0;
            --neutral-400: #9aa5b3;
            --neutral-500: #6b7785;
            --neutral-600: #4a5568;
            --neutral-700: #2d3748;
            --neutral-800: #1a202c;
            --neutral-900: #0d1117;
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #FF6B35 0%, #E85A2B 50%, #C94A1F 100%);
            --gradient-primary-soft: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(232, 90, 43, 0.1) 100%);
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px 0 rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 24px 0 rgba(0, 0, 0, 0.12);
            --shadow-glow: 0 0 20px rgba(255, 107, 53, 0.4);
            
            /* Border Radius */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--neutral-50);
            color: var(--neutral-800);
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background: var(--neutral-0);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            background: var(--gradient-primary);
            color: white;
            text-align: center;
        }
        
        .sidebar-brand h3 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.02em;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
            flex: 1;
            overflow-y: auto;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: var(--neutral-600);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: var(--gradient-primary-soft);
            color: var(--primary-600);
            border-left-color: var(--primary-500);
        }
        
        .sidebar-menu li a i {
            width: 20px;
            text-align: center;
        }
        
        .sidebar-user {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--neutral-200);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: var(--neutral-50);
        }
        
        /* Topbar */
        .topbar {
            background: var(--neutral-0);
            padding: 1rem 2rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .topbar h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            background: var(--neutral-0);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--neutral-200);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }
        
        /* Stats Cards */
        .stats-card {
            background: var(--neutral-0);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .stats-card-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            color: white;
        }
        
        .stats-card-body h4 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--neutral-800);
        }
        
        .stats-card-body p {
            margin: 0;
            color: var(--neutral-500);
            font-size: 0.875rem;
        }
        
        /* Tables */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: var(--neutral-100);
            border: none;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--neutral-600);
        }
        
        .table tbody td {
            padding: 0.875rem 1rem;
            vertical-align: middle;
            border-color: var(--neutral-200);
        }
        
        /* Badges */
        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: var(--radius-full);
            font-weight: 500;
            font-size: 0.75rem;
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .topbar {
                padding: 1rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @auth
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h3><i class="fas fa-tshirt"></i> NURMAN</h3>
        </div>
        
        <ul class="sidebar-menu">
            @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                <li><a href="{{ route('owner.dashboard') }}" class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Дашборд</a></li>
                <li><a href="{{ route('owner.analytics') }}" class="{{ request()->routeIs('owner.analytics*') ? 'active' : '' }}"><i class="bi bi-graph-up"></i> Аналитика</a></li>
                <li><a href="{{ route('owner.customers') }}" class="{{ request()->routeIs('owner.customers*') ? 'active' : '' }}"><i class="bi bi-people"></i> Клиенты</a></li>
                <li><a href="{{ route('owner.products') }}" class="{{ request()->routeIs('owner.products*') ? 'active' : '' }}"><i class="bi bi-box"></i> Товары</a></li>
                <li><a href="{{ route('owner.shops') }}" class="{{ request()->routeIs('owner.shops*') ? 'active' : '' }}"><i class="bi bi-shop"></i> Магазины</a></li>
                <li><a href="{{ route('owner.users') }}" class="{{ request()->routeIs('owner.users*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Сотрудники</a></li>
                <li><a href="{{ route('owner.coupons') }}" class="{{ request()->routeIs('owner.coupons*') ? 'active' : '' }}"><i class="bi bi-ticket"></i> Купоны</a></li>
                <li><a href="{{ route('owner.expenses') }}" class="{{ request()->routeIs('owner.expenses*') ? 'active' : '' }}"><i class="bi bi-cash-coin"></i> Расходы</a></li>
                <li><a href="{{ route('owner.reports') }}" class="{{ request()->routeIs('owner.reports*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Отчёты</a></li>
            @elseif(auth()->user()->isSeller())
                <li><a href="{{ route('seller.dashboard') }}" class="{{ request()->routeIs('seller.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Дашборд</a></li>
                <li><a href="{{ route('seller.sale') }}" class="{{ request()->routeIs('seller.sale*') ? 'active' : '' }}"><i class="bi bi-cart"></i> Продажа</a></li>
                <li><a href="{{ route('seller.products') }}" class="{{ request()->routeIs('seller.products*') ? 'active' : '' }}"><i class="bi bi-box"></i> Товары</a></li>
                <li><a href="{{ route('seller.coupons') }}" class="{{ request()->routeIs('seller.coupons*') ? 'active' : '' }}"><i class="bi bi-ticket"></i> Купоны</a></li>
                <li><a href="{{ route('seller.receipts') }}" class="{{ request()->routeIs('seller.receipts*') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Чеки</a></li>
                <li><a href="{{ route('seller.customers') }}" class="{{ request()->routeIs('seller.customers*') ? 'active' : '' }}"><i class="bi bi-people"></i> Клиенты</a></li>
            @elseif(auth()->user()->isWarehouse())
                <li><a href="{{ route('warehouse.dashboard') }}" class="{{ request()->routeIs('warehouse.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Дашборд</a></li>
                <li><a href="{{ route('warehouse.products') }}" class="{{ request()->routeIs('warehouse.products*') ? 'active' : '' }}"><i class="bi bi-box"></i> Товары</a></li>
                <li><a href="{{ route('warehouse.stock') }}" class="{{ request()->routeIs('warehouse.stock*') ? 'active' : '' }}"><i class="bi bi-layers"></i> Остатки</a></li>
                <li><a href="{{ route('warehouse.purchases') }}" class="{{ request()->routeIs('warehouse.purchases*') ? 'active' : '' }}"><i class="bi bi-truck"></i> Закупки</a></li>
                @if(auth()->user()->can('warehouse.qr'))
                    <li><a href="{{ route('warehouse.qrcodes.index') }}" class="{{ request()->routeIs('warehouse.qrcodes*') ? 'active' : '' }}"><i class="bi bi-printer"></i> Печать QR</a></li>
                @endif
                <li><a href="{{ route('warehouse.inventory') }}" class="{{ request()->routeIs('warehouse.inventory*') ? 'active' : '' }}"><i class="bi bi-clipboard-check"></i> Инвентаризация</a></li>
            @elseif(auth()->user()->isClient())
                <li><a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Главная</a></li>
                <li><a href="{{ route('client.profile') }}" class="{{ request()->routeIs('client.profile*') ? 'active' : '' }}"><i class="bi bi-person"></i> Профиль</a></li>
                <li><a href="{{ route('client.purchases') }}" class="{{ request()->routeIs('client.purchases*') ? 'active' : '' }}"><i class="bi bi-bag"></i> Покупки</a></li>
                <li><a href="{{ route('client.coupons') }}" class="{{ request()->routeIs('client.coupons*') ? 'active' : '' }}"><i class="bi bi-ticket"></i> Купоны</a></li>
            @endif
        </ul>
        
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ substr(auth()->user()->full_name ?? auth()->user()->username, 0, 1) }}
            </div>
            <div class="sidebar-user-info">
                <div class="fw-semibold">{{ auth()->user()->full_name ?? auth()->user()->username }}</div>
                <div class="text-muted small">{{ auth()->user()->role->display_name ?? '' }}</div>
            </div>
        </div>
    </aside>
    @endauth

    <!-- Main Content -->
    <main class="main-content">
        @auth
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <h1>@yield('page-title', 'Панель управления')</h1>
            </div>
            
            <div class="topbar-actions">
                <div class="dropdown">
                    <button class="btn btn-link" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        @if(auth()->user()->notifications()->unread()->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ auth()->user()->notifications()->unread()->count() }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @forelse(auth()->user()->notifications()->limit(5)->get() as $notification)
                            <li><a class="dropdown-item" href="{{ route('client.notifications.read', $notification) }}">{{ $notification->title }}</a></li>
                        @empty
                            <li><span class="dropdown-item text-muted">Нет уведомлений</span></li>
                        @endforelse
                    </ul>
                </div>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Выход
                    </button>
                </form>
            </div>
        </div>
        @endauth

        <!-- Page Content -->
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
