<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Регистрация - NURMAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: var(--accent-primary);
            --primary-dark: var(--accent-primary-dark);
            --bg-primary: #0f1724;
            --bg-secondary: #1a2332;
            --bg-card: #1f2937;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            max-width: 450px;
            width: 100%;
            padding: 2rem;
        }

        .register-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }

        .register-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .register-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .form-control {
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-control.is-invalid {
            border-color: var(--accent-red);
        }

        .invalid-feedback {
            color: var(--accent-red);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .btn-register {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .phone-hint {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-logo">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h1 class="register-title">Регистрация</h1>
                <p class="register-subtitle">Создайте личный кабинет покупателя</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>Ошибка!</strong> Проверьте правильность заполнения полей.
                </div>
            @endif

            <form method="POST" action="{{ route('client.register') }}" id="registerForm">
                @csrf

                <div class="form-group">
                    <label for="full_name" class="form-label">
                        <i class="bi bi-person me-1"></i> ФИО
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('full_name') is-invalid @enderror" 
                        id="full_name" 
                        name="full_name" 
                        value="{{ old('full_name') }}"
                        placeholder="Иванов Иван Иванович"
                        required
                    >
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">
                        <i class="bi bi-phone me-1"></i> Телефон
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('phone') is-invalid @enderror" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone') }}"
                        placeholder="+996555123456"
                        required
                        oninput="checkPhoneAvailability()"
                    >
                    <div class="phone-hint">Телефон будет использоваться для входа</div>
                    <div id="phoneCheckResult"></div>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i> Пароль
                    </label>
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        placeholder="Минимум 6 символов"
                        required
                        minlength="6"
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="bi bi-lock-fill me-1"></i> Подтверждение пароля
                    </label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Повторите пароль"
                        required
                    >
                </div>

                <button type="submit" class="btn-register">
                    <i class="bi bi-person-plus me-2"></i> Зарегистрироваться
                </button>
            </form>

            <div class="login-link">
                Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let phoneCheckTimeout;

        function checkPhoneAvailability() {
            const phone = document.getElementById('phone').value;
            const resultDiv = document.getElementById('phoneCheckResult');
            
            clearTimeout(phoneCheckTimeout);
            
            if (phone.length < 10) {
                resultDiv.innerHTML = '';
                return;
            }

            phoneCheckTimeout = setTimeout(() => {
                fetch('/client/register/check-phone', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ phone: phone })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        resultDiv.innerHTML = '<div class="invalid-feedback d-block"><i class="bi bi-x-circle me-1"></i>' + data.message + '</div>';
                        document.getElementById('phone').classList.add('is-invalid');
                    } else {
                        resultDiv.innerHTML = '<div class="text-success"><i class="bi bi-check-circle me-1"></i>' + data.message + '</div>';
                        document.getElementById('phone').classList.remove('is-invalid');
                    }
                });
            }, 500);
        }
    </script>
</body>
</html>
