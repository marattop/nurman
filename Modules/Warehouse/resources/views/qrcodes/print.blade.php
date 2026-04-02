@extends('layouts.warehouse')
@section('page-title', 'Печать QR-кода')
@section('content')
<div class="glass rounded-2xl overflow-hidden p-8">
    <div class="text-center mb-8">
        @if($qrCode->variant?->product?->photo)
            <img src="{{ asset('storage/' . $qrCode->variant->product->photo) }}" alt="{{ $qrCode->variant?->product?->name }}" class="w-64 h-64 rounded-2xl object-cover mx-auto mb-6 shadow-lg" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        @endif
        
        @if(!$qrCode->variant?->product?->photo)
            <div class="w-64 h-64 rounded-2xl mx-auto mb-6 bg-white/10 flex items-center justify-center">
                <i class="bi bi-image text-8xl text-muted/50"></i>
            </div>
        @endif
        
        <!-- Скрытая заглушка для onerror -->
        <div class="w-64 h-64 rounded-2xl mx-auto mb-6 bg-white/10 flex items-center justify-center" style="display: none;">
            <i class="bi bi-image text-8xl text-muted/50"></i>
        </div>
        
        <h2 class="text-4xl font-bold mb-3">{{ $qrCode->variant?->product?->name ?? 'Товар не найден' }}</h2>
        <p class="text-lg text-muted">Артикул: {{ $qrCode->variant?->product?->article ?? '—' }}</p>
        @if($qrCode->variant)
            <p class="text-lg text-muted mt-2">Размер: {{ $qrCode->variant->size ?? '—' }} | Цвет: {{ $qrCode->variant->color ?? '—' }}</p>
            <p class="text-2xl font-bold text-primary mt-3">{{ number_format($qrCode->variant->retail_price, 2) }} сом</p>
        @endif
    </div>

    <div class="flex justify-center mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-xl">
            @php
                $qrPath = $qrCode->qr_code_path ?? null;
                $fullPath = $qrPath ? public_path($qrPath) : null;
                $fileExists = $qrPath && file_exists($fullPath);
            @endphp
            @if($fileExists)
                <img src="{{ asset($qrPath) }}" alt="QR Code" class="w-80 h-80">
            @else
                <div class="w-80 h-80 flex items-center justify-center text-danger">
                    <i class="bi bi-qr-code text-6xl text-muted/30"></i>
                    <div class="ml-3 text-left">
                        <p>QR-код не найден</p>
                        @if($qrPath)
                            <p class="text-xs text-muted mt-2">Путь: {{ $qrPath }}</p>
                            <p class="text-xs text-muted">Полный путь: {{ $fullPath }}</p>
                            <p class="text-xs text-muted">Существует: {{ file_exists($fullPath) ? 'да' : 'нет' }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="flex gap-4 justify-center flex-wrap">
        <a href="{{ route('warehouse.qrcodes.print-label', $qrCode) }}" target="_blank" class="btn btn-primary btn-lg px-8 py-3">
            <i class="bi bi-printer me-2"></i>Напечатать
        </a>
        <a href="{{ route('warehouse.qrcodes.index') }}" class="btn btn-secondary btn-lg px-8 py-3">
            <i class="bi bi-arrow-left me-2"></i>Назад
        </a>
    </div>
</div>

<style>
    @media print {
        .sidebar, .bottom-nav, .btn { display: none !important; }
        .glass { box-shadow: none !important; border: none !important; }
    }
    @media screen {
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
    }
</style>
@endsection
