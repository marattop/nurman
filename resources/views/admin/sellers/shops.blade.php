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
                        <div style="font-weight: 500;">{{ $seller->user?->full_name ?? '—' }}</div>
                        <div style="color: #94a3b8; font-size: 13px;">{{ $seller->user?->email ?? '' }}</div>
                    </td>
                    <td>
                        @if($seller->shops->count() > 0)
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @foreach($seller->shops as $shop)
                                    <span class="badge badge-blue">{{ $shop->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.sellers.edit', $seller->id) }}" class="btn btn-outline">
                            <i class="bi bi-pencil"></i> Ред.
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align: center; padding: 3rem; color: #64748b;">Продавцы не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
