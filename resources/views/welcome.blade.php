<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>NURMAN - Мужская одежда и костюмы в Караколе</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind Play CDN for immediate preview -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Playfair Display', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{--
        Vite Error Fix:
        Мы убрали вызов @vite, чтобы не было ошибок в консоли (ERR_CONNECTION_REFUSED),
        так как стили теперь работают через CDN для максимальной стабильности.
    --}}
    
    <style type="text/tailwindcss">
        @layer base {
            body { @apply bg-[#11141a] text-white overflow-x-hidden; }
        }
        @layer components {
            .hero-bg {
                background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=1920&auto=format&fit=crop');
                background-size: cover;
                background-position: center;
            }
            .glass {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .map-container iframe {
                filter: invert(90%) hue-rotate(180deg) brightness(95%) contrast(90%);
            }
            .qr-wrapper {
                background: white;
                padding: 10px;
                border-radius: 8px;
                display: inline-block;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            }
        }
    </style>
</head>
<body class="bg-[#11141a] text-white overflow-x-hidden">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass px-4 py-3 md:px-6 md:py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl md:text-2xl font-serif font-bold tracking-widest text-white">NURMAN</span>
                <span class="text-[8px] md:text-[10px] uppercase tracking-[0.3em] text-gray-400 mt-1 hidden xs:block">Karakol</span>
            </div>
            
            <div class="flex items-center gap-3 md:gap-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-[10px] md:text-sm font-medium hover:text-amber-500 transition-colors uppercase tracking-wider">Кабинет</a>
                    @else
                        <a href="{{ route('login') }}" class="text-[10px] md:text-sm font-medium hover:text-amber-500 transition-colors uppercase tracking-wider border-b border-amber-500 pb-0.5">Войти</a>
                        @if (Route::has('client.register'))
                            <a href="{{ route('client.register') }}" class="px-3 py-1.5 md:px-5 md:py-2 bg-amber-600 hover:bg-amber-700 rounded-sm text-[10px] md:text-sm font-medium transition-all uppercase tracking-wider">Регистрация</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center hero-bg pt-20">
        <div class="text-center px-6 max-w-4xl">
            <h2 class="text-amber-500 uppercase tracking-[0.3em] md:tracking-[0.4em] text-xs md:text-sm mb-4 font-medium">Эстетика и стиль</h2>
            <h1 class="text-4xl md:text-7xl font-serif mb-6 md:mb-8 leading-tight text-white uppercase">Мужская одежда <br>& Костюмы</h1>
            <p class="text-sm md:text-xl text-gray-200 mb-8 md:mb-10 max-w-2xl mx-auto leading-relaxed">
                Индивидуальный подход к каждому клиенту. Мы подберем для вас идеальный образ, который подчеркнет вашу уверенность и статус.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="https://wa.me/996700955757" target="_blank" class="w-full sm:w-auto px-6 py-3.5 md:px-8 md:py-4 bg-white text-black font-semibold rounded-sm hover:bg-gray-200 transition-all uppercase tracking-widest text-xs md:text-sm flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp text-lg text-green-600"></i>
                    WhatsApp
                </a>
                <a href="#discount" class="w-full sm:w-auto px-6 py-3.5 md:px-8 md:py-4 border border-white text-white font-semibold rounded-sm hover:bg-white/10 transition-all uppercase tracking-widest text-xs md:text-sm">Карта скидок</a>
            </div>
        </div>
        
        <div class="absolute bottom-6 md:bottom-10 left-1/2 -translate-x-1/2 animate-bounce text-gray-400 hidden md:block">
            <i class="fas fa-chevron-down text-xl"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 md:py-24 px-6 bg-[#11141a]">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-12 text-center">
                <div class="space-y-4 group">
                    <div class="h-14 w-14 md:h-16 md:w-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:bg-amber-500/10 transition-colors">
                        <i class="fas fa-check-circle text-xl md:text-2xl text-amber-500"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-serif font-bold uppercase tracking-wider">Качество</h3>
                    <p class="text-sm md:text-base text-gray-400">Мы используем только лучшие материалы, чтобы ваша одежда служила долго.</p>
                </div>
                <div class="space-y-4 group">
                    <div class="h-14 w-14 md:h-16 md:w-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:bg-amber-500/10 transition-colors">
                        <i class="fas fa-search text-xl md:text-2xl text-amber-500"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-serif font-bold uppercase tracking-wider">Стиль</h3>
                    <p class="text-sm md:text-base text-gray-400">Современные тренды и классические решения в любой ситуации.</p>
                </div>
                <div class="space-y-4 group">
                    <div class="h-14 w-14 md:h-16 md:w-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:bg-amber-500/10 transition-colors">
                        <i class="fas fa-smile text-xl md:text-2xl text-amber-500"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-serif font-bold uppercase tracking-wider">Комфорт</h3>
                    <p class="text-sm md:text-base text-gray-400">Одежда, в которой удобно. Идеальная посадка и свобода движений.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Discount Section -->
    <section id="discount" class="py-16 md:py-24 px-6 bg-gradient-to-b lg:bg-gradient-to-r from-amber-900/10 to-transparent">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12 md:gap-16">
            <div class="flex-1 text-center lg:text-left">
                <h2 class="text-amber-500 uppercase tracking-widest text-xs md:text-sm mb-4">Программа лояльности</h2>
                <h2 class="text-3xl md:text-4xl font-serif mb-6 leading-tight uppercase">Ваша персональная <br class="hidden md:block">скидочная карта</h2>
                <p class="text-sm md:text-lg text-gray-300 mb-8 leading-relaxed">
                    Станьте нашим постоянным клиентом и получите доступ к эксклюзивным привилегиям. 
                    Зарегистрируйтесь, чтобы получить виртуальную карту с уникальным QR-кодом.
                </p>
                <div class="space-y-4 mb-10 text-white inline-block text-left">
                    <div class="flex items-center gap-4">
                        <i class="fas fa-check text-amber-500 text-xs"></i>
                        <span class="text-xs md:text-base text-gray-200">Накопительная система скидок</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <i class="fas fa-check text-amber-500 text-xs"></i>
                        <span class="text-xs md:text-base text-gray-200">Предложения для именинников</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <i class="fas fa-check text-amber-500 text-xs"></i>
                        <span class="text-xs md:text-base text-gray-200">Доступ к новым коллекциям</span>
                    </div>
                </div>
                <div class="block">
                    <a href="{{ route('client.register') }}" class="inline-block px-8 py-4 md:px-10 md:py-5 bg-amber-600 hover:bg-amber-700 rounded-sm font-bold transition-all uppercase tracking-widest text-xs md:text-sm">Получить карту</a>
                </div>
            </div>
            
            <div class="flex-1 w-full max-w-lg relative group order-first lg:order-last">
                <!-- Loyalty Card Design -->
                <div class="w-full aspect-[1.6/1] glass rounded-xl md:rounded-2xl p-5 md:p-8 flex flex-col justify-between shadow-[0_20px_50px_rgba(0,0,0,0.5)] relative z-10 overflow-hidden border border-white/20">
                    <div class="absolute top-0 right-0 p-16 bg-amber-500/10 blur-[80px] rounded-full -mr-20 -mt-20"></div>
                    
                    <div class="flex justify-between items-start">
                        <div class="text-lg md:text-2xl font-serif font-bold tracking-widest text-white">NURMAN</div>
                        <div class="text-right">
                            <div class="text-[8px] md:text-[10px] uppercase tracking-widest text-amber-500 font-bold mb-1">Status</div>
                            <div class="text-[10px] md:text-sm font-medium uppercase tracking-wider text-white">Gold Member</div>
                        </div>
                    </div>

                    <!-- QR Code and Percentage in the middle -->
                    <div class="flex flex-col items-center justify-center py-2 md:py-4 relative">
                        <div class="qr-wrapper mb-2 transform scale-75 md:scale-100">
                            <div id="qrcode" class="w-20 h-24 md:w-24 md:h-24 bg-white flex items-center justify-center relative overflow-hidden">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=NURMAN-KARAKOL&color=000&bgcolor=fff" alt="QR" class="w-full h-full p-1">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-white px-1 py-0.5 border border-black text-[6px] md:text-[8px] font-bold text-black transform -rotate-12 shadow-sm">NURMAN</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-2xl md:text-3xl font-serif font-bold text-amber-500 tracking-tighter">10% <span class="text-[10px] md:text-xs uppercase tracking-widest text-gray-400">Discount</span></div>
                    </div>

                    <div class="flex justify-between items-end border-t border-white/10 pt-3 md:pt-4">
                        <div>
                            <div class="text-[6px] md:text-[8px] uppercase tracking-widest text-gray-500 mb-1">Loyalty Code</div>
                            <div class="text-[10px] md:text-sm font-mono tracking-widest text-white">NRMN-KL-{{ rand(1000, 9999) }}-PRO</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[6px] md:text-[8px] uppercase tracking-widest text-gray-500 mb-1">VALID IN</div>
                            <div class="text-[8px] md:text-[10px] font-bold text-white uppercase tracking-widest">Karakol</div>
                        </div>
                    </div>
                </div>
                <!-- Card Glow -->
                <div class="absolute inset-0 bg-amber-500/10 blur-[100px] -z-10 group-hover:bg-amber-500/20 transition-all duration-500"></div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 md:py-24 px-6 border-t border-white/5 bg-[#11141a]">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-serif mb-10 md:mb-12 text-center text-white uppercase tracking-widest">Локация магазина</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-12 items-stretch">
                <div class="lg:col-span-2 map-container w-full h-[350px] md:h-[450px] rounded-sm overflow-hidden border border-white/10 shadow-2xl">
                    <iframe src="https://yandex.ru/map-widget/v1/?ll=78.394144%2C42.490812&z=17&pt=78.394144%2C42.490812%2Cpm2blm&mode=search&text=Каракол, ул. Алдашева 66" width="100%" height="100%" frameborder="0" allowfullscreen="true"></iframe>
                </div>
                <div class="glass p-8 md:p-10 rounded-sm flex flex-col justify-center border-l-4 border-amber-600">
                    <h3 class="text-lg md:text-xl font-serif mb-6 md:mb-8 text-amber-500 uppercase tracking-widest">Адрес</h3>
                    <div class="space-y-6 md:space-y-8">
                        <div class="flex items-start gap-4 md:gap-5">
                            <i class="fas fa-map-marker-alt text-amber-500 mt-1.5 text-base md:text-lg"></i>
                            <div>
                                <p class="text-white font-bold text-base md:text-lg mb-1 tracking-wide">г. Каракол</p>
                                <p class="text-sm md:text-base text-gray-400 leading-relaxed">ул. Алдашева 66</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 md:gap-5">
                            <i class="fas fa-clock text-amber-500 mt-1.5 text-base md:text-lg"></i>
                            <div>
                                <p class="text-white font-bold text-base md:text-lg mb-1 tracking-wide">График</p>
                                <p class="text-sm md:text-base text-gray-400 leading-relaxed">09:00 — 18:00</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 md:gap-5">
                            <i class="fas fa-phone text-amber-500 mt-1.5 text-base md:text-lg"></i>
                            <div>
                                <p class="text-white font-bold text-base md:text-lg mb-1 tracking-wide">Телефон</p>
                                <p class="text-sm md:text-base text-gray-400 leading-relaxed">+996 700 955 757</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-16 md:py-24 px-6 border-t border-white/5">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 md:gap-12 text-white">
                <div class="col-span-1 lg:col-span-1 text-center md:text-left">
                    <div class="text-2xl font-serif font-bold tracking-widest mb-6">NURMAN</div>
                    <p class="text-sm text-gray-400 mb-8 italic leading-relaxed">
                        Премиальная мужская одежда. Качество, проверенное временем.
                    </p>
                    <div class="flex gap-4 justify-center md:justify-start">
                        <a href="https://www.instagram.com/nurman_karakol" target="_blank" class="w-10 h-10 md:w-12 md:h-12 rounded-full glass flex items-center justify-center hover:bg-amber-500/20 transition-all text-white border border-white/10">
                            <i class="fab fa-instagram text-lg md:text-xl"></i>
                        </a>
                        <a href="https://wa.me/996700955757" target="_blank" class="w-10 h-10 md:w-12 md:h-12 rounded-full glass flex items-center justify-center hover:bg-amber-500/20 transition-all text-white border border-white/10">
                            <i class="fab fa-whatsapp text-lg md:text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div class="text-center md:text-left">
                    <h4 class="text-xs font-bold mb-6 md:mb-8 uppercase tracking-[0.2em] text-amber-500">Магазин</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Каракол, ул. Алдашева 66
                    </p>
                </div>
                
                <div class="text-center md:text-left">
                    <h4 class="text-xs font-bold mb-6 md:mb-8 uppercase tracking-[0.2em] text-amber-500">Время</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Без выходных <br>
                        09:00 — 18:00
                    </p>
                </div>
                
                <div class="text-center md:text-left">
                    <h4 class="text-xs font-bold mb-6 md:mb-8 uppercase tracking-[0.2em] text-amber-500">Контакты</h4>
                    <p class="text-sm text-white font-medium mb-2 tracking-wide">+996 700 955 757</p>
                    <p class="text-gray-400 text-xs">nurman.karakol@mail.com</p>
                </div>
            </div>
            
            <div class="mt-16 md:mt-20 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-gray-500 text-[10px] uppercase tracking-widest text-center">
                    &copy; {{ date('Y') }} NURMAN CLOTHING STORE.
                </p>
                <div class="flex gap-8">
                    <a href="#" class="text-gray-500 text-[10px] uppercase tracking-widest hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="text-gray-500 text-[10px] uppercase tracking-widest hover:text-white transition-colors">Terms</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>