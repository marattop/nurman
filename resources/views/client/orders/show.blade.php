@extends('layouts.app')

@section('title', 'Детали заказа - NURMAN')

@section('content')
<div class="container py-4">
    <h1>Заказ № {{ $order->transaction_number }}</h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Дата:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>Магазин:</strong> {{ $order->shop?->name ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Сумма:</strong> {{ number_format($order->total_amount, 0, ',', ' ') }} с</p>
                    @if($order->discount_amount > 0)
                        <p><strong>Скидка:</strong> {{ number_format($order->discount_amount, 0, ',', ' ') }} с</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <h3>Товары</h3>
    <table class="table table-dark">
        <thead>
            <tr>
                <th>Товар</th>
                <th>Кол-во</th>
                <th>Цена</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->variant?->product?->name ?? 'Товар' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 0, ',', ' ') }} с</td>
                <td>{{ number_format($item->subtotal, 0, ',', ' ') }} с</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <a href="{{ route('client.purchases.index') }}" class="btn btn-secondary">Назад к покупкам</a>
</div>
@endsection
