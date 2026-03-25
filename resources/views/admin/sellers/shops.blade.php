@extends('layouts.admin')
@section('page-title', 'Продавцы')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-person-badge"></i></div>
    <div>
        <h1 class="page-title">Продавцы</h1>
        <p class="page-subtitle">Привязка к магазинам</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список продавцов</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignShopModal">
            <i class="bi bi-plus-lg"></i> Добавить
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Имя</th>
                    <th>Магазины</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $seller)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $seller->full_name ?? '—' }}</div>
                        <div style="color: #94a3b8; font-size: 13px;">{{ $seller->email ?? '' }}</div>
                    </td>
                    <td>
                        @if($seller->shop)
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                <span class="badge badge-blue">{{ $seller->shop->name }}</span>
                            </div>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.sellers.shops.detach', $seller->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline" onclick="return confirm('Отвязать сотрудника от магазина?')">
                                <i class="bi bi-x-lg"></i> Отвязать
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align: center; padding: 3rem; color: #64748b;">Продавцы не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Модальное окно для привязки к магазину -->
<div class="modal fade" id="assignShopModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.sellers.shops.assign') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Привязка к магазину</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Сотрудник</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Выберите сотрудника</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->role->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Магазин</label>
                        <select name="shop_id" class="form-select">
                            <option value="">Без магазина</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
