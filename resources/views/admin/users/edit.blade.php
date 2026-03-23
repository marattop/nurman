@extends('layouts.app')

@section('title', 'Редактирование сотрудника - Администратор')

@section('content')
<style>
    .edit-card {
        background: var(--bg-card);
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
    }
    .edit-card-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--gradient-warm);
        border-radius: 12px 12px 0 0;
        color: white;
    }
    .edit-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .edit-card-body {
        padding: 2rem;
    }
    .form-control, .form-select {
        background: var(--bg-card);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    .form-control:focus, .form-select:focus {
        background: var(--bg-card);
        border-color: var(--primary);
        color: var(--text-primary);
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
    .form-control::placeholder {
        color: var(--text-muted);
    }
    .form-label {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .form-text {
        color: var(--text-muted);
    }
    .page-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    .page-title-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gradient);
        color: white;
    }
    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background: var(--primary-light);
        color: var(--primary-dark);
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="page-title-icon">
            <i class="bi bi-pencil"></i>
        </div>
        <h1 class="h3 mb-0" style="color: var(--primary);">Редактирование сотрудника</h1>
        <div class="ms-auto">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="edit-card">
                <div class="edit-card-header">
                    <h5 class="edit-card-title">
                        <i class="bi bi-person-badge"></i>
                        Информация о сотруднике
                    </h5>
                </div>
                <div class="edit-card-body">
                    <div class="info-badge">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ $user->full_name ?? $user->username }}</span>
                    </div>

                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Логин <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Телефон</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ФИО</label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $user->full_name) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Роль <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Магазин</label>
                                <select name="shop_id" class="form-select">
                                    <option value="">Не выбран</option>
                                    @foreach($shops as $shop)
                                        <option value="{{ $shop->id }}" {{ $user->shop_id == $shop->id ? 'selected' : '' }}>
                                            {{ $shop->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Магазин, к которому прикреплён сотрудник</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Новый пароль</label>
                            <input type="password" name="password" class="form-control" placeholder="Оставьте пустым, чтобы не менять">
                            <small class="form-text text-muted">Оставьте пустым, если не хотите менять пароль</small>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">
                                <strong>Активен</strong>
                            </label>
                            <div class="form-text">Сотрудник сможет входить в систему</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Сохранить
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="edit-card">
                <div class="edit-card-header">
                    <h5 class="edit-card-title">
                        <i class="bi bi-info-circle"></i>
                        Информация
                    </h5>
                </div>
                <div class="edit-card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">ID:</td>
                            <td class="fw-bold">{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email:</td>
                            <td>{{ $user->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Дата регистрации:</td>
                            <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Последний вход:</td>
                            <td>{{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Статус:</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Активен</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Заблокирован</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="edit-card mt-3">
                <div class="edit-card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        Опасная зона
                    </h6>
                    <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить сотрудника?')">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Удалить сотрудника
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
