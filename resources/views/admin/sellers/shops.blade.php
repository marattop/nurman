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
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
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
                    <td data-label="Имя">
                        <div style="font-weight: 500;">{{ $seller->full_name ?? '—' }}</div>
                        <div style="color: #94a3b8; font-size: 13px;">{{ $seller->email ?? '' }}</div>
                    </td>
                    <td data-label="Магазины">
                        @if($seller->shop)
                            <div style="display: flex; flex-wrap: wrap; gap: 4px; justify-content: flex-end; md:justify-content: flex-start;">
                                <span class="badge badge-blue">{{ $seller->shop->name }}</span>
                            </div>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td data-label="Действия">
                        <form action="{{ route('admin.sellers.shops.detach', $seller->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline w-full justify-center" onclick="return confirm('Отвязать сотрудника от магазина?')">
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.sellers.shops.assign') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-shop"></i> Привязка к магазину</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label" style="display:block; margin-bottom:8px; font-weight:600; color:#94a3b8;">Сотрудник</label>
                        <select name="employee_id" class="input" required>
                            <option value="">Выберите сотрудника</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->role->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="display:block; margin-bottom:8px; font-weight:600; color:#94a3b8;">Магазин</label>
                        <select name="shop_id" class="input">
                            <option value="">Без магазина</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer flex flex-col sm:flex-row gap-2">
                    <button type="button" class="btn btn-outline w-full sm:flex-1 justify-center" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary w-full sm:flex-1 justify-center">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
