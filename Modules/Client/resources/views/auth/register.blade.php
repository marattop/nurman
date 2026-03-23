@extends('core::layouts.master')

@section('title', 'Регистрация клиента')

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
                            <p class="text-muted">Регистрация клиента</p>
                        </div>

                        <!-- Registration Form -->
                        <form action="{{ route('client.register.post') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-telephone me-2"></i>Телефон <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                       placeholder="+996 ___ ___ ___" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">На этот телефон придет пароль</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-2"></i>ФИО
                                </label>
                                <input type="text" name="full_name" class="form-control form-control-lg @error('full_name') is-invalid @enderror" 
                                       placeholder="Иванов Иван Иванович" value="{{ old('full_name') }}">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-lock me-2"></i>Пароль <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       placeholder="Придумайте пароль" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Минимум 6 символов</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill me-2"></i>Подтверждение пароля <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg" 
                                       placeholder="Повторите пароль" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3" style="background: linear-gradient(135deg, #FF6B35, #E85A2B); border: none; font-weight: 600;">
                                <i class="bi bi-person-plus me-2"></i>Зарегистрироваться
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center mt-4 pt-4 border-top">
                            <p class="text-muted mb-2">Уже есть аккаунт?</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
