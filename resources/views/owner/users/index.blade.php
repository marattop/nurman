@extends('layouts.owner')

@section('page-title', 'Команда')
@section('page-subtitle', 'Управление пользователями и правами доступа')

@section('page-actions')
    <a href="{{ route('owner.users.create') }}" class="btn glass bg-accent/20 hover:bg-accent/30 text-accent px-4 py-2 rounded-xl flex items-center gap-2 transition-all">
        <i class="bi bi-person-plus-fill"></i>
        <span>Добавить пользователя</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="glass p-6 rounded-2xl">
        <form method="GET" action="{{ route('owner.users.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по имени, email или телефону..." 
                    class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 pl-11 pr-4 focus:outline-none focus:border-accent/50 transition-colors">
            </div>
            <div class="w-full md:w-64">
                <select name="role" onchange="this.form.submit()" 
                    class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 focus:outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer">
                    <option value="">Все роли</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.02]">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Пользователь</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Роль</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Статус</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted">Дата создания</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-muted text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($users as $user)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4" data-label="Пользователь">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold">
                                        {{ strtoupper(substr($user->username, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-white">{{ $user->username }}</div>
                                        <div class="text-xs text-muted">{{ $user->email ?? 'Нет email' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4" data-label="Роль">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-accent/10 text-accent">
                                    {{ $user->role?->name ?? 'Без роли' }}
                                </span>
                            </td>
                            <td class="px-6 py-4" data-label="Статус">
                                @if($user->is_active)
                                    <span class="flex items-center gap-1.5 text-success text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                                        Активен
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 text-danger text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-danger"></span>
                                        Не активен
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-muted" data-label="Дата создания">
                                {{ $user->created_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Действия">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('owner.users.edit', $user) }}" class="p-2 hover:bg-white/5 rounded-lg text-muted hover:text-white transition-colors">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('owner.users.delete', $user) }}" class="inline-block" onsubmit="return confirm('Вы уверены?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:bg-danger/10 rounded-lg text-muted hover:text-danger transition-colors">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-muted">
                                <i class="bi bi-people text-4xl mb-4 block opacity-20"></i>
                                <p>Пользователи не найдены</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
