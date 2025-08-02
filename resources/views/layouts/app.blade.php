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
    <link href="https://fonts.cdnfonts.com/css/opendyslexic" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <style>
        [x-cloak] { display: none !important; }

        html {
            background-color: #111827;
            font-size: 16px;
            scroll-behavior: smooth;
        }

        @media (prefers-color-scheme: dark) {
            html, body {
                background-color: #111827;
                color: white;
            }
        }

        #accessibility-panel {
            transition: transform 0.3s ease;
        }

        .access-panel-hidden {
            transform: translateX(100%);
        }

        .access-panel-visible {
            transform: translateX(0);
        }

        .access-control-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 0.5rem 1rem;
            background-color: #1f2937;
            color: white;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .access-control-btn:hover {
            background-color: #374151;
        }

        body.contrast-high, body.contrast-high * {
            background-color: #000 !important;
            color: #fff !important;
            border-color: #fff !important;
        }

        body.dyslexic-font, body.dyslexic-font * {
            font-family: 'OpenDyslexic', Arial, sans-serif !important;
            letter-spacing: 0.05em;
            line-height: 1.7;
        }

        .accessibility-icon:hover {
            transform: scale(1.1);
            transition: transform 0.3s ease;
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
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow dark:bg-gray-800 dark:shadow-md">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="py-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <div id="accessibility-panel" class="fixed top-0 right-0 h-full w-64 bg-gray-900 shadow-lg p-4 access-panel-hidden z-50">
        <h2 class="text-lg font-semibold text-white mb-4">Accessibilité</h2>
        <div class="space-y-2">
            <button onclick="setFontSize('small')" class="access-control-btn">Texte petit</button>
            <button onclick="setFontSize('normal')" class="access-control-btn">Texte normal</button>
            <button onclick="setFontSize('large')" class="access-control-btn">Texte grand</button>
            <button onclick="toggleContrast()" class="access-control-btn">Contraste élevé</button>
            <button onclick="toggleDyslexicFont()" class="access-control-btn">Police dyslexique</button>
            <button onclick="toggleReading()" class="access-control-btn">
                <span id="readLabel">Lire à voix haute</span>
                <i id="readingIcon" class="fas fa-play ml-2"></i>
            </button>
            <button onclick="resetAccessibility()" class="access-control-btn">Restaurer les réglages</button>
        </div>
    </div>

    <button onclick="toggleAccessibilityPanel()" class="fixed bottom-4 right-4 z-50 p-0 bg-transparent border-none accessibility-icon" aria-label="Accessibilité">
        <img src="{{ asset('acesibilite.webp') }}" alt="Accessibilité" class="w-12 h-12 object-contain">
    </button>

    <script>
        let isReading = false;
        let utterance;
        let paused = false;

        function toggleAccessibilityPanel() {
            const panel = document.getElementById('accessibility-panel');
            panel.classList.toggle('access-panel-hidden');
            panel.classList.toggle('access-panel-visible');
        }

        function setFontSize(size) {
            let html = document.querySelector('html');
            switch(size) {
                case 'small': html.style.fontSize = '14px'; break;
                case 'normal': html.style.fontSize = '16px'; break;
                case 'large': html.style.fontSize = '18px'; break;
            }
            localStorage.setItem('fontSize', size);
        }

        function toggleContrast() {
            document.body.classList.toggle('contrast-high');
            const active = document.body.classList.contains('contrast-high');
            localStorage.setItem('contrast', active ? 'on' : 'off');
        }

        function toggleDyslexicFont() {
            const isActive = document.body.classList.toggle('dyslexic-font');
            localStorage.setItem('dyslexic', isActive ? 'on' : 'off');
        }

        function toggleReading() {
            const icon = document.getElementById('readingIcon');
            const label = document.getElementById('readLabel');
            if (!isReading && !paused) {
                utterance = new SpeechSynthesisUtterance(document.body.innerText);
                utterance.lang = 'fr-FR';
                utterance.onend = () => {
                    isReading = false;
                    paused = false;
                    icon.classList.remove('fa-pause');
                    icon.classList.add('fa-play');
                    label.innerText = 'Lire à voix haute';
                };
                speechSynthesis.speak(utterance);
                isReading = true;
                paused = false;
                icon.classList.remove('fa-play');
                icon.classList.add('fa-pause');
                label.innerText = 'Pause de lecture';
            } else if (isReading) {
                speechSynthesis.pause();
                isReading = false;
                paused = true;
                icon.classList.remove('fa-pause');
                icon.classList.add('fa-play');
                label.innerText = 'Continuer la lecture';
            } else if (paused) {
                speechSynthesis.resume();
                isReading = true;
                paused = false;
                icon.classList.remove('fa-play');
                icon.classList.add('fa-pause');
                label.innerText = 'Pause de lecture';
            }
        }

        function resetAccessibility() {
            document.querySelector('html').style.fontSize = '16px';
            document.body.classList.remove('contrast-high');
            document.body.classList.remove('dyslexic-font');
            localStorage.removeItem('fontSize');
            localStorage.removeItem('contrast');
            localStorage.removeItem('dyslexic');
            speechSynthesis.cancel();
            isReading = false;
            paused = false;
            document.getElementById('readingIcon').classList.remove('fa-pause');
            document.getElementById('readingIcon').classList.add('fa-play');
            document.getElementById('readLabel').innerText = 'Lire à voix haute';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedFont = localStorage.getItem('fontSize');
            if (savedFont) setFontSize(savedFont);

            if (localStorage.getItem('contrast') === 'on') {
                document.body.classList.add('contrast-high');
            }

            if (localStorage.getItem('dyslexic') === 'on') {
                document.body.classList.add('dyslexic-font');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.altKey && e.key === 'a') {
                e.preventDefault();
                toggleAccessibilityPanel();
            }
        });
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
