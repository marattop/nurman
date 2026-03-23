@extends('layouts.app')
@section('title', 'Магазины')
@section('content')
<style>
:root { --accent-primary: #10b981; --accent-blue: #3b82f6; --accent-red: #ef4444; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.08); --glass-bg: rgba(20, 20, 20, 0.85); --glass-border: rgba(255, 255, 255, 0.1); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.data-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--glass-border); }
.data-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); font-weight: 700; color: var(--text-main); }
.table th { padding: 14px 20px; font-size: 12px; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
.table td { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); color: var(--text-main); }
.btn { padding: 8px 16px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; }
.btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
.btn-danger { background: #fee2e2; color: #dc2626; }
.btn-outline { background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-main); }
small { color: var(--text-muted) !important; }
strong { color: var(--text-main); }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-size: 1.25rem;"><i class="bi bi-shop"></i></div>
        <div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">Магазины</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Управление магазинами</p></div>
    </div>
    <div class="data-card">
        <div class="data-card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="bi bi-list-ul me-2"></i>Список магазинов</span>
            <button class="btn btn-primary"><i class="bi bi-plus-lg"></i>Добавить</button>
        </div>
        <table class="table">
            <thead><tr><th>Название</th><th>Адрес</th><th>Продавец</th><th>Статус</th><th>Действия</th></tr></thead>
            <tbody>
                @forelse($shops as $shop)
                <tr>
                    <td><strong>{{ $shop->name }}</strong></td>
                    <td><small>{{ $shop->address ?? '—' }}</small></td>
                    <td><small>{{ $shop->seller?->full_name ?? 'Не назначен' }}</small></td>
                    <td>@if($shop->is_active)<span style="color: var(--accent-primary);">✓ Активен</span>@else<span style="color: var(--accent-red);">✗ Не активен</span>@endif</td>
                    <td style="display: flex; gap: 8px;">
                        <button class="btn btn-outline"><i class="bi bi-pencil"></i></button>
                        <form method="POST" style="display: inline;"><button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">Магазины не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
