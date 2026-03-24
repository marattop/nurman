@extends('seller::layouts.seller')
@section('page-title', 'Клиенты')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-people"></i></div>
    <div>
        <h1 class="page-title">Клиенты</h1>
        <p class="page-subtitle">База клиентов</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #10b981;"></i> Список клиентов</h3>
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
                        <div style="font-weight: 500;">{{ $customer->full_name ?? '—' }}</div>
                        <div style="color: #94a3b8; font-size: 13px;">{{ $customer->email ?? '' }}</div>
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
                        <button class="btn btn-outline" onclick="alert('Функция просмотра будет доступна позже')">
                            <i class="bi bi-eye"></i> Просмотр
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Клиенты не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($customers->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
        <div style="color: #94a3b8; font-size: 13px;">Показано {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} из {{ $customers->total() }}</div>
        <div style="display: flex; gap: 8px;">
            @if($customers->onFirstPage())
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">← Назад</span>
            @else
                <a href="{{ $customers->previousPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #10b981; text-decoration: none; font-size: 14px;">← Назад</a>
            @endif
            @if($customers->hasMorePages())
                <a href="{{ $customers->nextPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #10b981; text-decoration: none; font-size: 14px;">Вперёд →</a>
            @else
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">Вперёд →</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
