@extends('core::layouts.master')

@section('page-title', 'Мой профиль')

@section('content')
@if($profile)
<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-12 col-lg-4">
        <div class="card text-center">
            <div class="card-body">
                <div style="width: 100px; height: 100px; margin: 0 auto 1rem; background: linear-gradient(135deg, #FF6B35, #E85A2B); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 3rem; color: white; font-weight: bold;">
                        {{ substr($profile->user->full_name ?? $profile->user->username, 0, 1) }}
                    </span>
                </div>
                <h4>{{ $profile->user->full_name ?? $profile->user->username }}</h4>
                <p class="text-muted">{{ $profile->user->phone }}</p>
                
                <div class="alert alert-info">
                    <strong>🎯 {{ $profile->loyalty_level }}</strong>
                    <br><small>Скидка: {{ $profile->loyalty_discount_percent }}%</small>
                </div>
                
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded">
                            <h5 class="text-primary mb-0">{{ $profile->bonus_points }}</h5>
                            <small class="text-muted">Бонусы</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded">
                            <h5 class="text-success mb-0">{{ number_format($profile->total_purchases, 0, '.', ' ') }} с</h5>
                            <small class="text-muted">Покупки</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Coupon -->
    <div class="col-12 col-lg-8">
        @if($activeCoupon)
            <div class="card border-warning mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-ticket me-2"></i>Ваш активный купон
                </div>
                <div class="card-body text-center">
                    <h3 class="mb-3">{{ $activeCoupon->code }}</h3>
                    <div class="alert alert-light d-inline-block">
                        <strong>Скидка:</strong> 
                        @if($activeCoupon->discount_type === 'percent')
                            {{ $activeCoupon->discount_value }}%
                        @elseif($activeCoupon->discount_type === 'fixed')
                            {{ $activeCoupon->discount_value }} сом
                        @else
                            Оптовая цена
                        @endif
                    </div>
                    <br>
                    <small class="text-muted">
                        @if($activeCoupon->valid_until)
                            Действует до {{ $activeCoupon->valid_until->format('d.m.Y') }}
                        @endif
                    </small>
                </div>
            </div>
        @else
            <div class="card mb-4">
                <div class="card-body text-center text-muted">
                    <i class="bi bi-ticket" style="font-size: 3rem;"></i>
                    <p class="mt-2">У вас пока нет активных купонов</p>
                    <p class="small">Попросите продавца создать для вас купон на скидку</p>
                </div>
            </div>
        @endif
        
        <!-- Recent Purchases -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bag me-2"></i>Последние покупки</span>
                <a href="{{ route('client.purchases') }}" class="btn btn-sm btn-outline-primary">Все</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($recentPurchases as $purchase)
                        <a href="{{ route('client.purchases.show', $purchase->transaction_id) }}" class="list-group-item list-group-item-action px-0">
                            <div class="d-flex justify-content-between">
                                <strong>{{ number_format($purchase->amount, 0, '.', ' ') }} сом</strong>
                                <small class="text-muted">{{ $purchase->created_at->format('d.m.Y H:i') }}</small>
                            </div>
                            <small class="text-muted">
                                @if($purchase->bonus_earned > 0)
                                    <span class="text-success">+{{ $purchase->bonus_earned }} бонусов</span>
                                @endif
                            </small>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Пока нет покупок</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Профиль не найден. Обратитесь к администратору.
</div>
@endif

<!-- Notifications -->
@if($notifications->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-bell me-2"></i>Уведомления
    </div>
    <div class="card-body">
        <div class="list-group list-group-flush">
            @foreach($notifications as $notification)
                <div class="list-group-item {{ $notification->is_read ? '' : 'bg-light' }}">
                    <div class="d-flex justify-content-between">
                        <strong>
                            @if($notification->type === 'coupon')
                                <i class="bi bi-ticket text-primary"></i>
                            @elseif($notification->type === 'sale')
                                <i class="bi bi-bag text-success"></i>
                            @elseif($notification->type === 'birthday')
                                <i class="bi bi-gift text-warning"></i>
                            @else
                                <i class="bi bi-bell text-info"></i>
                            @endif
                            {{ $notification->title }}
                        </strong>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 mt-1">{{ $notification->message }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
