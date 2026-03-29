@extends('layouts.warehouse')
@section('page-title', 'Доступ ограничен')
@section('content')
<div class="glass rounded-2xl overflow-hidden p-8 text-center">
    <div class="mb-6">
        <i class="bi bi-shield-lock text-8xl text-warning/50"></i>
    </div>
    
    <h2 class="text-3xl font-bold mb-4">Доступ ограничен</h2>
    
    <p class="text-lg text-muted mb-6">
        У вас нет разрешения на просмотр этой страницы.
    </p>
    
    <div class="bg-warning/10 border border-warning/30 rounded-xl p-4 mb-6">
        <p class="text-warning font-medium">
            <i class="bi bi-info-circle me-2"></i>
            Обратитесь к администратору для получения разрешения на печать QR-кодов.
        </p>
    </div>
    
    <div class="flex gap-4 justify-center">
        <a href="{{ route('warehouse.dashboard') }}" class="btn btn-primary btn-lg px-8">
            <i class="bi bi-house me-2"></i>На дашборд
        </a>
        <a href="{{ route('warehouse.qrcodes.index') }}" class="btn btn-outline btn-lg px-8">
            <i class="bi bi-arrow-left me-2"></i>Назад
        </a>
    </div>
</div>
@endsection
