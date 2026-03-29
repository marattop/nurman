@extends('seller::layouts.seller')
@section('page-title', 'Профиль клиента')
@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('seller.customers') }}" style="color: #94a3b8; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-arrow-left"></i> Назад к списку
    </a>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px;">
    <!-- Инфо о клиенте -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <div class="card">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; background: rgba(16,185,129,0.1); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 16px;">
                    {{ mb_substr($customer->full_name, 0, 1) }}
                </div>
                <h2 style="margin: 0 0 4px; font-size: 20px;">{{ $customer->full_name }}</h2>
                <div style="color: #94a3b8; font-size: 14px;">{{ $customer->phone }}</div>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Дата регистрации:</span>
                    <span style="color: #f1f5f9;">{{ $customer->created_at->format('d.m.Y') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Скидка:</span>
                    <span class="badge-success">{{ $customer->customerProfile->loyalty_discount_percent ?? 0 }}%</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Бонусы:</span>
                    <span style="color: #38bdf8; font-weight: 600;">{{ number_format($customer->customerProfile->bonus_points ?? 0, 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- История покупок -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="card-header" style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <h3 class="card-header-title">Последние покупки</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 12px 20px;">Дата</th>
                        <th style="text-align: left; padding: 12px 20px;">ID Заказа</th>
                        <th style="text-align: right; padding: 12px 20px;">Сумма</th>
                        <th style="text-align: right; padding: 12px 20px;">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->transactions as $transaction)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                        <td style="padding: 12px 20px; color: #94a3b8;">{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                        <td style="padding: 12px 20px; font-weight: 500;">#{{ $transaction->id }}</td>
                        <td style="padding: 12px 20px; text-align: right; font-weight: 600; color: #10b981;">
                            {{ number_format($transaction->total_amount, 0, '.', ' ') }} сом
                        </td>
                        <td style="padding: 12px 20px; text-align: right;">
                            <span class="badge-success">Успешно</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 48px; color: #64748b;">Покупок пока нет</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
