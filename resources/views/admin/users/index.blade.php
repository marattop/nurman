@extends('layouts.admin')
@section('page-title', 'Сотрудники')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-people"></i></div>
    <div>
        <h1 class="page-title">Сотрудники</h1>
        <p class="page-subtitle">Управление командой</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список сотрудников</h3>
        <button class="btn btn-outline"><i class="bi bi-plus-lg"></i> Добавить</button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Имя</th>
                    <th>Роль</th>
                    <th>Телефон</th>
                    <th>Магазин</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $user->full_name ?? '—' }}</div>
                        <div style="color: #94a3b8; font-size: 13px;">{{ $user->email ?? '' }}</div>
                    </td>
                    <td>
                        @if($user->isOwner())
                            <span class="badge badge-blue">Владелец</span>
                        @elseif($user->isAdmin())
                            <span class="badge badge-blue">Админ</span>
                        @elseif($user->isSeller())
                            <span class="badge badge-success">Продавец</span>
                        @elseif($user->isWarehouse())
                            <span class="badge badge-warning">Склад</span>
                        @else
                            <span class="badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">Пользователь</span>
                        @endif
                    </td>
                    <td style="color: #94a3b8;">{{ $user->phone ?? '—' }}</td>
                    <td style="color: #94a3b8;">{{ $user->shop?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline">
                            <i class="bi bi-pencil"></i> Ред.
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Сотрудники не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
