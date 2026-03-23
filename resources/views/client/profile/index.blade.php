@extends('layouts.app')

@section('title', 'Профиль - NURMAN')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <h1 class="mb-4">👤 Профиль</h1>

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
    <div class="card mb-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="card-header" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
            <h5 class="mb-0">📝 Личная информация</h5>
        </div>
        <div class="card-body">
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
    <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="card-header" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
            <h5 class="mb-0">🔐 Смена пароля</h5>
        </div>
        <div class="card-body">
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
