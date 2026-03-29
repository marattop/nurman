<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NURMAN | Вход в систему</title>
    
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
            overflow-x: hidden;
            position: relative;
        }

        /* Background Effects */
        .bg-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.1) 0%, rgba(217, 119, 6, 0) 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(60px);
        }

        .bg-glow-1 { top: -100px; right: -100px; }
        .bg-glow-2 { bottom: -100px; left: -100px; }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            z-index: 10;
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-logo {
            font-family: var(--font-serif);
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: block;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.4em;
            color: var(--accent-color);
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-secondary);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 2px;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--accent-color);
            background: rgba(255, 255, 255, 0.08);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus + .input-icon {
            color: var(--accent-color);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 0.85rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .remember-me input {
            accent-color: var(--accent-color);
        }

        .forgot-link {
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .forgot-link:hover {
            color: var(--accent-color);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 2px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -5px rgba(217, 119, 6, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 2px;
            margin-bottom: 24px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .register-hint {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .register-hint a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }

        .register-hint a:hover {
            text-decoration: underline;
        }

        .credentials-box {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .credentials-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--text-secondary);
            text-align: center;
            margin-bottom: 12px;
        }

        .credentials-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .credential-tag {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            padding: 4px 10px;
            border-radius: 2px;
            font-size: 0.7rem;
            color: var(--text-secondary);
            font-family: monospace;
        }

        .copyright {
            text-align: center;
            margin-top: 30px;
            font-size: 0.7rem;
            color: var(--text-secondary);
            opacity: 0.6;
            letter-spacing: 0.05em;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            animation: fadeIn 0.6s ease-out forwards;
        }
    </style>
</head>
<body>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <div class="login-container">
        <div class="login-card">
            <header class="login-header">
                <a href="/" style="text-decoration: none; color: inherit;">
                    <span class="brand-logo">NURMAN</span>
                </a>
                <span class="brand-subtitle">Эстетика и стиль</span>
            </header>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Логин</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            name="username" 
                            placeholder="Введите имя пользователя" 
                            value="{{ old('username') }}"
                            required 
                            autofocus
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Пароль</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            name="password" 
                            placeholder="Введите пароль" 
                            required
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Запомнить меня</span>
                    </label>
                    <a href="#" class="forgot-link">Забыли пароль?</a>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span>Войти в систему</span>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </form>

            <div class="register-hint">
                Нет аккаунта? 
                <a href="{{ route('client.register') }}">Создать профиль</a>
            </div>

            <div class="credentials-box">
                <p class="credentials-title">Тестовый доступ</p>
                <div class="credentials-list">
                    <span class="credential-tag">admin / admin123</span>
                    <span class="credential-tag">seller1 / seller123</span>
                    <span class="credential-tag">warehouse1 / warehouse123</span>
                </div>
            </div>

            <div class="copyright">
                &copy; {{ date('Y') }} NURMAN. Все права защищены.
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.querySelector('span').textContent = 'Подключение...';
            btn.querySelector('i').className = 'fas fa-spinner fa-spin';
        });
    </script>
</body>
</html>
