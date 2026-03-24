@extends('layouts.admin')
@section('page-title', 'Товары')
@section('content')
<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
        <i class="bi bi-box-seam"></i>
    </div>
    <div>
        <h2 style="font-size: 28px; font-weight: 700; margin: 0;">Товары</h2>
        <p style="font-size: 14px; color: #94a3b8; margin: 4px 0 0 0;">Управление ассортиментом</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <h3 style="font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список товаров
        </h3>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <form method="GET" style="display: flex; align-items: center; gap: 8px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 8px 12px;">
                <i class="bi bi-search" style="color: #94a3b8;"></i>
                <input type="text" name="search" placeholder="Поиск..." value="{{ request('search') }}" style="background: transparent; border: none; color: #f1f5f9; outline: none; font-size: 14px;">
            </form>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Добавить
            </a>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Фото</th>
                    <th style="text-align: left;">Название</th>
                    <th>Артикул</th>
                    <th>Цена</th>
                    <th>Остаток</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->photo)
                            <img src="{{ asset('storage/' . $product->photo) }}" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;">
                        @else
                            <div style="width: 44px; height: 44px; border-radius: 8px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
                                <i class="bi bi-box"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 500;">{{ Str::limit($product->name, 50) }}</div>
                        <div style="color: #94a3b8; font-size: 13px;">{{ $product->category?->name ?? '' }}</div>
                    </td>
                    <td style="color: #94a3b8;">{{ $product->article }}</td>
                    <td style="color: #10b981; font-weight: 600;">{{ number_format($product->price, 0, '.', ' ') }} сом</td>
                    <td>
                        @if($product->stock_quantity <= 5)
                            <span class="badge badge-danger">{{ $product->stock_quantity }} шт</span>
                        @else
                            <span class="badge badge-success">{{ $product->stock_quantity }} шт</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.products.edit', $product->id) }}" style="padding: 8px 12px; border-radius: 8px; background: rgba(56,189,248,0.1); color: #38bdf8; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Удалить?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="padding: 8px 12px; border-radius: 8px; background: rgba(239,68,68,0.15); color: #ef4444; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">Товары не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($products->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
        <div style="color: #94a3b8; font-size: 13px;">Показано {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} из {{ $products->total() }}</div>
        <div style="display: flex; gap: 8px;">
            @if($products->onFirstPage())
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">← Назад</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: rgba(56,189,248,0.1); color: #38bdf8; text-decoration: none; font-size: 14px;">← Назад</a>
            @endif
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: rgba(56,189,248,0.1); color: #38bdf8; text-decoration: none; font-size: 14px;">Вперёд →</a>
            @else
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">Вперёд →</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
