<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NURMAN - Авторизация')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        bg: '#0f172a',
                        accent: '#38bdf8',
                        'accent-hover': '#0ea5e9',
                        text: '#f8fafc',
                        muted: '#94a3b8',
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(56,189,248,0.2)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')
</body>
</html>
