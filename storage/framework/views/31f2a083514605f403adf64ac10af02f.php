<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>NURMAN | Вход в систему</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ———————————————— КОРНЕВЫЕ СТИЛИ И ЦВЕТА (NURMAN System) ———————————————— */
        :root {
            --bg-color: #0f172a;
            --card-color: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --accent-primary: var(--accent-primary);
            --accent-hover: var(--accent-primary-dark);
            --error-color: var(--accent-red);
            --success-color: var(--accent-primary);
            --border-color: #334155;
            --font-family: 'Inter', sans-serif;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-color);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ———————————————— ФОНОВЫЙ ЭФФЕКТ (Анимированные частицы) ———————————————— */
        .background-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
        .particle {
            position: absolute;
            background-color: var(--accent-primary);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s infinite linear;
        }

        /* ———————————————— КАРТА ВХОДА (Main Container) ———————————————— */
        .login-card {
            background-color: var(--card-color);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            z-index: 10;
            position: relative;
            border: 1px solid var(--border-color);
            opacity: 0;
            transform: translateY(20px) scale(0.95);
            animation: cardAppear 0.6s forwards 0.2s ease-out;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -1.5px;
            color: var(--accent-primary);
            margin-bottom: 0.5rem;
            display: inline-block;
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }

        .login-header h2 {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        /* ———————————————— ФОРМА ВВОДА (Input Styles) ———————————————— */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.1rem;
            transition: var(--transition);
        }

        input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background-color: rgba(15, 23, 42, 0.5);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: var(--font-family);
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            background-color: rgba(15, 23, 42, 0.8);
        }

        input:focus + .input-icon {
            color: var(--accent-primary);
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
        }

        /* Laravel ошибки */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        /* ———————————————— ОПЦИИ И КНОПКА (Actions) ———————————————— */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .remember-me {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .remember-me input {
            width: auto;
            margin-right: 0.5rem;
            accent-color: var(--accent-primary);
            border: none;
        }

        .forgot-password {
            color: var(--accent-primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .forgot-password:hover {
            color: var(--accent-hover);
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background-color: var(--accent-primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: var(--font-family);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        }

        .btn-submit:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            left: 0;
            top: 100%;
            transition: all 0.3s;
        }

        .btn-submit:active:after {
            top: 0;
            opacity: 0;
            transition: 0s;
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* ———————————————— ПОДСКАЗКИ (Credentials Hint) ———————————————— */
        .credentials-hint {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .hint-title {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-align: center;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .hint-credentials {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
        }

        .credential {
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-secondary);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-family: 'SF Mono', Monaco, Consolas, monospace;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .credential:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .copyright {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        /* ———————————————— АНИМАЦИИ (Keyframes) ———————————————— */
        @keyframes cardAppear {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes float {
            0% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-100px) translateX(50px); }
            100% { transform: translateY(0) translateX(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .error-shake {
            animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
            border-color: var(--error-color) !important;
        }
    </style>
</head>
<body>
    <div class="background-particles" id="particles"></div>

    <div class="login-card" id="loginCard">
        <div class="login-header">
            <div class="brand-logo">NURMAN</div>
            <h2>Вход в панель управления</h2>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo e($errors->first()); ?></span>
            </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="<?php echo e(route('login.post')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Имя пользователя" 
                    value="<?php echo e(old('username')); ?>"
                    required 
                    autofocus
                >
                <i class="fas fa-user input-icon"></i>
                <div class="error-message" id="usernameError">Введите имя пользователя</div>
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Пароль" 
                    required
                >
                <i class="fas fa-lock input-icon"></i>
                <div class="error-message" id="passwordError">Введите пароль</div>
            </div>

            <div class="form-actions">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <span for="remember">Запомнить</span>
                </label>
                <a href="#" class="forgot-password">Забыли пароль?</a>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span>Войти</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Нет аккаунта? 
                <a href="<?php echo e(route('client.register')); ?>" style="color: var(--accent-primary); text-decoration: none; font-weight: 600;">
                    Зарегистрироваться
                </a>
            </p>
        </div>

        <div class="credentials-hint">
            <p class="hint-title">Тестовые учётные записи</p>
            <div class="hint-credentials">
                <span class="credential">admin / admin123</span>
                <span class="credential">seller1 / seller123</span>
                <span class="credential">warehouse1 / warehouse123</span>
            </div>
        </div>

        <div class="copyright">
            © <?php echo e(date('Y')); ?> NURMAN. Все права защищены.
        </div>
    </div>

    <script>
        // 1. СОЗДАНИЕ ФОНОВЫХ ЧАСТИЦ
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 20;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const size = Math.random() * 5 + 2 + 'px';
                particle.style.width = size;
                particle.style.height = size;

                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';

                particle.style.animationDelay = Math.random() * 10 + 's';
                particle.style.animationDuration = Math.random() * 20 + 10 + 's';

                container.appendChild(particle);
            }
        }

        // 2. ОБРАБОТКА ФОРМЫ
        function handleLogin(event) {
            event.preventDefault();

            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const loginCard = document.getElementById('loginCard');
            const submitBtn = document.getElementById('submitBtn');

            // Сброс ошибок
            usernameInput.classList.remove('error-shake');
            passwordInput.classList.remove('error-shake');
            let hasError = false;

            // Валидация
            if (usernameInput.value.trim().length < 1) {
                usernameInput.classList.add('error-shake');
                hasError = true;
            }
            if (passwordInput.value.length < 1) {
                passwordInput.classList.add('error-shake');
                hasError = true;
            }

            if (hasError) {
                loginCard.classList.add('error-shake');
                setTimeout(() => loginCard.classList.remove('error-shake'), 600);
                return false;
            }

            // Отправка формы
            submitBtn.disabled = true;
            const btnText = submitBtn.querySelector('span');
            const btnIcon = submitBtn.querySelector('i');
            btnText.textContent = 'Подключение...';
            btnIcon.className = 'fas fa-spinner fa-spin';

            // Разрешаем форме отправиться
            setTimeout(() => {
                event.target.submit();
            }, 500);
        }

        // Инициализация
        window.onload = function() {
            createParticles();
            
            // Автофокус на поле пароля если логин заполнен
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            if (usernameInput && usernameInput.value) {
                passwordInput.focus();
            }
        };
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\nurman\resources\views/auth/login.blade.php ENDPATH**/ ?>