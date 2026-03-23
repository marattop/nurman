@extends('core::layouts.master')

@section('title', 'Вход в систему')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #FF6B35 0%, #E85A2B 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, #FF6B35, #E85A2B); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tshirt" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            <h2 style="font-family: 'Space Grotesk', sans-serif; font-weight: 800; background: linear-gradient(135deg, #FF6B35, #E85A2B); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">NURMAN</h2>
                            <p class="text-muted">Система управления магазином</p>
                        </div>

                        <!-- Login Form -->
                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-2"></i>Имя пользователя
                                </label>
                                <input type="text" name="username" class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                       placeholder="Введите имя пользователя" value="{{ old('username') }}" required autofocus>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-lock me-2"></i>Пароль
                                </label>
                                <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       placeholder="Введите пароль" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Запомнить меня
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3" style="background: linear-gradient(135deg, #FF6B35, #E85A2B); border: none; font-weight: 600;">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                            </button>
                        </form>

                        <!-- Client Registration Link -->
                        <div class="text-center mt-4 pt-4 border-top">
                            <p class="text-muted mb-2">Нет аккаунта?</p>
                            <a href="{{ route('client.register') }}" class="btn btn-outline-primary">
                                <i class="bi bi-person-plus me-2"></i>Регистрация клиента
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
