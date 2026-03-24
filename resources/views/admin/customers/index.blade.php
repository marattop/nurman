@extends('layouts.admin')
@section('page-title', 'Клиенты')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-heart"></i></div>
    <div>
        <h1 class="page-title">Клиенты</h1>
        <p class="page-subtitle">Управление клиентами</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список клиентов</h3>
        <button class="btn btn-outline"><i class="bi bi-plus-lg"></i> Добавить</button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Имя</th>
                    <th>Телефон</th>
                    <th>Баланс</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $customer->name ?? '—' }}</div>
                    </td>
                    <td style="color: #94a3b8;">{{ $customer->phone ?? '—' }}</td>
                    <td>
                        @if(($customer->balance ?? 0) > 0)
                            <span style="color: #10b981; font-weight: 600;">{{ number_format($customer->balance, 2) }} сом</span>
                        @else
                            <span style="color: #94a3b8;">0 сом</span>
                        @endif
                    </td>
                    <td>
                        @if(($customer->is_gold ?? false))
                            <span class="badge badge-warning"><i class="bi bi-star-fill"></i> Gold</span>
                        @else
                            <span class="badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">Обычный</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-outline"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Клиенты не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
