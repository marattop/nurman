@extends('layouts.admin')
@section('page-title', 'Журнал действий персонала')
@section('page-subtitle', 'Действия кладовщиков и продавцов')

@section('content')

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display: grid; gap: 24px;">
    {{-- Фильтры --}}
    <div class="card" style="padding: 16px 20px;">
        <form action="{{ route('admin.staff-logs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:flex-1">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">Пользователь</label>
                <select name="user_id" class="input w-full">
                    <option value="">Все пользователи</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->username }} ({{ $user->role?->name ?? '—' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:flex-1">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">Действие</label>
                <select name="action" class="input w-full">
                    <option value="">Все действия</option>
                    @foreach($actions as $key => $label)
                        <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:flex-1">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">С даты</label>
                <input type="date" name="date_from" class="input w-full" value="{{ request('date_from') }}">
            </div>
            <div class="w-full sm:flex-1">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8;">По дату</label>
                <input type="date" name="date_to" class="input w-full" value="{{ request('date_to') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary justify-center">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('admin.staff-logs.export', request()->query()) }}" class="btn btn-outline justify-center" title="Экспорт CSV">
                    <i class="bi bi-download"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Кнопка очистки --}}
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0;"><i class="bi bi-journal-text" style="color: #38bdf8;"></i> Записи журнала</h3>
        <button onclick="showClearModal()" class="btn btn-danger btn-sm">
            <i class="bi bi-trash"></i> Очистить журнал
        </button>
    </div>

    {{-- Таблица --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 12px 16px;">Дата/Время</th>
                        <th style="text-align: left; padding: 12px 16px;">Пользователь</th>
                        <th style="text-align: left; padding: 12px 16px;">Роль</th>
                        <th style="text-align: left; padding: 12px 16px;">Действие</th>
                        <th style="text-align: left; padding: 12px 16px;">Описание</th>
                        <th style="text-align: left; padding: 12px 16px;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td data-label="Дата/Время" style="padding: 12px 16px;">
                            <div style="font-weight: 500;">{{ $log->created_at->format('d.m.Y') }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td data-label="Пользователь" style="padding: 12px 16px;">
                            <div style="font-weight: 500;">{{ $log->user?->username ?? '—' }}</div>
                            @if($log->user?->full_name)
                                <div style="font-size: 12px; color: #94a3b8;">{{ $log->user?->full_name }}</div>
                            @endif
                        </td>
                        <td data-label="Роль" style="padding: 12px 16px;">
                            @php
                                $roleColors = [
                                    'admin' => 'rgba(56, 189, 248, 0.15)',
                                    'seller' => 'rgba(34, 197, 94, 0.15)',
                                    'warehouse' => 'rgba(139, 92, 246, 0.15)',
                                ];
                                $roleName = $log->user?->role?->name ?? '—';
                                $bgColor = $roleColors[$roleName] ?? 'rgba(148, 163, 184, 0.15)';
                            @endphp
                            <span class="badge" style="background: {{ $bgColor }};">
                                {{ $roleName }}
                            </span>
                        </td>
                        <td data-label="Действие" style="padding: 12px 16px;">
                            @php
                                $actionIcons = [
                                    'login' => ['icon' => 'bi-box-arrow-in-right', 'color' => '#10b981'],
                                    'logout' => ['icon' => 'bi-box-arrow-right', 'color' => '#f59e0b'],
                                    'purchase_created' => ['icon' => 'bi-cart-plus', 'color' => '#38bdf8'],
                                    'purchase_deleted' => ['icon' => 'bi-trash', 'color' => '#ef4444'],
                                    'inventory_adjustment' => ['icon' => 'bi-clipboard-check', 'color' => '#8b5cf6'],
                                    'stock_transfer' => ['icon' => 'bi-arrow-left-right', 'color' => '#06b6d4'],
                                    'sale_created' => ['icon' => 'bi-cash-stack', 'color' => '#22c55e'],
                                    'sale_return' => ['icon' => 'bi-arrow-counterclockwise', 'color' => '#f97316'],
                                    'logs_cleared' => ['icon' => 'bi-eraser', 'color' => '#64748b'],
                                ];
                                $iconData = $actionIcons[$log->action] ?? ['icon' => 'bi-dot', 'color' => '#94a3b8'];
                            @endphp
                            <span style="display: inline-flex; align-items: center; gap: 6px;">
                                <i class="bi {{ $iconData['icon'] }}" style="color: {{ $iconData['color'] }};"></i>
                                {{ $actions[$log->action] ?? $log->action }}
                            </span>
                        </td>
                        <td data-label="Описание" style="padding: 12px 16px; max-width: 400px;">
                            <div style="font-size: 13px; color: #cbd5e1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $log->description ?? '—' }}
                            </div>
                        </td>
                        <td data-label="IP" style="padding: 12px 16px; font-family: monospace; color: #94a3b8;">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                            Записей не найдено
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasMorePages())
        <div style="padding: 16px; border-top: 1px solid rgba(148, 163, 184, 0.1);">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Модальное окно очистки --}}
<div id="clearModal" style="display: none; position: fixed; inset: 0; z-index: 1000; overflow-y: auto;">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5);" onclick="hideClearModal()"></div>
    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
        <div style="background: #1e293b; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 16px; max-width: 450px; width: 100%; padding: 32px;">
            <div style="text-align: center;">
                <div style="width: 64px; height: 64px; background: rgba(239, 68, 68, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 32px; margin: 0 auto 20px;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 700; color: #f1f5f9; margin-bottom: 8px;">Очистка журнала</h3>
                <p style="color: #94a3b8; margin-bottom: 24px;">
                    Вы действительно хотите удалить выбранные записи журнала? Это действие нельзя отменить.
                </p>
                <form action="{{ route('admin.staff-logs.clear') }}" method="POST">
                    @csrf
                    @if(request('user_id'))
                        <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                    @endif
                    @if(request('action'))
                        <input type="hidden" name="action" value="{{ request('action') }}">
                    @endif
                    @if(request('date_from'))
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    @endif
                    @if(request('date_to'))
                        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    @endif
                    <input type="hidden" name="confirm" value="yes">
                    <div style="display: flex; gap: 12px;">
                        <button type="button" onclick="hideClearModal()" style="flex: 1; padding: 10px 16px; background: rgba(255,255,255,0.05); color: #f1f5f9; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; cursor: pointer;">
                            Отмена
                        </button>
                        <button type="submit" style="flex: 1; padding: 10px 16px; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer;">
                            <i class="bi bi-trash"></i> Удалить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showClearModal() {
    document.getElementById('clearModal').style.display = 'block';
}

function hideClearModal() {
    document.getElementById('clearModal').style.display = 'none';
}

// Закрытие по ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideClearModal();
    }
});
</script>

@endsection
