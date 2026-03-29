@extends('layouts.owner')

@section('page-title', 'Команда')
@section('page-subtitle', 'Управление сотрудниками и их ролями')

@section('page-actions')
<a href="{{ route('owner.users.create') }}" class="glass px-4 py-2 rounded-xl bg-accent text-bg font-bold text-sm flex items-center gap-2 hover:bg-accent-hover transition-all shadow-glow">
    <i class="bi bi-person-plus-fill"></i>
    <span>Добавить сотрудника</span>
</a>
@endsection

@section('content')

<!-- Filters -->
<div class="glass p-4 rounded-2xl mb-6">
    <form method="GET" action="{{ route('owner.users.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-muted"></i>
            <input type="text" name="search" class="w-full bg-white/5 border border-white/10 rounded-xl py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:border-accent transition-all" placeholder="Поиск по имени или email..." value="{{ request('search') }}">
        </div>
        <div class="flex gap-4">
            <select name="role" class="bg-white/5 border border-white/10 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-accent transition-all min-w-[150px]" onchange="this.form.submit()">
                <option value="">Все роли</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            @if(request()->anyFilled(['search', 'role']))
            <a href="{{ route('owner.users.index') }}" class="glass p-2.5 rounded-xl text-rose-500 hover:bg-rose-500/10 transition-colors" title="Сбросить">
                <i class="bi bi-x-lg"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="text-[10px] uppercase tracking-wider text-muted border-b border-white/5">
                    <th class="px-6 py-4 font-bold">Сотрудник</th>
                    <th class="px-6 py-4 font-bold">Роль</th>
                    <th class="px-6 py-4 font-bold">Статус</th>
                    <th class="px-6 py-4 font-bold">Дата регистрации</th>
                    <th class="px-6 py-4 font-bold text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($users as $user)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4" data-label="Сотрудник">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold text-sm border border-accent/20 shadow-glow">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-sm">{{ $user->username }}</div>
                                @if($user->email)
                                <div class="text-[11px] text-muted">{{ $user->email }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4" data-label="Роль">
                        <span class="px-2.5 py-1 rounded-lg bg-accent/10 text-accent text-[10px] font-bold uppercase tracking-wider">
                            {{ $user->role?->name ?? 'Без роли' }}
                        </span>
                    </td>
                    <td class="px-6 py-4" data-label="Статус">
                        @if($user->is_active)
                            <span class="flex items-center gap-1.5 text-emerald-500 text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                Активен
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-rose-500 text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                                Не активен
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-muted" data-label="Дата">{{ $user->created_at->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-right" data-label="Действия">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('owner.users.edit', $user) }}" class="w-8 h-8 rounded-lg glass flex items-center justify-center text-muted hover:text-accent hover:border-accent/30 transition-all" title="Редактировать">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form method="POST" action="{{ route('owner.users.delete', $user) }}" class="inline" onsubmit="return confirm('Вы уверены, что хотите удалить этого сотрудника?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg glass flex items-center justify-center text-muted hover:text-rose-500 hover:border-rose-500/30 transition-all" title="Удалить">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-muted text-3xl mb-4">
                                <i class="bi bi-people"></i>
                            </div>
                            <h4 class="text-lg font-bold">Сотрудники не найдены</h4>
                            <p class="text-sm text-muted mt-1">Попробуйте изменить параметры поиска</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-white/5 bg-white/5">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
