@extends('layouts.app')

@section('title', 'Детали покупки - NURMAN')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📦 Покупка № {{ $purchase->transaction_number }}</h1>
        <div>
            <a href="{{ route('client.purchases.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
            <a href="{{ route('client.purchases.receipt', $purchase->id) }}" class="btn btn-primary" target="_blank">
                <i class="bi bi-printer"></i> Печать чека
            </a>
        </div>
    </div>

    <div class="card mb-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>📅 Дата:</strong> {{ $purchase->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>🏪 Магазин:</strong> {{ $purchase->shop?->name ?? '—' }}</p>
                    <p><strong>👤 Продавец:</strong> {{ $purchase->user?->full_name ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>💰 Сумма:</strong> <span style="color: var(--primary); font-size: 1.5rem; font-weight: 700;">{{ number_format($purchase->total_amount, 0, ',', ' ') }} с</span></p>
                    @if($purchase->discount_amount > 0)
                        <p><strong>🎉 Скидка:</strong> {{ number_format($purchase->discount_amount, 0, ',', ' ') }} с</p>
                    @endif
                    <p><strong>💳 Оплата:</strong> 
                        @if($purchase->payment_method === 'cash') Наличные
                        @elseif($purchase->payment_method === 'card') Карта
                        @else Перевод @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mb-3">📦 Товары</h3>
    <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <table class="table table-dark mb-0">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                <tr>
                    <td>
                        {{ $item->variant?->product?->name ?? 'Товар' }}
                        @if($item->variant?->size || $item->variant?->color)
                            <small class="text-muted">
                                @if($item->variant?->size) ({{ $item->variant->size }}) @endif
                            </small>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 0, ',', ' ') }} с</td>
                    <td>{{ number_format($item->subtotal, 0, ',', ' ') }} с</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
