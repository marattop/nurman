@extends('layouts.admin')
@section('page-title', 'Журнал действий')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-journal-text"></i></div>
    <div>
        <h1 class="page-title">Журнал действий</h1>
        <p class="page-subtitle">История операций</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Последние действия</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Пользователь</th>
                    <th>Действие</th>
                    <th>Объект</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $activity)
                <tr>
                    <td data-label="Пользователь">
                        <div style="font-weight: 500;">{{ $activity->user?->full_name ?? '—' }}</div>
                    </td>
                    <td data-label="Действие" style="color: #94a3b8;">{{ $activity->description ?? '—' }}</td>
                    <td data-label="Объект" style="color: #94a3b8;">{{ $activity->subject_type ?? '—' }}</td>
                    <td data-label="Дата" style="color: #94a3b8;">{{ $activity->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; padding: 3rem; color: #64748b;">Записей не найдено</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
