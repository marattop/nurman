@extends('layouts.admin')
@section('page-title', 'Настройки')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-gear"></i></div>
    <div>
        <h1 class="page-title">Настройки</h1>
        <p class="page-subtitle">Конфигурация системы</p>
    </div>
</div>

<div class="card">
    <form method="POST" action="{{ route('admin.settings.save') }}">
        @csrf
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Название компании</label>
            <input type="text" name="company_name" class="input" value="{{ $settings['company_name'] ?? 'NURMAN' }}">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Валюта</label>
            <input type="text" name="currency" class="input" value="{{ $settings['currency'] ?? 'сом' }}">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Настройки QR</label>
            <div style="display: flex; align-items: center; gap: 12px;">
                <input type="checkbox" name="qr_auto_generate" id="qr_auto" value="1" {{ ($settings['qr_auto_generate'] ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                <label for="qr_auto" style="color: #94a3b8;">Автоматическая генерация QR-кодов</label>
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Лимит пользователей</label>
            <input type="number" name="user_limit" class="input" value="{{ $settings['user_limit'] ?? 10 }}">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Сохранить
        </button>
    </form>
</div>
@endsection
