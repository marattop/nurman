@extends('seller::layouts.seller')
@section('page-title', 'Клиенты')
@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div class="page-icon"><i class="bi bi-people"></i></div>
        <div>
            <h1 class="page-title">Клиенты</h1>
            <p class="page-subtitle">База клиентов магазина</p>
        </div>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05);">
        <form action="{{ route('seller.customers') }}" method="GET" style="flex: 1; max-width: 400px; position: relative;">
            <i class="bi bi-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Поиск по имени или телефону..." style="padding-left: 40px; height: 40px;">
        </form>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 20px;">Клиент</th>
                    <th style="text-align: left; padding: 12px 20px;">Телефон</th>
                    <th style="text-align: center; padding: 12px 20px;">Скидка (%)</th>
                    <th style="text-align: center; padding: 12px 20px;">Бонусы</th>
                    <th style="text-align: right; padding: 12px 20px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                    <td style="padding: 12px 20px;">
                        <div style="font-weight: 600; color: #f1f5f9;">{{ $customer->full_name }}</div>
                        <div style="font-size: 12px; color: #64748b;">{{ $customer->email }}</div>
                    </td>
                    <td style="padding: 12px 20px; color: #94a3b8;">{{ $customer->phone }}</td>
                    <td style="padding: 12px 20px; text-align: center;">
                        <span class="badge-success">{{ $customer->customerProfile->loyalty_discount_percent ?? 0 }}%</span>
                    </td>
                    <td style="padding: 12px 20px; text-align: center; color: #38bdf8; font-weight: 600;">
                        {{ number_format($customer->customerProfile->bonus_points ?? 0, 0) }}
                    </td>
                    <td style="padding: 12px 20px; text-align: right;">
                        <a href="{{ route('seller.customers.show', $customer->id) }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 13px;">
                            <i class="bi bi-eye"></i> Просмотр
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 48px; color: #64748b;">
                        <i class="bi bi-people" style="font-size: 48px; opacity: 0.1; display: block; margin-bottom: 16px;"></i>
                        Клиенты не найдены
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($customers->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05);">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
