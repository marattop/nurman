@extends('layouts.admin')
@section('page-title', 'Разрешения: Кладовщик')
@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-accent/15 flex items-center justify-center text-accent text-2xl">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold m-0">Кладовщик</h2>
            <p class="text-sm text-muted m-0">Управление разрешениями склада</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="glass rounded-xl px-4 py-3 bg-success/20 border border-success/30 mb-6">
        <p class="text-success font-medium"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</p>
    </div>
@endif

<div class="card p-5 border border-white/5">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-text">Кладовщик (Warehouse)</h3>
            <p class="text-sm text-muted">Разрешения для работы со складом</p>
        </div>
        <span class="px-3 py-1 bg-accent/20 text-accent text-sm font-bold rounded">
            {{ $roles->first()->users_count ?? 0 }} сотрудников
        </span>
    </div>

    <a href="{{ route('admin.roles.permissions', $roles->first()) }}" class="btn btn-primary">
        <i class="bi bi-shield-check me-2"></i>Настроить разрешения
    </a>
</div>
@endsection
