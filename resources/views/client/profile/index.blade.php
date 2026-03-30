@extends('layouts.app')

@section('title', 'Профиль - NURMAN')

@section('content')
<style>
    .profile-page { max-width: 800px; margin: 0 auto; padding: 1rem; }
    
    @media (max-width: 768px) {
        .profile-page { padding: 0.75rem; }
    }
    
    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        margin-bottom: 1rem;
    }
    .profile-card-header {
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 1.25rem;
        border-radius: 16px 16px 0 0;
    }
    .profile-card-body { padding: 1.25rem; }
    
    .form-label {
        color: var(--text-primary) !important;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }
    .form-control, .form-select {
        background: var(--bg-input, var(--bg-secondary)) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-size: 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary) !important;
        outline: none !important;
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25) !important;
    }
    .form-control::placeholder {
        color: var(--text-secondary) !important;
    }
    .form-text {
        color: var(--text-secondary) !important;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }
    .card-title {
        color: var(--text-primary) !important;
        font-size: 1.125rem;
        font-weight: 700;
        margin: 0;
    }
    .page-title {
        color: var(--text-primary) !important;
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }
    .alert {
        color: #fff !important;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .alert-success { background: #10b981 !important; }
    .alert-danger { background: #ef4444 !important; }
    .alert-warning { background: #f59e0b !important; }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
    }
    .btn-primary { background: var(--primary); color: #fff !important; }
    .btn-secondary { background: var(--bg-secondary); color: var(--text-primary) !important; }
    
    .mb-3 { margin-bottom: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .row { display: flex; flex-wrap: wrap; margin: 0 -0.5rem; }
    .col-md-6 { flex: 0 0 50%; max-width: 50%; padding: 0 0.5rem; }
    
    @media (max-width: 640px) {
        .col-md-6 { flex: 0 0 100%; max-width: 100%; }
        .profile-card-body { padding: 1rem; }
        .btn { width: 100%; justify-content: center; }
    }
</style>

<div class="profile-page">
    <h1 class="page-title">Профиль</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0" style="padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Основная информация -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h5 class="card-title">Личная информация</h5>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('client.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Телефон</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->phone }}" disabled>
                    <small class="form-text">Телефон нельзя изменить</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">ФИО</label>
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
                    Сохранить изменения
                </button>
            </form>
        </div>
    </div>

    <!-- Смена пароля -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h5 class="card-title">Смена пароля</h5>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('client.profile.password') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Текущий пароль</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Новый пароль</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        <small class="form-text">Минимум 8 символов</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Подтверждение пароля</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Сохранить
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
