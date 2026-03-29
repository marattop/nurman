@extends('layouts.warehouse')
@section('page-title', 'Магазины')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($shops ?? [] as $shop)
        <div class="glass p-6 rounded-2xl">
            <h3 class="text-lg font-bold text-white">{{ $shop->name }}</h3>
            <p class="text-sm text-muted mt-2">{{ $shop->address ?? '—' }}</p>
        </div>
    @empty
        <p class="text-muted text-center col-span-3 py-20">Магазины не найдены</p>
    @endforelse
</div>
@endsection
