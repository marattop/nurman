@extends('layouts.storekeeper')

@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-box-seam"></i></div>
    <div>
        <h1 class="page-title">Наличие товаров</h1>
        <p class="page-subtitle">Остатки на складе</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #22c55e;"></i> Список остатков</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Товар</th>
                    <th style="text-align: left; padding: 12px 16px;">Артикул</th>
                    <th style="text-align: center; padding: 12px 16px;">Размер</th>
                    <th style="text-align: center; padding: 12px 16px;">Цвет</th>
                    <th style="text-align: right; padding: 12px 16px;">Количество</th>
                    <th style="text-align: left; padding: 12px 16px;">Место</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stock ?? [] as $item)
                <tr>
                    <td data-label="Товар" style="padding: 12px 16px;">
                        <div style="font-weight: 500;">{{ $item->variant?->product?->name ?? '—' }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">{{ $item->variant?->product?->category?->name ?? '' }}</div>
                    </td>
                    <td data-label="Артикул" style="padding: 12px 16px; font-family: monospace; color: #94a3b8;">
                        {{ $item->variant?->product?->article ?? '—' }}
                    </td>
                    <td data-label="Размер" style="padding: 12px 16px; text-align: center;">
                        @if($item->variant?->size)
                            <span class="badge badge-blue">{{ $item->variant?->size }}</span>
                        @else
                            <span style="color: #64748b;">—</span>
                        @endif
                    </td>
                    <td data-label="Цвет" style="padding: 12px 16px; text-align: center;">
                        @if($item->variant?->color)
                            <span class="badge badge-purple">{{ $item->variant?->color }}</span>
                        @else
                            <span style="color: #64748b;">—</span>
                        @endif
                    </td>
                    <td data-label="Количество" style="padding: 12px 16px; text-align: right;">
                        <span style="font-weight: 600; color: {{ $item->quantity <= $item->min_stock ? '#ef4444' : '#10b981' }};">
                            {{ $item->quantity }} шт
                        </span>
                    </td>
                    <td data-label="Место" style="padding: 12px 16px;">
                        @if($item->shop?->name)
                            <span class="badge badge-blue"><i class="bi bi-shop"></i> {{ $item->shop->name }}</span>
                        @elseif($item->warehouse?->name)
                            <span class="badge badge-purple"><i class="bi bi-building"></i> {{ $item->warehouse->name }}</span>
                        @else
                            <span style="color: #64748b;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                        Остатки не найдены
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stock->hasMorePages())
    <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.05);">
        {{ $stock->links() }}
    </div>
    @endif
</div>
@endsection
