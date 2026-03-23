@extends('core::layouts.master')

@section('page-title', 'Дашборд склада')

@section('content')
<div class="row g-4">
    <!-- Stats -->
    <div class="col-12 col-md-3">
        <div class="stats-card">
            <div class="stats-card-icon">
                <i class="bi bi-box"></i>
            </div>
            <div class="stats-card-body">
                <h4>{{ $totalProducts }}</h4>
                <p>Товаров всего</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="stats-card">
            <div class="stats-card-icon" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stats-card-body">
                <h4 class="text-danger">{{ $lowStock }}</h4>
                <p>Заканчиваются</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="stats-card" style="cursor: pointer;" onclick="location.href='{{ route('warehouse.products.create') }}'">
            <div class="stats-card-icon" style="background: linear-gradient(135deg, #28a745, #218838);">
                <i class="bi bi-plus-circle"></i>
            </div>
            <div class="stats-card-body">
                <h4>Добавить</h4>
                <p>Новый товар</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="stats-card" style="cursor: pointer;" onclick="location.href='{{ route('warehouse.purchases.create') }}'">
            <div class="stats-card-icon" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                <i class="bi bi-truck"></i>
            </div>
            <div class="stats-card-body">
                <h4>Закупка</h4>
                <p>Пополнить склад</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-2">
    <div class="col-12 col-md-6 col-lg-2">
        <a href="{{ route('warehouse.products') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-3">
                <i class="bi bi-box fs-1 text-primary mb-2"></i>
                <h6>Товары</h6>
            </div>
        </a>
    </div>
    
    <div class="col-12 col-md-6 col-lg-2">
        <a href="{{ route('warehouse.stock') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-3">
                <i class="bi bi-layers fs-1 text-success mb-2"></i>
                <h6>Остатки</h6>
            </div>
        </a>
    </div>
    
    <div class="col-12 col-md-6 col-lg-2">
        <a href="{{ route('warehouse.purchases') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-3">
                <i class="bi bi-truck fs-1 text-info mb-2"></i>
                <h6>Закупки</h6>
            </div>
        </a>
    </div>
    
    <div class="col-12 col-md-6 col-lg-2">
        <a href="{{ route('warehouse.qrcodes') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-3">
                <i class="bi bi-qr-code fs-1 text-warning mb-2"></i>
                <h6>QR-коды</h6>
            </div>
        </a>
    </div>
    
    <div class="col-12 col-md-6 col-lg-2">
        <a href="{{ route('warehouse.inventory') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-3">
                <i class="bi bi-clipboard-check fs-1 text-danger mb-2"></i>
                <h6>Инвентаризация</h6>
            </div>
        </a>
    </div>
    
    <div class="col-12 col-md-6 col-lg-2">
        <a href="{{ route('warehouse.shops') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-3">
                <i class="bi bi-shop fs-1 text-secondary mb-2"></i>
                <h6>Магазины</h6>
            </div>
        </a>
    </div>
</div>

<!-- Recent Purchases & New Products -->
<div class="row g-4 mt-2">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-truck me-2"></i>Последние закупки</span>
                <a href="{{ route('warehouse.purchases') }}" class="btn btn-sm btn-outline-primary">Все</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($recentPurchases as $purchase)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <strong>{{ $purchase->variant->product->name }}</strong>
                                <br><small class="text-muted">
                                    {{ $purchase->variant->size }} {{ $purchase->variant->color }} • 
                                    +{{ $purchase->quantity }} шт • 
                                    {{ number_format($purchase->total_amount, 0, '.', ' ') }} сом
                                </small>
                            </div>
                            <span class="badge bg-success">+{{ $purchase->quantity }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Нет закупок</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-plus-circle me-2"></i>Новые товары</span>
                <a href="{{ route('warehouse.products') }}" class="btn btn-sm btn-outline-primary">Все</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($newProducts as $product)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $product->name }}</strong>
                                <span class="text-muted">{{ $product->article }}</span>
                            </div>
                            <small class="text-muted">
                                {{ $product->category->name ?? 'Без категории' }} • 
                                Создал: {{ $product->createdBy->full_name ?? 'Система' }}
                            </small>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Нет товаров</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
