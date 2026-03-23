@extends('layouts.app')
@section('page-title','Склад')
@section('content')
<style>
.stats-card{background:var(--bg-card);border-radius:16px;padding:1.5rem;box-shadow:var(--card-shadow);transition:all 0.3s ease;border:1px solid var(--border-color);height:100%}
.stats-card:hover{transform:translateY(-4px);box-shadow:var(--card-shadow-hover)}
.stats-card-icon{width:64px;height:64px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:var(--gradient-warm);color:white;font-size:1.75rem;box-shadow:0 4px 16px rgba(16,185,129,0.35);margin-bottom:1rem}
.stats-card h4{font-size:1.5rem;font-weight:700;margin:0;color:var(--text-primary)}
.stats-card p{font-size:0.875rem;color:var(--text-secondary);margin:0.25rem 0 0 0}
@media(max-width: 768px){.stats-card{padding:1.25rem}.stats-card-icon{width:52px;height:52px;font-size:1.4rem;margin-bottom:0.75rem}.stats-card h4{font-size:1.25rem}.stats-card p{font-size:0.8rem}}
@media(max-width: 576px){.stats-card{padding:1rem}.stats-card-icon{width:46px;height:46px;font-size:1.2rem;margin-bottom:0.5rem}.stats-card h4{font-size:1.1rem}.stats-card p{font-size:0.75rem}.col-12,.col-12.col-md-4{flex:0 0 100%;max-width:100%}}
</style>
<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="stats-card">
            <div class="stats-card-icon"><i class="bi bi-box"></i></div>
            <div><h4>{{ $totalProducts }}</h4><p>Товаров</p></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stats-card">
            <div class="stats-card-icon" style="background:linear-gradient(135deg,#dc3545,#c82333)"><i class="bi bi-exclamation-triangle"></i></div>
            <div><h4 class="text-danger">{{ $lowStock }}</h4><p>Заканчивается</p></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ route('warehouse.purchases.index') }}" style="text-decoration:none">
            <div class="stats-card" style="background:linear-gradient(135deg,var(--accent-blue),#2563eb);cursor:pointer">
                <div class="stats-card-icon"><i class="bi bi-truck"></i></div>
                <div>
                    <h4 style="color:white">Приёмка</h4>
                    <p style="color:rgba(255,255,255,0.9)">Оформить поступление</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
