@extends('layouts.storekeeper')

@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-tags"></i></div>
    <div>
        <h1 class="page-title">Товары</h1>
        <p class="page-subtitle">Каталог товаров</p>
    </div>
    <div style="margin-left: auto; display: flex; gap: 8px;">
        <select onchange="location.href=this.value" class="input" style="width: auto;">
            <option value="{{ route('storekeeper.products.index') }}">Все категории</option>
            @foreach($categories as $cat)
                <option value="{{ route('storekeeper.products.category', $cat->id) }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
    @forelse($products as $product)
        <a href="{{ route('storekeeper.products.detail', $product->id) }}" 
            style="text-decoration: none; color: inherit;"
            class="card" 
            style="padding: 0; overflow: hidden; transition: 0.2s; background: #1e293b; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
            <div style="padding: 20px;">
                <h3 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">{{ $product->name }}</h3>
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #94a3b8;">Артикул:</span>
                        <span style="font-family: monospace;">{{ $product->article }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #94a3b8;">Категория:</span>
                        <span class="badge badge-blue">{{ $product->category?->name ?? '—' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #94a3b8;">Вариантов:</span>
                        <span style="font-weight: 600; color: #22c55e;">{{ $product->variants->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #94a3b8;">На складе:</span>
                        <span style="font-weight: 600; color: #10b981;">
                            {{ $product->variants->sum(fn($v) => $v->stock->sum('quantity')) }} шт
                        </span>
                    </div>
                </div>

                @if($product->variants->count() > 0)
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @foreach($product->variants->take(5) as $variant)
                                <span class="badge badge-blue">{{ $variant->size ?? '—' }}</span>
                            @endforeach
                            @if($product->variants->count() > 5)
                                <span class="badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">+{{ $product->variants->count() - 5 }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </a>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
            <i class="bi bi-tags" style="font-size: 64px; color: #64748b; opacity: 0.3; display: block; margin-bottom: 16px;"></i>
            <p style="color: #94a3b8; font-size: 16px;">Товары не найдены</p>
        </div>
    @endforelse
</div>

@if($products->hasMorePages())
    <div style="margin-top: 24px;">
        {{ $products->links() }}
    </div>
@endif
@endsection
