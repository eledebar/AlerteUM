<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data
      x-init="
        if (localStorage.theme === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
      "
      x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Polices -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">

    <!-- Botón modo claro/oscuro -->
    <button id="theme-toggle"
        class="absolute top-4 right-4 z-50 px-3 py-1 rounded bg-gray-300 text-black dark:bg-gray-800 dark:text-white hover:opacity-80 transition">
        🌞 / 🌙
    </button>

    <div class="min-h-screen">

        {{-- Barre de navigation principale --}}
        @include('layouts.navigation')

        {{-- En-tête de la page (optionnel) --}}
        @isset($header)
            <header class="bg-white shadow dark:bg-gray-800 dark:shadow-md">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Contenu principal --}}
        <main class="py-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
