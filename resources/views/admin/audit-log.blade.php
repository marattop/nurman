@extends('layouts.admin')

@section('page-title', 'Журнал аудита')

@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-shield-lock"></i></div>
    <div>
        <h1 class="page-title">Журнал аудита администратора</h1>
        <p class="page-subtitle">Полная история действий администраторов</p>
    </div>
</div>

{{-- Статистика --}}
<div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div style="color: rgba(255,255,255,0.8); font-size: 0.875rem;">Всего записей</div>
        <div style="color: #fff; font-size: 1.75rem; font-weight: 700; margin-top: 0.25rem;">{{ $stats['total'] }}</div>
    </div>
    <div class="card" style="padding: 1rem; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div style="color: rgba(255,255,255,0.8); font-size: 0.875rem;">Сегодня</div>
        <div style="color: #fff; font-size: 1.75rem; font-weight: 700; margin-top: 0.25rem;">{{ $stats['today'] }}</div>
    </div>
    <div class="card" style="padding: 1rem; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div style="color: rgba(255,255,255,0.8); font-size: 0.875rem;">За неделю</div>
        <div style="color: #fff; font-size: 1.75rem; font-weight: 700; margin-top: 0.25rem;">{{ $stats['this_week'] }}</div>
    </div>
    <div class="card" style="padding: 1rem; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div style="color: rgba(255,255,255,0.8); font-size: 0.875rem;">За месяц</div>
        <div style="color: #fff; font-size: 1.75rem; font-weight: 700; margin-top: 0.25rem;">{{ $stats['this_month'] }}</div>
    </div>
</div>

{{-- Фильтры --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-funnel"></i> Фильтры</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.audit-log') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <label class="form-label">Пользователь</label>
                <select name="user_id" class="form-control">
                    <option value="">Все пользователи</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->username }} ({{ $user->role?->name ?? '—' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Действие</label>
                <select name="action" class="form-control">
                    <option value="">Все действия</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                            {{ $action }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Тип модели</label>
                <select name="model_type" class="form-control">
                    <option value="">Все модели</option>
                    @foreach($modelTypes as $modelType)
                        <option value="{{ $modelType }}" {{ request('model_type') === $modelType ? 'selected' : '' }}>
                            {{ class_basename($modelType) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Дата с</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div>
                <label class="form-label">Дата по</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div>
                <label class="form-label">IP адрес</label>
                <input type="text" name="ip_address" class="form-control" placeholder="192.168.1.1" value="{{ request('ip_address') }}">
            </div>

            <div style="display: flex; align-items: end;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="bi bi-search"></i> Поиск
                </button>
                <a href="{{ route('admin.audit-log') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Таблица аудита --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="bi bi-table"></i> Записи аудита
            <span class="badge" style="margin-left: 0.5rem; background: #667eea;">{{ $auditLogs->total() }}</span>
        </h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 0.75rem;">ID</th>
                    <th style="text-align: left; padding: 0.75rem;">Пользователь</th>
                    <th style="text-align: left; padding: 0.75rem;">Действие</th>
                    <th style="text-align: left; padding: 0.75rem;">Модель</th>
                    <th style="text-align: left; padding: 0.75rem;">IP адрес</th>
                    <th style="text-align: left; padding: 0.75rem;">Дата</th>
                    <th style="text-align: right; padding: 0.75rem;">Детали</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $log)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 0.75rem; color: #64748b;">#{{ $log->id }}</td>
                    <td style="padding: 0.75rem;">
                        <div style="font-weight: 500;">{{ $log->user?->username ?? '—' }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $log->user?->role?->display_name ?? '' }}</div>
                    </td>
                    <td style="padding: 0.75rem;">
                        <span class="badge" style="background: {{ $log->getActionColor() }}; color: #fff;">
                            {{ $log->getActionDescription() }}
                        </span>
                    </td>
                    <td style="padding: 0.75rem;">
                        @if($log->model_type)
                            <div style="font-size: 0.875rem;">{{ $log->getModelName() }}</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">ID: {{ $log->model_id }}</div>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td style="padding: 0.75rem; font-family: monospace; font-size: 0.875rem;">
                        {{ $log->ip_address }}
                    </td>
                    <td style="padding: 0.75rem;">
                        <div style="font-size: 0.875rem;">{{ $log->created_at->format('d.m.Y') }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td style="padding: 0.75rem; text-align: right;">
                        <button class="btn btn-sm btn-info" onclick="showLogDetails({{ $log->id }})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>

                {{-- Скрытые детали для модального окна --}}
                <div id="log-details-{{ $log->id }}" style="display: none;">
                    <div class="modal-details">
                        <h4>Старые значения</h4>
                        <pre style="background: #f1f5f9; padding: 1rem; border-radius: 0.5rem; overflow-x: auto;">{{ json_encode($log->old_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        
                        <h4 style="margin-top: 1rem;">Новые значения</h4>
                        <pre style="background: #f1f5f9; padding: 1rem; border-radius: 0.5rem; overflow-x: auto;">{{ json_encode($log->new_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        
                        <h4 style="margin-top: 1rem;">User Agent</h4>
                        <p style="font-size: 0.75rem; color: #64748b; word-break: break-all;">{{ $log->user_agent ?? '—' }}</p>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                        Записей не найдено
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($auditLogs->hasPages())
    <div class="card-footer">
        {{ $auditLogs->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- Модальное окно для деталей --}}
<div id="logModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; border-radius: 0.5rem; padding: 1.5rem; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0;">Детали записи</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<style>
    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .modal-details h4 {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #475569;
    }
</style>

<script>
function showLogDetails(logId) {
    const detailsDiv = document.getElementById('log-details-' + logId);
    const modal = document.getElementById('logModal');
    const modalContent = document.getElementById('modalContent');
    
    if (detailsDiv) {
        modalContent.innerHTML = detailsDiv.querySelector('.modal-details').innerHTML;
        modal.style.display = 'block';
    }
}

function closeModal() {
    document.getElementById('logModal').style.display = 'none';
}

// Закрытие по клику вне модального окна
document.getElementById('logModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Закрытие по Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection
