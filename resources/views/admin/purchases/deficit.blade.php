@extends('layouts.admin')
@section('page-title', 'Дефицит товаров')
@section('content')

<div class="page-header">
    <div class="page-icon"><i class="bi bi-exclamation-triangle"></i></div>
    <div>
        <h1 class="page-title">Дефицит товаров</h1>
        <p class="page-subtitle">Товары с критически низким остатком</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0 sm:ml-auto w-full sm:w-auto">
        <a href="{{ route('admin.purchases.create', ['mode' => 'deficit']) }}" class="btn btn-primary justify-center">
            <i class="bi bi-cart-plus"></i> Закупить дефицит
        </a>
    </div>
</div>

@if(count($deficitItems) > 0)
<div class="card" style="padding: 16px 20px; margin-bottom: 24px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);">
    <div style="display: flex; align-items: center; gap: 12px;">
        <i class="bi bi-exclamation-triangle-fill" style="font-size: 24px; color: #ef4444;"></i>
        <div>
            <strong style="color: #ef4444;">Внимание!</strong>
            <span style="color: #fca5a5;">{{ count($deficitItems) }} товаров заканчиваются (остаток ≤ 5 шт)</span>
        </div>
    </div>
</div>
@endif

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #f59e0b;"></i> Список товаров</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Товар</th>
                    <th style="text-align: center; padding: 12px 16px;">Размер</th>
                    <th style="text-align: center; padding: 12px 16px;">Текущий остаток</th>
                    <th style="text-align: center; padding: 12px 16px;">Продаж/день</th>
                    <th style="text-align: center; padding: 12px 16px;">Последняя продажа</th>
                    <th style="text-align: center; padding: 12px 16px;">Рекомендуется закупить</th>
                    <th style="padding: 12px 16px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deficitItems as $item)
                <tr style="{{ $item['current_qty'] <= 0 ? 'background: rgba(239, 68, 68, 0.1);' : '' }}">
                    <td data-label="Товар" style="padding: 12px 16px;">
                        <div style="font-weight: 500;">{{ $item['variant']->product->name ?? '—' }}</div>
                    </td>
                    <td data-label="Размер" style="padding: 12px 16px; text-align: center;">
                        <span class="badge badge-blue">{{ $item['variant']->size ?? '—' }}</span>
                    </td>
                    <td data-label="Текущий остаток" style="padding: 12px 16px; text-align: center;">
                        @if($item['current_qty'] <= 0)
                            <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                                <i class="bi bi-x-circle"></i> Нет
                            </span>
                        @elseif($item['current_qty'] <= 2)
                            <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                                {{ $item['current_qty'] }} шт
                            </span>
                        @else
                            <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                                {{ $item['current_qty'] }} шт
                            </span>
                        @endif
                    </td>
                    <td data-label="Продаж/день" style="padding: 12px 16px; text-align: center;">
                        @if($item['avg_daily_sales'] > 0)
                            <span style="font-weight: 600; color: #10b981;">{{ $item['avg_daily_sales'] }} шт/день</span>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td data-label="Последняя продажа" style="padding: 12px 16px; text-align: center; color: #94a3b8;">
                        @if($item['last_sale_at'])
                            {{ \Carbon\Carbon::parse($item['last_sale_at'])->diffForHumans() }}
                        @else
                            Нет продаж
                        @endif
                    </td>
                    <td data-label="Рекомендуется закупить" style="padding: 12px 16px; text-align: center;">
                        @if($item['recommended_qty'] > 0)
                            <span style="font-weight: 600; color: #38bdf8;">{{ $item['recommended_qty'] }} шт</span>
                            <div style="font-size: 12px; color: #94a3b8;">на 30 дней</div>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td data-label="Действия" style="padding: 12px 16px;">
                        <a href="{{ route('admin.purchases.create', ['mode' => 'deficit']) }}" 
                           class="btn btn-primary btn-sm w-full sm:w-auto justify-center"
                           onclick="addToPurchase({{ $item['variant']->id }}, '{{ $item['variant']->product->name }}', {{ $item['recommended_qty'] }})">
                            <i class="bi bi-cart-plus"></i> В закупку
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-check-circle" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                        Все товары в наличии
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function addToPurchase(variantId, productName, recommendedQty) {
    // Переходим на страницу создания закупки с предустановленным товаром
    const url = "{{ route('admin.purchases.create', ['mode' => 'deficit']) }}";
    // В реальной реализации можно передать через сессию или query params
    window.location.href = url;
}
</script>
@endsection
