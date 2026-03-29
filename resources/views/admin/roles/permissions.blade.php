@extends('layouts.admin')
@section('page-title', 'Разрешения: ' . $role->display_name)
@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="w-12 h-12 rounded-xl bg-accent/15 flex items-center justify-center text-accent text-2xl">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold m-0">{{ $role->display_name }}</h2>
            <p class="text-sm text-muted m-0">Разрешения склада</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="glass rounded-xl px-4 py-3 bg-success/20 border border-success/30 mb-6">
        <p class="text-success font-medium"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</p>
    </div>
@endif

<form action="{{ route('admin.roles.permissions.update', $role) }}" method="POST" class="space-y-6">
    @csrf
    
    @foreach($permissions as $group => $groupData)
        <div class="glass rounded-2xl p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i class="bi bi-box-seam text-accent"></i>
                {{ $groupData['label'] ?? 'Склад' }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($groupData['permissions'] ?? [] as $permission)
                    @php
                        $permName = is_array($permission) ? $permission['name'] : $permission->name;
                        $permDisplayName = is_array($permission) ? $permission['display_name'] : $permission->display_name;
                        $permDescription = is_array($permission) ? ($permission['description'] ?? null) : ($permission->description ?? null);
                        $isChecked = in_array($permName, $rolePermissions);
                    @endphp

                    <label class="flex items-center gap-3 p-4 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all {{ $isChecked ? 'bg-accent/10 border border-accent/30' : 'border border-transparent' }}">
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permName }}"
                               {{ $isChecked ? 'checked' : '' }}
                               class="w-5 h-5 rounded bg-white/10 border-white/20 text-accent focus:ring-accent focus:ring-2">
                        <div class="flex-1">
                            <div class="font-medium text-base">{{ $permDisplayName }}</div>
                            @if($permDescription)
                                <div class="text-sm text-muted mt-1">{{ $permDescription }}</div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="flex gap-4 justify-end">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">
            <i class="bi bi-x-lg me-2"></i>Отмена
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-lg me-2"></i>Сохранить изменения
        </button>
    </div>
</form>

<style>
    input[type="checkbox"]:checked + div {
        color: #38bdf8;
    }
</style>
@endsection
