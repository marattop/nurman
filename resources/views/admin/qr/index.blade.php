@extends('layouts.admin')
@section('page-title', 'QR-коды')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code"></i></div>
    <div>
        <h1 class="page-title">QR-коды</h1>
        <p class="page-subtitle">Управление QR-кодами товаров</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список товаров</h3>
        <a href="{{ route('admin.qr.scan') }}" class="btn btn-primary"><i class="bi bi-qr-code-scan"></i> Сканировать</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Товар</th>
                    <th>Артикул</th>
                    <th>QR-код</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            @if($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;">
                            @else
                                <div style="width: 44px; height: 44px; border-radius: 8px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
                                    <i class="bi bi-box"></i>
                                </div>
                            @endif
                            <span style="font-weight: 500;">{{ Str::limit($product->name, 40) }}</span>
                        </div>
                    </td>
                    <td style="color: #94a3b8;">{{ $product->article }}</td>
                    <td>
                        @php
                            // В контроллере загружается: variants.qrCodes
                            $qrCount = $product->variants->sum(fn($v) => $v->qrCodes->count());
                        @endphp

                        @if($qrCount > 0)
                            <span class="badge badge-success">{{ $qrCount }} шт</span>
                        @else
                            <span class="badge badge-danger">Нет QR</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.qr.generate.page', $product->id) }}" class="btn btn-outline">
                            <i class="bi bi-plus-lg"></i> Генерировать
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; padding: 3rem; color: #64748b;">Товары не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
