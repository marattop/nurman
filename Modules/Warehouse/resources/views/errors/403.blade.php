@extends('layouts.warehouse')

@section('page-title', 'Доступ ограничен')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center">
    <div class="w-32 h-32 rounded-full bg-danger/10 flex items-center justify-center text-danger mb-8">
        <i class="bi bi-shield-lock text-6xl"></i>
    </div>
    
    <h2 class="text-3xl font-black text-white mb-4">403: Нет доступа</h2>
    <p class="text-muted max-w-md mx-auto mb-8 leading-relaxed">
        У вашего аккаунта недостаточно прав для просмотра этого раздела. 
        Пожалуйста, обратитесь к администратору для получения доступа.
    </p>

    <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('warehouse.dashboard') }}" class="btn btn-primary px-8 py-3 rounded-2xl font-black">
            ВЕРНУТЬСЯ НА ГЛАВНУЮ
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline px-8 py-3 rounded-2xl font-black w-full">
                ВЫЙТИ ИЗ АККАУНТА
            </button>
        </form>
    </div>
</div>
@endsection
