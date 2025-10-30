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
                    <img src="{{ asset('images/dentsusoken_logo.png') }}" alt="DENTSUSOKEN_LOGO" class="h-11">
                </div>

                <!-- Navigation -->
                @auth
                <nav class="flex items-center gap-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                           class="px-6 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
                           onclick="event.preventDefault();
                                    this.closest('form').submit();">
                            logout
                        </a>
                    </form>
                </nav>
                @endauth
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-8 py-12">
            <div class="flex flex-wrap justify-center items-center gap-10">

                <a class="flex justify-center items-center w-64 h-64 sm:w-80 sm:h-80  
                          bg-lime-500 text-white
                          rounded-2xl shadow-lg
                          transition-all duration-300 
                          transform hover:-translate-y-1 hover:shadow-2xl
                          focus:outline-none focus:ring-2 focus:ring-lime-700 focus:ring-opacity-50"
                          href="{{ route('questions.index') }}">
                    <span class="text-4xl font-bold">問題</span>
                </a>

                <a class="flex justify-center items-center w-64 h-64 sm:w-80 sm:h-80
                          bg-lime-500 text-white
                          rounded-2xl shadow-lg 
                          transition-all duration-300 
                          transform hover:-translate-y-1 hover:shadow-2xl
                          focus:outline-none focus:ring-2 focus:ring-lime-700 focus:ring-opacity-50"
                          href="{{ route('rooms.index') }}" >
                    
                    <span class="text-4xl font-bold">ルーム</span>
                </a>

            </div>
        </main>
    </body>
</html>