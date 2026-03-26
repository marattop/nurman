@extends('layouts.admin')
@section('page-title', 'Сотрудники')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-people"></i></div>
    <div>
        <h1 class="page-title">Сотрудники</h1>
        <p class="page-subtitle">Управление командой</p>
    </div>
</div>

@if(session('success'))
    <div class="card" style="padding: 16px; border: 1px solid rgba(16,185,129,0.25); margin-bottom: 16px;">
        <div style="font-weight:700; color:#10b981; margin-bottom: 6px;">
            <i class="bi bi-check-circle"></i> Успешно
        </div>
        <div style="color:#f1f5f9;">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="card" style="padding: 16px; border: 1px solid rgba(239,68,68,0.25); margin-bottom: 16px;">
        <div style="font-weight:700; color:#ef4444; margin-bottom: 6px;">
            <i class="bi bi-x-circle"></i> Ошибка
        </div>
        <div style="color:#f1f5f9;">{{ session('error') }}</div>
    </div>
@endif

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список сотрудников</h3>
        <button
            type="button"
            class="btn btn-outline"
            onclick="showAddUserModal()"
        >
            <i class="bi bi-plus-lg"></i> Добавить
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Имя</th>
                    <th>Роль</th>
                    <th>Телефон</th>
                    <th>Магазин</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $user->full_name ?? '—' }}</div>
                        <div style="color: #94a3b8; font-size: 13px;">{{ $user->email ?? '' }}</div>
                    </td>
                    <td>
                        @if($user->isOwner())
                            <span class="badge badge-blue">Владелец</span>
                        @elseif($user->isAdmin())
                            <span class="badge badge-blue">Админ</span>
                        @elseif($user->isSeller())
                            <span class="badge badge-success">Продавец</span>
                        @elseif($user->isWarehouse())
                            <span class="badge badge-warning">Склад</span>
                        @else
                            <span class="badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">Пользователь</span>
                        @endif
                    </td>
                    <td style="color: #94a3b8;">{{ $user->phone ?? '—' }}</td>
                    <td style="color: #94a3b8;">{{ $user->shop?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline">
                            <i class="bi bi-pencil"></i> Ред.
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Сотрудники не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add user modal --}}
<div id="addUserModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="bi bi-person-plus"></i> Добавить сотрудника
                </div>
                <button type="button" class="btn-close" onclick="hideAddUserModal()">&times;</button>
            </div>
            <div class="modal-body">
                @if($errors->any())
                    <div style="margin-bottom: 12px; padding: 12px; border-radius: 12px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25);">
                        <div style="font-weight:700; color:#ef4444; margin-bottom: 6px;">
                            <i class="bi bi-exclamation-triangle"></i> Проверьте поля
                        </div>
                        <ul style="margin:0; padding-left: 18px;">
                            @foreach($errors->all() as $error)
                                <li style="margin: 4px 0;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" autocomplete="off">
                    @csrf

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                                Логин <span style="color:#ef4444">*</span>
                            </label>
                            <input
                                type="text"
                                name="username"
                                class="input"
                                value="{{ old('username') }}"
                                required
                                maxlength="50"
                            >
                        </div>

                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                                Телефон
                            </label>
                            <input
                                type="text"
                                name="phone"
                                class="input"
                                value="{{ old('phone') }}"
                                maxlength="20"
                            >
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            class="input"
                            value="{{ old('email') }}"
                            maxlength="255"
                        >
                    </div>

                    <div style="margin-top: 12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                            ФИО
                        </label>
                        <input
                            type="text"
                            name="full_name"
                            class="input"
                            value="{{ old('full_name') }}"
                            maxlength="255"
                        >
                    </div>

                    <div style="margin-top: 12px; display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                                Роль <span style="color:#ef4444">*</span>
                            </label>
                            <select name="role_id" class="input" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string)old('role_id') === (string)$role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                                Магазин
                            </label>
                            <select name="shop_id" class="input">
                                <option value="">Не выбран</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}" {{ (string)old('shop_id') === (string)$shop->id ? 'selected' : '' }}>
                                        {{ $shop->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom: 6px;">
                            Пароль <span style="color:#ef4444">*</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            class="input"
                            required
                            minlength="6"
                            maxlength="255"
                        >
                        <div style="color:#94a3b8; font-size:12px; margin-top:6px;">
                            Минимум 6 символов
                        </div>
                    </div>

                    <div style="margin-top: 12px; display:flex; align-items:center; gap: 10px;">
                        <input type="checkbox" name="is_active" value="1" id="addUserIsActive" {{ old('is_active') ? 'checked' : '' }}>
                        <label for="addUserIsActive" style="font-weight:700; cursor:pointer;">Активен</label>
                    </div>

                    <div style="margin-top: 18px; display:flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; justify-content:center;">
                            <i class="bi bi-save"></i> Создать
                        </button>
                        <button type="button" class="btn btn-outline" onclick="hideAddUserModal()" style="flex: 1; justify-content:center;">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showAddUserModal() {
        const modal = document.getElementById('addUserModal');
        if (!modal) return;
        modal.classList.add('show');
        modal.style.display = 'block';

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop adduser-backdrop';
        backdrop.onclick = hideAddUserModal;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    function hideAddUserModal() {
        const modal = document.getElementById('addUserModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.querySelectorAll('.adduser-backdrop').forEach(b => b.remove());
        document.body.style.overflow = '';
    }

    @if($errors->any())
        // Если валидация не прошла — открываем модалку с ошибками
        showAddUserModal();
    @endif
</script>

@endsection
