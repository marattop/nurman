@extends('layouts.owner')

@section('page-title', 'Редактировать пользователя')
@section('page-subtitle', 'Изменение параметров учетной записи: ' . $user->username)

@section('content')
<div class="flex justify-center">
    <div class="glass p-8 rounded-2xl w-full max-w-2xl">
        <form method="POST" action="{{ route('owner.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Username -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted ml-1">Имя пользователя <span class="text-danger">*</span></label>
                    <input type="text" name="username" required value="{{ $user->username }}"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 focus:outline-none focus:border-accent/50 transition-colors">
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted ml-1">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 focus:outline-none focus:border-accent/50 transition-colors">
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted ml-1">Телефон</label>
                    <input type="text" name="phone" value="{{ $user->phone }}"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 focus:outline-none focus:border-accent/50 transition-colors">
                </div>

                <!-- Full Name -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted ml-1">Полное имя</label>
                    <input type="text" name="full_name" value="{{ $user->full_name }}"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 focus:outline-none focus:border-accent/50 transition-colors">
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted ml-1">Новый пароль</label>
                    <input type="password" name="password" minlength="6"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 focus:outline-none focus:border-accent/50 transition-colors"
                        placeholder="Оставьте пустым, чтобы не менять">
                </div>

                <!-- Role -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted ml-1">Роль <span class="text-danger">*</span></label>
                    <div class="relative">
                        <select name="role_id" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-muted pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="pt-2">
                <label class="inline-flex items-center cursor-pointer group">
                    <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="hidden peer">
                    <div class="w-5 h-5 border-2 border-white/20 rounded-md flex items-center justify-center peer-checked:bg-accent peer-checked:border-accent transition-all mr-3">
                        <i class="bi bi-check-lg text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                    </div>
                    <span class="text-sm font-medium text-white/80 group-hover:text-white transition-colors">Активен</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <a href="{{ route('owner.users.index') }}" 
                    class="flex-1 px-6 py-3 rounded-xl border border-white/10 text-center font-semibold hover:bg-white/5 transition-all">
                    Отмена
                </a>
                <button type="submit" 
                    class="flex-1 px-6 py-3 rounded-xl bg-accent hover:bg-accent-hover text-white font-semibold shadow-glow transition-all">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
