@extends('layouts.app')
@section('title', 'Журнал действий')
@section('content')
<style>
:root { --accent-primary: #10b981; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.08); --glass-bg: rgba(20, 20, 20, 0.85); --glass-border: rgba(255, 255, 255, 0.1); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.data-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--glass-border); }
.data-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); font-weight: 700; color: var(--text-main); }
.table th { padding: 14px 20px; font-size: 12px; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
.table td { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); color: var(--text-main); }
small { color: var(--text-muted) !important; }
strong { color: var(--text-main); }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-size: 1.25rem;"><i class="bi bi-journal-text"></i></div>
        <div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">Журнал действий</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">История операций</p></div>
    </div>
    <div class="data-card">
        <div class="data-card-header"><i class="bi bi-list-ul me-2"></i>Последние действия</div>
        <table class="table">
            <thead><tr><th>Пользователь</th><th>Действие</th><th>Объект</th><th>Дата</th></tr></thead>
            <tbody>
                @forelse($logs as $activity)
                <tr>
                    <td><strong>{{ $activity->user?->full_name ?? '—' }}</strong></td>
                    <td><small>{{ $activity->description ?? '—' }}</small></td>
                    <td><small>{{ $activity->subject_type ?? '—' }}</small></td>
                    <td><small>{{ $activity->created_at->format('d.m.Y H:i') }}</small></td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-muted);">Записей нет</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
