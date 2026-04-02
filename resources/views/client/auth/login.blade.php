@extends('core::layouts.master')

@section('title', 'Вход для клиентов')

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
                                <i class="fas fa-user" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            <h2 style="font-family: 'Space Grotesk', sans-serif; font-weight: 800; background: linear-gradient(135deg, #FF6B35, #E85A2B); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">NURMAN</h2>
                            <p class="text-muted">Вход для клиентов</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('client.login.post') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Номер телефона</label>
                                <input type="text" class="form-control form-control-lg" id="phone" name="phone" 
                                    placeholder="+996 XXX XXX XXX" required 
                                    value="{{ old('phone') }}"
                                    style="border-radius: 12px; border: 2px solid #e0e0e0;">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Пароль</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" 
                                    required 
                                    style="border-radius: 12px; border: 2px solid #e0e0e0;">
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" 
                                style="border-radius: 12px; background: linear-gradient(135deg, #FF6B35, #E85A2B); border: none; font-weight: 700; padding: 14px;">
                                <i class="fas fa-sign-in-alt me-2"></i> Войти
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Нет аккаунта? 
                                <a href="{{ route('client.register') }}" style="color: #FF6B35; font-weight: 600; text-decoration: none;">
                                    Зарегистрироваться
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
