@extends('layouts.storekeeper')

@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-person"></i></div>
    <div>
        <h1 class="page-title">Профиль</h1>
        <p class="page-subtitle">Настройки профиля</p>
    </div>
</div>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 16px; background: rgba(34,197,94,0.15); display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: 700; color: #22c55e; overflow: hidden;">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username, 0, 1)) }}
                @endif
            </div>
            <h2 style="margin: 0 0 4px 0; font-size: 24px;">{{ auth()->user()->full_name ?? auth()->user()->username }}</h2>
            <p style="margin: 0; color: #94a3b8;">{{ auth()->user()->role->display_name ?? 'Кладовщик' }}</p>
        </div>

        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('storekeeper.profile.update') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8; font-size: 14px;">ФИО</label>
                <input type="text" name="full_name" value="{{ auth()->user()->full_name ?? '' }}" class="input">
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8; font-size: 14px;">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" class="input">
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8; font-size: 14px;">Телефон</label>
                <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" class="input">
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #94a3b8; font-size: 14px;">Аватар</label>
                <input type="file" name="avatar" accept="image/*" class="input">
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 8px;">
                <i class="bi bi-check-circle"></i> Сохранить изменения
            </button>
        </form>
    </div>
</div>
@endsection
