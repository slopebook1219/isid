<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>クイズいいせん行きまSHOW!!!</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=noto-sans-jp:400,700,900" rel="stylesheet" />

        <!-- Styles / Scripts -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white flex flex-col">
        <!-- Header -->
        <header class="w-full px-8 py-6 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between"> 
                <!-- Logo -->
                <div>
                    <img src="{{ Vite::asset('public/images/dentsusoken_logo.png') }}" alt="DENTSUSOKEN_LOGO" class="h-11">
                </div>

                <!-- Navigation -->
            @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                                class="px-6 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
                        >
                                ダッシュボード
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                                class="px-6 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
                        >
                                ログイン
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                    class="px-6 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
                                >
                                    新規登録
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-8">
            <div class="text-center">
                <h1 class="text-6xl md:text-7xl lg:text-8xl font-black text-gray-900 leading-tight">
                    クイズいいせん<br>
                    行きまSHOW!!!
                </h1>
                </div>
            </main>
    </body>
</html>
