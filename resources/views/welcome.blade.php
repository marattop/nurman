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

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="anonymous" />

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
            .map-container .leaflet-container {
                filter: invert(90%) hue-rotate(180deg) brightness(95%) contrast(90%);
            }
            #map {
                width: 100%;
                height: 100%;
            }
            .qr-wrapper {
                background: white;
                padding: 10px;
                border-radius: 8px;
                display: inline-block;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            }
            .icon { width: 1em; height: 1em; fill: currentColor; display: inline-block; vertical-align: middle; }
            .icon-lg { width: 1.25em; height: 1.25em; }
            .icon-xl { width: 1.5em; height: 1.5em; }
            .icon-2xl { width: 2em; height: 2em; }
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
                    <svg class="icon text-green-600" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.8-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                    WhatsApp
                </a>
                <a href="#discount" class="w-full sm:w-auto px-6 py-3.5 md:px-8 md:py-4 border border-white text-white font-semibold rounded-sm hover:bg-white/10 transition-all uppercase tracking-widest text-xs md:text-sm">Карта скидок</a>
            </div>
        </div>

        <div class="absolute bottom-6 md:bottom-10 left-1/2 -translate-x-1/2 animate-bounce text-gray-400 hidden md:block">
            <svg class="icon-xl" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 md:py-24 px-6 bg-[#11141a]">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-12 text-center">
                <div class="space-y-4 group">
                    <div class="h-14 w-14 md:h-16 md:w-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:bg-amber-500/10 transition-colors">
                        <svg class="icon-2xl text-amber-500" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-serif font-bold uppercase tracking-wider">Качество</h3>
                    <p class="text-sm md:text-base text-gray-400">Мы используем только лучшие материалы, чтобы ваша одежда служила долго.</p>
                </div>
                <div class="space-y-4 group">
                    <div class="h-14 w-14 md:h-16 md:w-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:bg-amber-500/10 transition-colors">
                        <svg class="icon-2xl text-amber-500" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-serif font-bold uppercase tracking-wider">Стиль</h3>
                    <p class="text-sm md:text-base text-gray-400">Современные тренды и классические решения в любой ситуации.</p>
                </div>
                <div class="space-y-4 group">
                    <div class="h-14 w-14 md:h-16 md:w-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:bg-amber-500/10 transition-colors">
                        <svg class="icon-2xl text-amber-500" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.1 170.9c20.1-21.3 49-34.2 80.9-34.2s60.8 12.9 80.9 34.2c6.1 6.4 5.8 16.5-.6 22.6s-16.5 5.8-22.6-.6C292.9 177.1 274.9 168 256 168s-36.9 9.1-51.8 24.9c-6.1 6.4-16.2 6.7-22.6.6s-6.7-16.2-.6-22.6zM364.3 316.4C339.2 347.7 300.4 368 256 368s-83.2-20.3-108.3-51.6c-5.5-6.9-4.4-16.9 2.5-22.4s16.9-4.4 22.4 2.5c19.3 24.1 49.4 39.5 83.4 39.5s64.1-15.4 83.4-39.5c5.5-6.9 15.5-8 22.4-2.5s8 15.5 2.5 22.4z"/></svg>
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
                        <svg class="icon text-amber-500" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
                        <span class="text-xs md:text-base text-gray-200">Накопительная система скидок</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <svg class="icon text-amber-500" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
                        <span class="text-xs md:text-base text-gray-200">Предложения для именинников</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <svg class="icon text-amber-500" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
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
                    <div id="map" class="w-full h-full"></div>
                </div>
                <div class="glass p-8 md:p-10 rounded-sm flex flex-col justify-center border-l-4 border-amber-600">
                    <h3 class="text-lg md:text-xl font-serif mb-6 md:mb-8 text-amber-500 uppercase tracking-widest">Адрес</h3>
                    <div class="space-y-6 md:space-y-8">
                        <div class="flex items-start gap-4 md:gap-5">
                            <svg class="icon-xl text-amber-500 mt-1.5" viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>
                            <div>
                                <p class="text-white font-bold text-base md:text-lg mb-1 tracking-wide">г. Каракол</p>
                                <p class="text-sm md:text-base text-gray-400 leading-relaxed">ул. Алдашева 66</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 md:gap-5">
                            <svg class="icon-xl text-amber-500 mt-1.5" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg>
                            <div>
                                <p class="text-white font-bold text-base md:text-lg mb-1 tracking-wide">График</p>
                                <p class="text-sm md:text-base text-gray-400 leading-relaxed">09:00 — 18:00</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 md:gap-5">
                            <svg class="icon-xl text-amber-500 mt-1.5" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>
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
    <footer class="py-16 md:py-24 px-6 border-t border-white/5 bg-[#11141a]">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 md:gap-12 text-white">
                <div class="col-span-1 lg:col-span-1 text-center md:text-left">
                    <div class="text-2xl font-serif font-bold tracking-widest mb-6">NURMAN</div>
                    <p class="text-sm text-gray-400 mb-8 italic leading-relaxed">
                        Премиальная мужская одежда. Качество, проверенное временем.
                    </p>
                    <div class="flex gap-4 justify-center md:justify-start">
                        <a href="https://www.instagram.com/nurman_karakol" target="_blank" class="w-10 h-10 md:w-12 md:h-12 rounded-full glass flex items-center justify-center hover:bg-amber-500/20 transition-all text-white border border-white/10">
                            <svg class="icon-lg" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                        </a>
                        <a href="https://wa.me/996700955757" target="_blank" class="w-10 h-10 md:w-12 md:h-12 rounded-full glass flex items-center justify-center hover:bg-amber-500/20 transition-all text-white border border-white/10">
                            <svg class="icon-lg" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.8-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
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

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map', {
                center: [42.490812, 78.394144],
                zoom: 17
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
                maxZoom: 19
            }).addTo(map);

            L.marker([42.490812, 78.394144]).addTo(map)
                .bindPopup('<b style="color:#000">NURMAN</b><br><span style="color:#333">ул. Алдашева 66, Каракол</span>')
                .openPopup();
        });
    </script>
</body>
</html>
