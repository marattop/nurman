@extends('layouts.app')

@section('title', 'Настройки продавца')

@section('content')
<style>
    .settings-card {
        background: var(--bg-card);
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-label {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .form-select, .form-control {
        background: var(--bg-card);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    .form-select:focus, .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    .form-check-label {
        color: var(--text-primary);
    }
    .info-box {
        background: rgba(16, 185, 129, 0.1);
        border-left: 4px solid var(--primary);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    .info-box h6 {
        color: var(--primary);
        margin-bottom: 0.5rem;
    }
    .info-box p {
        color: var(--text-secondary);
        margin: 0;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0" style="color: var(--primary);">
            <i class="bi bi-gear"></i> Настройки продавца: {{ $seller->full_name }}
        </h1>
        <a href="{{ route('admin.sellers.shops') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>

    <div class="info-box">
        <h6><i class="bi bi-info-circle"></i> Информация</h6>
        <p>Здесь вы можете настроить доступ продавца к складу. По умолчанию продавец работает только с витриной магазина.</p>
    </div>

    <form action="{{ route('admin.sellers.settings.update', $seller->id) }}" method="POST">
        @csrf
        
        <div class="settings-card">
            <h5 class="mb-4"><i class="bi bi-shop"></i> Привязка к магазину</h5>
            
            <div class="mb-4">
                <label class="form-label">Магазин <span class="text-danger">*</span></label>
                <select name="shop_id" class="form-select" required>
                    <option value="">Выберите магазин</option>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}" {{ $seller->shop_id == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }} ({{ $shop->code }})
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Магазин, к которому привязан продавец</small>
            </div>
        </div>

        <div class="settings-card">
            <h5 class="mb-4"><i class="bi bi-layers"></i> Доступ к складу</h5>
            
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="can_sell_from_warehouse" id="canSellFromWarehouse" {{ $seller->can_sell_from_warehouse ? 'checked' : '' }}>
                <label class="form-check-label" for="canSellFromWarehouse">
                    <strong>Разрешить продажу со склада</strong>
                </label>
            </div>
            
            <div class="alert" style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid var(--primary); color: var(--text-primary);">
                <i class="bi bi-info-circle"></i>
                <strong>Как это работает:</strong>
                <ul class="mb-0 mt-2">
                    <li>Если <strong>выключено</strong> — продавец продаёт товары только из витрины магазина</li>
                    <li>Если <strong>включено</strong> — продавец может переключаться между витриной и складом при продаже</li>
                    <li>Полезно, когда товары в витрине закончились</li>
                </ul>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Сохранить
            </button>
            <a href="{{ route('admin.sellers.shops') }}" class="btn btn-outline-secondary">
                Отмена
            </a>
        </div>
    </form>
</div>
@endsection
