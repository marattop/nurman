@extends('layouts.app')

@section('title', 'Заказы - NURMAN')

@section('content')
<div class="container py-4">
    <h1>История заказов</h1>
    
    @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>№ заказа</th>
                        <th>Дата</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->transaction_number }}</td>
                        <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ number_format($order->total_amount, 0, ',', ' ') }} с</td>
                        <td>
                            @if($order->type === 'sale')
                                <span class="badge bg-success">Завершён</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->type }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('client.purchases.show', $order->id) }}" class="btn btn-sm btn-primary">
                                Подробнее
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{ $orders->links() }}
    @else
        <div class="alert alert-info">
            У вас пока нет заказов
        </div>
    @endif
    
    <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">На главную</a>
</div>
@endsection
