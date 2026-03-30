@php
    $user = auth()->user();
    if (!$user) return;
    
    $items = [];

    if ($user->isAdmin()) {
        $items = [
            ['route' => 'admin.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Главная', 'active' => 'admin.dashboard'],
            ['route' => 'admin.products.index', 'icon' => 'bi-box-seam', 'label' => 'Товары', 'active' => 'admin.products.*'],
            ['route' => 'admin.qr.scan', 'icon' => 'bi-qr-code-scan', 'label' => 'Сканер', 'active' => 'admin.qr.scan', 'center' => true],
            ['route' => 'admin.reports.index', 'icon' => 'bi-graph-up', 'label' => 'Отчёты', 'active' => 'admin.reports.*'],
            ['route' => 'admin.profile.index', 'icon' => 'bi-person-circle', 'label' => 'Профиль', 'active' => 'admin.profile.*'],
        ];
    } elseif ($user->isSeller()) {
        $items = [
            ['route' => 'seller.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Главная', 'active' => 'seller.dashboard'],
            ['route' => 'seller.my-sales.index', 'icon' => 'bi-receipt', 'label' => 'История', 'active' => 'seller.my-sales.*'],
            ['route' => 'seller.pos.index', 'icon' => 'bi-cart3', 'label' => 'POS', 'active' => 'seller.pos.*', 'center' => true],
            ['route' => 'seller.customers.index', 'icon' => 'bi-people', 'label' => 'Клиенты', 'active' => 'seller.customers.*'],
            ['route' => 'seller.profile.index', 'icon' => 'bi-person-circle', 'label' => 'Профиль', 'active' => 'seller.profile.*'],
        ];
    } elseif ($user->isWarehouse()) {
        $items = [
            ['route' => 'warehouse.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Главная', 'active' => 'warehouse.dashboard'],
            ['route' => 'warehouse.products', 'icon' => 'bi-box-seam', 'label' => 'Товары', 'active' => 'warehouse.products*'],
            ['route' => 'admin.qr.scan', 'icon' => 'bi-qr-code-scan', 'label' => 'Сканер', 'active' => 'admin.qr.scan', 'center' => true],
            ['route' => 'warehouse.stock.index', 'icon' => 'bi-layers', 'label' => 'Остатки', 'active' => 'warehouse.stock*'],
            ['route' => 'warehouse.profile.index', 'icon' => 'bi-person-circle', 'label' => 'Профиль', 'active' => 'warehouse.profile*'],
        ];
    } elseif ($user->isOwner()) {
        $items = [
            ['route' => 'owner.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Главная', 'active' => 'owner.dashboard'],
            ['route' => 'owner.products', 'icon' => 'bi-box-seam', 'label' => 'Товары', 'active' => 'owner.products*'],
            ['route' => 'owner.scan-product', 'icon' => 'bi-qr-code-scan', 'label' => 'Сканер', 'active' => 'owner.scan-product', 'center' => true],
            ['route' => 'owner.reports.index', 'icon' => 'bi-graph-up', 'label' => 'Отчёты', 'active' => 'owner.reports*'],
            ['route' => 'admin.profile.index', 'icon' => 'bi-person-circle', 'label' => 'Профиль', 'active' => 'admin.profile*'],
        ];
    } elseif ($user->isClient()) {
        $items = [
            ['route' => 'client.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Главная', 'active' => 'client.dashboard'],
            ['route' => 'client.purchases.index', 'icon' => 'bi-bag-check', 'label' => 'Покупки', 'active' => 'client.purchases*'],
            ['route' => 'client.coupons.index', 'icon' => 'bi-ticket-perforated', 'label' => 'Купоны', 'active' => 'client.coupons*', 'center' => true],
            ['route' => 'client.notifications', 'icon' => 'bi-bell', 'label' => 'Уведом.', 'active' => 'client.notifications*'],
            ['route' => 'client.profile.index', 'icon' => 'bi-person-circle', 'label' => 'Профиль', 'active' => 'client.profile*'],
        ];
    }
@endphp

@if(!empty($items))
<div class="bottom-nav">
    @foreach($items as $item)
        @php
            $isActive = false;
            if (isset($item['active'])) {
                $isActive = request()->routeIs($item['active']) || request()->is(trim(str_replace('*', '', $item['active']), '/.') . '*');
            }
            $url = '#';
            try {
                if (isset($item['route'])) {
                    $url = route($item['route']);
                } elseif (isset($item['url'])) {
                    $url = url($item['url']);
                }
            } catch (\Exception $e) {
                $url = '#';
            }
        @endphp
        
        <a href="{{ $url }}" class="bottom-nav-item {{ $isActive ? 'active' : '' }}">
            @if(isset($item['center']) && $item['center'])
                <div class="bottom-nav-center">
                    <i class="bi {{ $item['icon'] }}"></i>
                </div>
            @else
                <i class="bi {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            @endif
        </a>
    @endforeach
</div>

<style>
    .bottom-nav { 
        display: none; 
        position: fixed; 
        bottom: 0; 
        left: 0; 
        right: 0; 
        height: 64px; 
        background: #0f172a; 
        border-top: 1px solid rgba(255,255,255,0.05); 
        z-index: 1000; 
        padding: 0 4px; 
        align-items: center;
        justify-content: space-around;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.4);
    }
    .bottom-nav-item { 
        flex: 1; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        color: #94a3b8; 
        text-decoration: none; 
        font-size: 10px; 
        gap: 3px; 
        transition: 0.2s; 
        height: 100%;
        min-width: 0;
        padding-top: 4px;
    }
    .bottom-nav-item i { font-size: 20px; line-height: 1; }
    .bottom-nav-item span { 
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis; 
        width: 100%; 
        text-align: center;
        padding: 0 2px;
    }
    .bottom-nav-item.active { color: #38bdf8; }
    .bottom-nav-item.active i { transform: translateY(-2px); transition: 0.3s; }
    
    .bottom-nav-center { 
        width: 54px; 
        height: 54px; 
        background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%); 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        color: #0f172a; 
        font-size: 24px; 
        margin-top: -28px; 
        border: 4px solid #1e293b; 
        box-shadow: 0 4px 15px rgba(56,189,248,0.4); 
        transition: 0.3s;
    }
    .bottom-nav-item.active .bottom-nav-center {
        background: #f1f5f9;
        color: #0ea5e9;
        transform: scale(1.05);
    }
    
    @media (max-width: 768px) {
        body { padding-bottom: 64px !important; }
        .bottom-nav { display: flex; }
        
        /* Скрываем другие навигации если они мешают */
        .desktop-only { display: none !important; }
    }
</style>
@endif
