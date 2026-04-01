@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header text-center mb-6">
            <h1 class="text-2xl font-bold">Второй этап защиты</h1>
            <p class="text-muted">Введите секретный ключ для входа в систему</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-500 p-4 rounded-xl mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.verify.post') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-muted mb-2">Секретный ключ (Текущий год)</label>
                    <input type="password" name="secret_key" required autofocus 
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-center text-2xl tracking-[1em] focus:outline-none focus:border-accent"
                        placeholder="••••">
                </div>

                <button type="submit" class="w-full bg-accent hover:bg-accent-hover text-white font-bold py-3.5 rounded-xl transition-all shadow-glow">
                    ПОДТВЕРДИТЬ ВХОД
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-muted hover:text-white underline">Отмена и выход</button>
            </form>
        </div>
    </div>
</div>

<style>
    .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; }
    .auth-card { width: 100%; max-width: 400px; padding: 2rem; background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); }
    .text-muted { color: #94a3b8; }
    .bg-accent { background: #38bdf8; }
    .hover\:bg-accent-hover:hover { background: #0ea5e9; }
</style>
@endsection
