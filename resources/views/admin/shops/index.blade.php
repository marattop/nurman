@extends('layouts.admin')
@section('page-title', 'Магазины')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-shop"></i></div>
    <div>
        <h1 class="page-title">Магазины</h1>
        <p class="page-subtitle">Управление магазинами</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список магазинов</h3>
        <button class="btn btn-primary"><i class="bi bi-plus-lg"></i> Добавить</button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Название</th>
                    <th>Адрес</th>
                    <th>Телефон</th>
                    <th>Продавцы</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shops as $shop)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $shop->name }}</div>
                    </td>
                    <td style="color: #94a3b8;">{{ $shop->address ?? '—' }}</td>
                    <td style="color: #94a3b8;">{{ $shop->phone ?? '—' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ $shop->sellers_count ?? 0 }} чел.</span>
                    </td>
                    <td>
                        <button class="btn btn-outline"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Магазины не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
