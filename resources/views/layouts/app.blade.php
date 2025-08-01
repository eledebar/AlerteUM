<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-cloak>
<script src="https://kit.fontawesome.com/3e9f0f6842.js" crossorigin="anonymous"></script>

<head>
    <script>
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const savedTheme = localStorage.theme;
        const shouldUseDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
        if (shouldUseDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <style>
        [x-cloak] { display: none !important; }

        html {
            background-color: #111827; 
        }

        @media (prefers-color-scheme: dark) {
            html, body {
                background-color: #111827;
                color: white;
            }
        }
    </style>
</head>
<body
    x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
    x-init="document.body.removeAttribute('x-cloak')"
    x-cloak
    class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white"
>
    <button x-on:click="
            darkMode = !darkMode;
            if (darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            }
        "
        class="absolute top-4 right-4 z-50 p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
        aria-label="Cambiar tema"
    >
        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg"
             class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 3v1m0 16v1m8.66-12.66l-.707.707M4.05 19.95l-.707.707M21 12h-1M4 12H3m16.66 
                  4.66l-.707-.707M4.05 4.05l-.707-.707M12 7a5 5 0 000 10 5 5 0 000-10z"/>
        </svg>

        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg"
             class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
        </svg>
    </button>

    <div x-show="true" x-cloak>
        <div class="min-h-screen">
            {{-- Barra de navegación --}}
            @include('layouts.navigation')

            {{-- Cabecera de la página (opcional) --}}
            @isset($header)
                <header class="bg-white shadow dark:bg-gray-800 dark:shadow-md">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Contenido principal --}}
            <main class="py-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
