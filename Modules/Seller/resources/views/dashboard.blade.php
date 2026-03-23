@extends('core::layouts.master')

@section('page-title', 'Дашборд продавца')

@section('content')
<div class="row g-4">
    <!-- Stats -->
    <div class="col-12 col-md-4">
        <div class="stats-card">
            <div class="stats-card-icon">
                <i class="bi bi-cart"></i>
            </div>
            <div class="stats-card-body">
                <h4>{{ $todayCount }}</h4>
                <p>Продаж сегодня</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-4">
        <div class="stats-card">
            <div class="stats-card-icon" style="background: linear-gradient(135deg, #28a745, #218838);">
                <i class="bi bi-cash"></i>
            </div>
            <div class="stats-card-body">
                <h4>{{ number_format($todaySales, 0, '.', ' ') }} сом</h4>
                <p>Выручка сегодня</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-4">
        <div class="stats-card" style="cursor: pointer;" onclick="location.href='{{ route('seller.sale') }}'">
            <div class="stats-card-icon" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                <i class="bi bi-plus-circle"></i>
            </div>
            <div class="stats-card-body">
                <h4>Новая продажа</h4>
                <p>Нажмите для начала</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-2">
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('seller.sale') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4">
                <i class="bi bi-cart fs-1 text-primary mb-3"></i>
                <h5>Продажа</h5>
                <p class="text-muted small mb-0">Оформить продажу</p>
            </div>
        </a>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('seller.products') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4">
                <i class="bi bi-box fs-1 text-success mb-3"></i>
                <h5>Товары</h5>
                <p class="text-muted small mb-0">Посмотреть наличие</p>
            </div>
        </a>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('seller.coupons') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4">
                <i class="bi bi-ticket fs-1 text-warning mb-3"></i>
                <h5>Купоны</h5>
                <p class="text-muted small mb-0">Создать купон</p>
            </div>
        </a>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('seller.receipts') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4">
                <i class="bi bi-receipt fs-1 text-info mb-3"></i>
                <h5>Чеки</h5>
                <p class="text-muted small mb-0">История продаж</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Sales -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i>Последние продажи</span>
        <a href="{{ route('seller.receipts') }}" class="btn btn-sm btn-outline-primary">Все</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Время</th>
                        <th>Товаров</th>
                        <th>Сумма</th>
                        <th>Оплата</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $sale->transaction_number }}</span></td>
                            <td>{{ $sale->created_at->format('H:i') }}</td>
                            <td><span class="badge bg-info">{{ $sale->items_count }} шт</span></td>
                            <td><strong>{{ number_format($sale->total_amount, 0, '.', ' ') }} сом</strong></td>
                            <td>
                                @if($sale->payment_method === 'cash')
                                    <span class="badge bg-success"><i class="bi bi-cash"></i> Наличные</span>
                                @elseif($sale->payment_method === 'card')
                                    <span class="badge bg-primary"><i class="bi bi-credit-card"></i> Карта</span>
                                @else
                                    <span class="badge bg-info"><i class="bi bi-arrow-left-right"></i> Перевод</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-2">Пока нет продаж</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
