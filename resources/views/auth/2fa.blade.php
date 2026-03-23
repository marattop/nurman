@extends('layouts.app')

@section('title', '2FA Аутентификация')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, var(--accent-primary), var(--accent-primary-dark)); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-shield-lock" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            <h2 class="mb-2">Двухфакторная аутентификация</h2>
                            <p class="text-muted">Введите код из приложения аутентификации</p>
                        </div>

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form action="{{ route('auth.2fa.verify') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-key me-2"></i>Код аутентификации
                                </label>
                                <input type="text" name="code" class="form-control form-control-lg @error('code') is-invalid @enderror"
                                       placeholder="123456" maxlength="6" pattern="[0-9]{6}" required autofocus
                                       style="text-align: center; letter-spacing: 0.5rem; font-size: 1.5rem;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> Введите 6-значный код из Google Authenticator или аналогичного приложения
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 mb-3" style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-primary-dark)); border: none;">
                                <i class="bi bi-check-circle"></i> Проверить
                            </button>
                        </form>

                        <div class="text-center">
                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#recoveryCodeHelp">
                                <i class="bi bi-question-circle"></i> Использовать код восстановления
                            </button>
                        </div>

                        <div class="collapse mt-3" id="recoveryCodeHelp">
                            <div class="alert alert-info">
                                <p class="mb-2"><strong>Коды восстановления:</strong></p>
                                <p class="small mb-0">
                                    Если у вас нет доступа к приложению аутентификации, используйте один из кодов восстановления.
                                    Эти коды были выданы вам при включении 2FA.
                                </p>
                            </div>
                            <form action="{{ route('auth.2fa.verify') }}" method="POST">
                                @csrf
                                <input type="text" name="code" class="form-control mb-2" placeholder="Код восстановления" style="text-transform: uppercase;">
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-key"></i> Использовать код восстановления
                                </button>
                            </form>
                        </div>

                        <div class="text-center mt-4 pt-4 border-top">
                            <p class="text-muted mb-2">Не можете войти?</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-headset"></i> Связаться с администратором
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Автоматический фокус на поле ввода
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.querySelector('input[name="code"]');
    if (codeInput) {
        codeInput.focus();
        
        // Автоматическая отправка формы после ввода 6 цифр
        codeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 6) {
                setTimeout(() => {
                    this.form.submit();
                }, 300);
            }
        });
    }
});
</script>
@endsection
