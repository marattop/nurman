<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NURMAN | Регистрация клиента</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind Play CDN for immediate preview -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --bg-color: #0f1115;
            --card-bg: rgba(255, 255, 255, 0.03);
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --accent-color: #d97706;
            --accent-hover: #b45309;
            --error-color: #ef4444;
            --success-color: #10b981;
            --border-color: rgba(255, 255, 255, 0.1);
            --font-serif: 'Playfair Display', serif;
            --font-sans: 'Inter', sans-serif;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Background Glows */
        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.08) 0%, rgba(217, 119, 6, 0) 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(80px);
        }
        .bg-glow-1 { top: -200px; right: -200px; }
        .bg-glow-2 { bottom: -200px; left: -200px; }

        .register-container {
            width: 100%;
            max-width: 500px;
            z-index: 10;
        }

        .register-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.6s ease-out forwards;
        }

        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-logo {
            font-family: var(--font-serif);
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: block;
        }

        .register-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: var(--accent-color);
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-secondary);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            border-radius: 2px;
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--accent-color);
            background: rgba(255, 255, 255, 0.07);
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 0.9rem;
            transition: var(--transition);
        }

        input:focus + .input-icon {
            color: var(--accent-color);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 2px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -5px rgba(217, 119, 6, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .login-link a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 2px;
            margin-bottom: 24px;
            font-size: 0.8rem;
            border: 1px solid transparent;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .phone-status {
            font-size: 0.7rem;
            margin-top: 4px;
            display: block;
        }

        .text-success { color: var(--success-color); }
        .text-danger { color: var(--error-color); }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .copyright {
            text-align: center;
            margin-top: 30px;
            font-size: 0.7rem;
            color: var(--text-secondary);
            opacity: 0.5;
        }

        /* Адаптация под телефон */
        @media (max-width: 480px) {
            body {
                padding: 20px 16px;
            }
            .register-card {
                padding: 30px 20px;
            }
            .brand-logo {
                font-size: 1.8rem;
            }
            .register-title {
                font-size: 0.65rem;
            }
            input {
                padding: 14px 16px 14px 40px;
                font-size: 1rem;
            }
            .input-icon {
                left: 12px;
                font-size: 1rem;
            }
            .btn-submit {
                padding: 14px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <div class="register-container">
        <div class="register-card">
            <header class="register-header">
                <a href="/" style="text-decoration: none; color: inherit;">
                    <span class="brand-logo">NURMAN</span>
                </a>
                <span class="register-title">Регистрация клиента</span>
            </header>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Проверьте правильность заполнения полей
                </div>
            @endif

            <form method="POST" action="{{ route('client.register') }}" id="registerForm">
                @csrf

                <div class="form-group">
                    <label class="form-label">Ваше имя</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            name="full_name" 
                            value="{{ old('full_name') }}"
                            placeholder="Напр. Александр Иванов"
                            required
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    @error('full_name')<span class="text-danger text-[10px]">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Номер телефона</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="phone"
                            name="phone" 
                            value="{{ old('phone') }}"
                            placeholder="+996XXXXXXXXX"
                            required
                            oninput="checkPhoneAvailability()"
                        >
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                    <div id="phoneCheckResult" class="phone-status"></div>
                    @error('phone')<span class="text-danger text-[10px]">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Пароль</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            name="password" 
                            placeholder="Минимум 6 символов"
                            required
                            minlength="6"
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    @error('password')<span class="text-danger text-[10px]">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Подтверждение пароля</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            placeholder="Повторите пароль"
                            required
                        >
                        <i class="fas fa-check-double input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span>Создать профиль</span>
                    <i class="fas fa-user-plus"></i>
                </button>
            </form>

            <div class="login-link">
                Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
            </div>

            <div class="copyright">
                &copy; {{ date('Y') }} NURMAN. Эстетика вашего стиля.
            </div>
        </div>
    </div>

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
                        resultDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>' + data.message + '</span>';
                        document.getElementById('phone').style.borderColor = 'var(--error-color)';
                    } else {
                        resultDiv.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>' + data.message + '</span>';
                        document.getElementById('phone').style.borderColor = 'var(--success-color)';
                    }
                });
            }, 500);
        }

        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.querySelector('span').textContent = 'Создание...';
            btn.querySelector('i').className = 'fas fa-spinner fa-spin';
        });
    </script>
</body>
</html>
