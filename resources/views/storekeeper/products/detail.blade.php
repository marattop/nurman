@extends('layouts.storekeeper')

@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-tag"></i></div>
    <div>
        <h1 class="page-title">{{ $product->name }}</h1>
        <p class="page-subtitle">Характеристики товара</p>
    </div>
    <div style="margin-left: auto;">
        <a href="{{ route('storekeeper.products.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 320px 1fr; gap: 24px;">
    {{-- Основная информация --}}
    <div class="card" style="padding: 0; overflow: hidden; height: fit-content; position: sticky; top: 24px;">
        <div class="card-header">
            <h3 class="card-header-title"><i class="bi bi-info-circle" style="color: #22c55e;"></i> Информация</h3>
        </div>
        <div style="padding: 20px;">
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Название</p>
                    <p style="margin: 0; font-weight: 500;">{{ $product->name }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Артикул</p>
                    <p style="margin: 0; font-family: monospace;">{{ $product->article }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Категория</p>
                    <span class="badge badge-blue">{{ $product->category?->name ?? '—' }}</span>
                </div>
                @if($product->brand)
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Бренд</p>
                    <p style="margin: 0;">{{ $product->brand }}</p>
                </div>
                @endif
                @if($product->material)
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Материал</p>
                    <p style="margin: 0;">{{ $product->material }}</p>
                </div>
                @endif
                @if($product->country)
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Страна</p>
                    <p style="margin: 0;">{{ $product->country }}</p>
                </div>
                @endif
                <div style="padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.05);">
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Всего вариантов</p>
                    <p style="margin: 0; font-size: 24px; font-weight: 700; color: #22c55e;">{{ $product->variants->count() }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Общее количество</p>
                    <p style="margin: 0; font-size: 24px; font-weight: 700; color: #10b981;">
                        {{ $product->variants->sum(fn($v) => $v->stock->sum('quantity')) }} шт
                    </p>
                </div>
            </div>

            @if($product->description)
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.05);">
                    <p style="margin: 0 0 8px 0; font-size: 12px; color: #94a3b8;">Описание</p>
                    <p style="margin: 0; font-size: 14px; line-height: 1.6;">{{ $product->description }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Варианты по размерам --}}
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @foreach($variantsBySize as $size => $variants)
            <div class="card" style="padding: 0; overflow: hidden;">
                <div class="card-header">
                    <h3 class="card-header-title">
                        <i class="bi bi-rulers" style="color: #22c55e;"></i>
                        Размер: {{ $size ?: 'Универсальный' }}
                    </h3>
                </div>
                <div style="display: flex; flex-direction: column;">
                    @foreach($variants as $variant)
                        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($variant->color)
                                        <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(139,92,246,0.15); display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-palette" style="color: #8b5cf6;"></i>
                                        </div>
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-tag" style="color: #38bdf8;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p style="margin: 0; font-weight: 500;">{{ $variant->color ?? 'Без цвета' }}</p>
                                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8; font-family: monospace;">SKU: {{ $variant->sku ?? '—' }}</p>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 24px;">
                                    @if($variant->barcode)
                                        <div style="text-align: right;">
                                            <p style="margin: 0 0 4px 0; font-size: 11px; color: #94a3b8;">Штрих-код</p>
                                            <p style="margin: 0; font-family: monospace; font-size: 13px;">{{ $variant->barcode }}</p>
                                        </div>
                                    @endif
                                    <div style="text-align: right;">
                                        <p style="margin: 0 0 4px 0; font-size: 11px; color: #94a3b8;">В наличии</p>
                                        <p style="margin: 0; font-size: 20px; font-weight: 700; color: {{ $variant->stock->sum('quantity') <= 5 ? '#ef4444' : '#10b981' }};">
                                            {{ $variant->stock->sum('quantity') }} шт
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($variant->stock->count() > 0)
                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.05);">
                                    <p style="margin: 0 0 8px 0; font-size: 12px; color: #94a3b8;">Расположение:</p>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        @foreach($variant->stock as $stock)
                                            <span class="badge" style="background: rgba(255,255,255,0.05);">
                                                {{ $stock->shop?->name ?? $stock->warehouse?->name ?? 'Не указано' }}: 
                                                <strong style="color: #22c55e;">{{ $stock->quantity }} шт</strong>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
