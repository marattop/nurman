@extends('layouts.owner')

@section('page-title', 'Журнал действий')
@section('page-subtitle', 'История активности пользователей системы')

@section('page-actions')
    <a href="{{ route('owner.logs.export') }}" class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold rounded-xl transition-all border border-white/10">
        <i class="bi bi-download mr-2"></i> Экспорт
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter Card -->
    <div class="glass p-6 rounded-2xl">
        <form method="GET" action="{{ route('owner.logs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2 px-1">Пользователь</label>
                <select name="user_id" class="bg-sidebar border border-border text-text text-sm rounded-xl focus:ring-accent focus:border-accent block w-full p-2.5 transition-all" onchange="this.form.submit()">
                    <option value="">Все пользователи</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2 px-1">Действие</label>
                <select name="action" class="bg-sidebar border border-border text-text text-sm rounded-xl focus:ring-accent focus:border-accent block w-full p-2.5 transition-all" onchange="this.form.submit()">
                    <option value="">Все действия</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2 px-1">От</label>
                <input type="date" name="date_from" class="bg-sidebar border border-border text-text text-sm rounded-xl focus:ring-accent focus:border-accent block w-full p-2.5 transition-all" value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-2 px-1">До</label>
                <input type="date" name="date_to" class="bg-sidebar border border-border text-text text-sm rounded-xl focus:ring-accent focus:border-accent block w-full p-2.5 transition-all" value="{{ request('date_to') }}">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-accent hover:bg-accent-hover text-bg text-sm font-bold rounded-xl transition-all shadow-glow">
                    <i class="bi bi-funnel mr-2"></i> Фильтр
                </button>
            </div>
        </form>
    </div>

    <!-- Logs Table Card -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="text-muted text-[10px] uppercase tracking-widest border-b border-white/5">
                        <th class="px-6 py-4 font-bold">ID</th>
                        <th class="px-6 py-4 font-bold">Пользователь</th>
                        <th class="px-6 py-4 font-bold">Действие</th>
                        <th class="px-6 py-4 font-bold">Описание</th>
                        <th class="px-6 py-4 font-bold">IP</th>
                        <th class="px-6 py-4 font-bold text-right">Дата</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4" data-label="ID">
                            <span class="px-2 py-1 rounded-md bg-accent/10 text-accent text-[10px] font-bold">#{{ $log->id }}</span>
                        </td>
                        <td class="px-6 py-4" data-label="Пользователь">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-accent text-xs font-bold border border-white/10">
                                    {{ substr($log->user?->username ?? '?', 0, 1) }}
                                </div>
                                <span class="text-text font-semibold text-sm">{{ $log->user?->username ?? 'Система' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4" data-label="Действие">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-success/10 text-success border border-success/20">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4" data-label="Описание">
                            <p class="text-muted text-sm max-w-xs truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </p>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-muted" data-label="IP">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-right" data-label="Дата">
                            <div class="flex flex-col text-right">
                                <span class="text-text text-sm font-medium">{{ $log->created_at->format('d.m.Y') }}</span>
                                <span class="text-muted text-[10px]">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-muted">
                            <i class="bi bi-journal-x text-5xl block mb-4 opacity-20"></i>
                            <p class="text-sm font-medium">Журнал пуст</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-white/5 bg-white/2">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
