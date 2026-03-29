@extends('layouts.warehouse')
@section('page-title', 'Пакетная печать QR-кодов')
@section('content')
<div class="glass rounded-2xl overflow-hidden p-6">
    <h2 class="text-2xl font-bold mb-6 text-center">Пакетная печать QR-кодов</h2>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        @foreach($qrCodes as $qrCode)
            <div class="bg-white rounded-xl p-4 text-center print-break">
                <p class="text-sm font-bold mb-2 text-gray-800">{{ $qrCode->variant?->product?->name ?? 'Товар' }}</p>
                <p class="text-xs text-gray-500 mb-2">{{ $qrCode->variant?->product?->article ?? '' }}</p>
                <p class="text-xs text-gray-500 mb-2">{{ $qrCode->variant?->size ?? '' }} / {{ $qrCode->variant?->color ?? '' }}</p>
                <p class="text-sm font-bold text-primary mb-2">{{ number_format($qrCode->variant?->retail_price ?? 0, 0) }} сом</p>
                
                <div class="bg-white p-2 flex justify-center">
                    @if($qrCode->qr_code_path && file_exists(public_path($qrCode->qr_code_path)))
                        <img src="{{ asset($qrCode->qr_code_path) }}" alt="QR" class="w-32 h-32">
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex gap-3 justify-center">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer me-2"></i>Печать
        </button>
        <a href="{{ route('warehouse.qrcodes.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left me-2"></i>Назад
        </a>
    </div>
</div>

<style>
    @media print {
        .sidebar, .bottom-nav, .btn { display: none !important; }
        .glass { box-shadow: none !important; border: none !important; }
        .print-break { page-break-inside: avoid; }
    }
</style>
@endsection
