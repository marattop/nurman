@extends('seller::layouts.seller')
@section('title', 'Мой профиль')
@section('page-title', 'Мой профиль')

@section('content')
<div class="card">
    <div class="page-header" style="margin-bottom: 24px;">
        <div class="page-icon" style="background: rgba(16,185,129,0.15); color: #10b981;">
            <i class="bi bi-person-circle"></i>
        </div>
        <div>
            <h1 class="page-title" style="font-size: 24px;">Профиль продавца</h1>
            <p class="page-subtitle">Настройки профиля и аватар</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-success/15 border border-success/30 text-success rounded-xl p-4 mb-6 flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-danger/15 border border-danger/30 text-danger rounded-xl p-4 mb-6">
            <div class="flex items-center gap-3 mb-2">
                <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                <span class="font-bold">Ошибка</span>
            </div>
            <ul class="list-disc list-inside text-sm opacity-90 space-y-1 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Карточка профиля --}}
        <div class="card !p-0 overflow-hidden border-white/5">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-person text-accent"></i> Информация
                </h3>
            </div>
            <div class="p-5">
                <div class="text-center mb-6">
                    <div class="relative inline-block">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-success to-success-hover flex items-center justify-center text-bg font-black text-5xl mx-auto mb-4 border-4 border-white/10 shadow-xl overflow-hidden">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->full_name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->full_name ?? $user->username, 0, 1)) }}
                            @endif
                        </div>
                        <div class="absolute bottom-0 right-0 w-10 h-10 bg-success border-4 border-bg rounded-full flex items-center justify-center">
                            <i class="bi bi-check text-white text-xs"></i>
                        </div>
                    </div>
                    
                    <h2 class="text-2xl font-black text-white mb-1">{{ $user->full_name ?? $user->username }}</h2>
                    <p class="text-muted text-sm">{{ $user->role->display_name ?? 'Продавец' }}</p>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 text-muted">
                        <i class="bi bi-person text-success"></i>
                        <span class="text-text">{{ $user->username }}</span>
                    </div>
                    @if($user->email)
                    <div class="flex items-center gap-3 text-muted">
                        <i class="bi bi-envelope text-success"></i>
                        <span class="text-text">{{ $user->email }}</span>
                    </div>
                    @endif
                    @if($user->phone)
                    <div class="flex items-center gap-3 text-muted">
                        <i class="bi bi-phone text-success"></i>
                        <span class="text-text">{{ $user->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Форма редактирования --}}
        <div class="lg:col-span-2 card !p-0 overflow-hidden border-white/5">
            <div class="px-5 py-4 border-b border-white/5 bg-white/5">
                <h3 class="text-base font-semibold m-0 flex items-center gap-2">
                    <i class="bi bi-pencil text-success"></i> Редактировать профиль
                </h3>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    {{-- Загрузка аватара --}}
                    <div>
                        <label class="block font-semibold mb-2 text-text text-sm">Аватар</label>
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-success to-success-hover flex items-center justify-center text-bg font-black text-2xl border-4 border-white/10 overflow-hidden">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->full_name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->full_name ?? $user->username, 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                                <label for="avatar" class="btn btn-outline py-2 px-4 text-sm cursor-pointer">
                                    <i class="bi bi-upload"></i> Загрузить фото
                                </label>
                                @if($user->avatar)
                                    <button type="button" onclick="removeAvatar()" class="btn btn-danger py-2 px-4 text-sm ml-2">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                @endif
                            </div>
                        </div>
                        <p class="text-[10px] text-muted mt-2">PNG, JPG до 2MB</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-semibold mb-2 text-text text-sm">ФИО</label>
                            <input type="text" name="full_name" class="input" value="{{ old('full_name', $user->full_name) }}" placeholder="Иванов Иван">
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-text text-sm">Логин</label>
                            <input type="text" name="username" class="input" value="{{ old('username', $user->username) }}" disabled>
                            <p class="text-[10px] text-muted mt-1">Логин нельзя изменить</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-semibold mb-2 text-text text-sm">Email</label>
                            <input type="email" name="email" class="input" value="{{ old('email', $user->email) }}" placeholder="example@mail.ru">
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-text text-sm">Телефон</label>
                            <input type="tel" name="phone" class="input" value="{{ old('phone', $user->phone) }}" placeholder="+996 555 123 456">
                        </div>
                    </div>

                    <div class="border-t border-white/5 pt-5 mt-5">
                        <h4 class="text-sm font-semibold text-text mb-4 flex items-center gap-2">
                            <i class="bi bi-lock text-success"></i> Смена пароля
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block font-semibold mb-2 text-text text-sm">Новый пароль</label>
                                <input type="password" name="password" class="input" placeholder="••••••••">
                            </div>
                            <div>
                                <label class="block font-semibold mb-2 text-text text-sm">Подтверждение</label>
                                <input type="password" name="password_confirmation" class="input" placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="btn btn-primary flex-1">
                            <i class="bi bi-check-lg"></i> Сохранить
                        </button>
                        <a href="{{ route('seller.dashboard') }}" class="btn btn-outline">
                            <i class="bi bi-x-lg"></i> Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.w-20.h-20 img')?.remove();
            const avatarDiv = document.querySelector('.w-20.h-20');
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';
            avatarDiv.appendChild(img);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeAvatar() {
    if (confirm('Вы уверены, что хотите удалить аватар?')) {
        document.querySelector('input[type="file"]').value = '';
        location.reload();
    }
}
</script>
@endsection
