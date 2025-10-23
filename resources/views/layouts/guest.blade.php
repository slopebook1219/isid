<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased flex flex-col min-h-screen">
        <header class="w-full px-8 py-6 bg-white border-b border-gray-200">
            <div class="flex items-center">
                <div>
                    <a href="/">
                        <img src="{{ asset('images/dentsusoken_logo.png') }}" alt="DENTSUSOKEN_LOGO" class="h-11">
                    </a>
                </div>
            </div>
        </header>
        <main class="flex-grow flex flex-col sm:justify-center items-center w-full">
            <div class="w-full sm:max-w-md mt-8 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </main>
    </body>
</html>
