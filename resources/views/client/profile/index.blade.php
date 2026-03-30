@extends('layouts.app')

@section('title', 'Профиль - NURMAN')

@section('content')
<style>
    .profile-page { max-width: 800px; margin: 0 auto; padding: 2rem; }
    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        margin-bottom: 1.5rem;
    }
    .profile-card-header {
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
        border-radius: 16px 16px 0 0;
    }
    .profile-card-body { padding: 1.5rem; }
    .form-label {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .form-control, .form-select {
        background: var(--bg-input, var(--bg-secondary));
        border: 1px solid var(--border-color);
        color: var(--text-primary);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
    }
    .form-text {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }
</style>

<div class="profile-page">
    <h1 class="mb-4" style="color: var(--text-primary);">👤 Профиль</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Основная информация -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h5 class="mb-0" style="color: var(--text-primary);">📝 Личная информация</h5>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('client.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Телефон</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->phone }}" disabled>
                    <small class="text-muted">Телефон нельзя изменить</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">ФИО *</label>
                    <input type="text" name="full_name" class="form-control" value="{{ auth()->user()->full_name }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Дата рождения</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ $profile->birth_date?->format('Y-m-d') ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Пол</label>
                        <select name="gender" class="form-select">
                            <option value="">Не указано</option>
                            <option value="male" {{ $profile->gender === 'male' ? 'selected' : '' }}>Мужской</option>
                            <option value="female" {{ $profile->gender === 'female' ? 'selected' : '' }}>Женский</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Город</label>
                    <input type="text" name="city" class="form-control" value="{{ $profile->city ?? '' }}" placeholder="Бишкек">
                </div>

                <div class="mb-3">
                    <label class="form-label">Адрес</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Улица, дом, квартира">{{ $profile->address ?? '' }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Сохранить изменения
                </button>
            </form>
        </div>
    </div>

    <!-- Смена пароля -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h5 class="mb-0" style="color: var(--text-primary);">🔐 Смена пароля</h5>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('client.profile.password') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Текущий пароль *</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Новый пароль *</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        <small class="text-muted">Минимум 8 символов</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Подтверждение пароля *</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-key"></i> Изменить пароль
                </button>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> На главную
        </a>
    </div>
</div>
@endsection
