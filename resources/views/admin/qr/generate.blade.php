@extends('layouts.admin')
@section('page-title', 'Генерация QR-кодов')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-qr-code"></i></div>
    <div>
        <h1 class="page-title">Генерация QR-кодов</h1>
        <p class="page-subtitle">{{ $product->name }}</p>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-box"></i> {{ $product->name }}</h3>
        <a href="{{ route('admin.qr.manage') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
    <div class="card-body">
        <p class="text-muted">Артикул: {{ $product->article }}</p>
        
        @if($product->variants->count() > 0)
            <div class="mb-6">
                <h5 class="font-semibold mb-3">Варианты товара:</h5>
                <div class="space-y-2">
                    @foreach($product->variants as $variant)
                        <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 border border-white/5">
                            <div>
                                <strong class="text-text">{{ $variant->size ?? 'Стандарт' }}</strong>
                                <span class="text-muted text-sm ml-2">({{ $variant->barcode ?? '—' }})</span>
                            </div>
                            <span class="badge {{ $variant->qrCodes->count() > 0 ? 'badge-success' : 'badge-danger' }}">
                                {{ $variant->qrCodes->count() > 0 ? 'QR есть' : 'Нет QR' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <form id="generateForm">
                @csrf
                <button type="submit" class="btn btn-primary w-full sm:w-auto justify-center">
                    <i class="bi bi-qr-code-scan"></i> Сгенерировать QR-коды
                </button>
            </form>
        @else
            <p class="text-warning">У этого товара нет вариантов</p>
        @endif
    </div>
</div>

<script>
document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Генерация...';
    
    try {
        const response = await fetch('{{ route('admin.qr.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: {{ $product->id }} })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('QR-коды успешно сгенерированы!');
            window.location.href = '{{ route('admin.qr.manage') }}';
        } else {
            alert('Ошибка: ' + result.message);
        }
    } catch (error) {
        alert('Ошибка: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-qr-code-scan"></i> Сгенерировать QR-коды';
    }
});
</script>
@endsection
