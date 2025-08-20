<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-cloak>
<script src="https://kit.fontawesome.com/3e9f0f6842.js" crossorigin="anonymous"></script>

<head>
    <script>
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        const savedTheme = localStorage.theme
        const shouldUseDark = savedTheme === 'dark' || (!savedTheme && prefersDark)
        if (shouldUseDark) { document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') }
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
        [x-cloak]{display:none!important}
        html{background-color:#111827;font-size:16px;scroll-behavior:smooth}
        body{margin:0;padding:0}
        .skip-link{position:absolute;left:0;top:-40px;background:#111827;color:#fff;padding:.5rem .75rem;z-index:100;border-radius:.375rem}
        .skip-link:focus{top:.5rem;left:.5rem;outline:2px solid #3b82f6}
        @media (prefers-color-scheme: dark){html,body{background-color:#111827;color:#fff}}
        #accessibility-panel{transition:transform .3s ease,opacity .2s ease}
        .access-panel-hidden{transform:translateX(100%);visibility:hidden;opacity:0;pointer-events:none}
        .access-panel-visible{transform:translateX(0);visibility:visible;opacity:1;pointer-events:auto}
        .access-control-btn{display:flex;justify-content:space-between;align-items:center;width:100%;padding:.5rem 1rem;background-color:#1f2937;color:#fff;border-radius:.375rem;cursor:pointer;font-size:.95rem}
        .access-control-btn:hover{background-color:#374151}
        .access-control-btn:focus{outline:2px solid #3b82f6;outline-offset:2px}
        body.contrast-high, body.contrast-high *{background-color:#000!important;color:#fff!important;border-color:#fff!important}
        body.dyslexic-font, body.dyslexic-font *{font-family:'OpenDyslexic',Arial,sans-serif!important;letter-spacing:.05em;line-height:1.7}
        .accessibility-icon:hover{transform:scale(1.1);transition:transform .3s ease}
        @media (max-width:768px){main{padding-left:1rem;padding-right:1rem}header>div{padding-left:1rem!important;padding-right:1rem!important}.fixed.bottom-4.right-4{bottom:1rem;right:1rem}}
        @media (prefers-reduced-motion: reduce){#accessibility-panel,.accessibility-icon:hover{transition:none!important;transform:none!important}}
    </style>
</head>

<body x-init="document.body.removeAttribute('x-cloak')" x-cloak class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <a href="#main-content" class="skip-link">Aller au contenu</a>

    <div id="app-root" x-show="true" x-cloak>
        <div class="min-h-screen">
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white shadow dark:bg-gray-800 dark:shadow-md">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @elseif (View::hasSection('header'))
                <header class="bg-white shadow dark:bg-gray-800 dark:shadow-md">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <main id="main-content" class="py-6" tabindex="-1">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
        </div>
    </div>

    <div id="accessibility-panel" class="fixed top-0 right-0 h-full w-64 bg-gray-900 shadow-lg p-4 access-panel-hidden z-50" role="dialog" aria-modal="true" aria-labelledby="access-title" aria-hidden="true" tabindex="-1">
        <div class="flex items-center justify-between mb-3">
            <h2 id="access-title" class="text-lg font-semibold text-white">Accessibilité</h2>
            <button type="button" id="access-close" class="access-control-btn w-auto px-3 py-1 text-sm" aria-label="Fermer le panneau">Fermer</button>
        </div>
        <div class="space-y-2" role="group" aria-labelledby="access-title">
            <div class="space-y-2" role="group" aria-label="Taille du texte">
                <button id="fs-small" onclick="setFontSize('small')" class="access-control-btn" aria-pressed="false">Texte petit</button>
                <button id="fs-normal" onclick="setFontSize('normal')" class="access-control-btn" aria-pressed="true">Texte normal</button>
                <button id="fs-large" onclick="setFontSize('large')" class="access-control-btn" aria-pressed="false">Texte grand</button>
            </div>
            <button id="btn-contrast" onclick="toggleContrast()" class="access-control-btn" aria-pressed="false">
                <span id="contrastLabel">Contraste élevé</span>
                <i id="contrastIcon" class="fas fa-eye ml-2" aria-hidden="true"></i>
            </button>
            <button id="btn-dys" onclick="toggleDyslexicFont()" class="access-control-btn" aria-pressed="false">
                <span id="dyslexicLabel">Police dyslexique</span>
                <i id="dyslexicIcon" class="fas fa-font ml-2" aria-hidden="true"></i>
            </button>
            <button id="btn-read" onclick="toggleReading()" class="access-control-btn" aria-pressed="false">
                <span id="readLabel">Lire à voix haute</span>
                <i id="readingIcon" class="fas fa-play ml-2" aria-hidden="true"></i>
            </button>
            <button onclick="resetAccessibility()" class="access-control-btn">Restaurer les réglages</button>
        </div>
    </div>

    <button id="a11yToggle" onclick="toggleAccessibilityPanel()" class="fixed bottom-4 right-4 z-50 p-0 bg-transparent border-none accessibility-icon" aria-label="Ouvrir le panneau d’accessibilité" aria-controls="accessibility-panel" aria-expanded="false">
        <img src="{{ asset('acesibilite.webp') }}" alt="Accessibilité" class="w-12 h-12 object-contain">
    </button>

    <span id="a11y-live" class="sr-only" aria-live="polite" aria-atomic="true"></span>

    <script>
        let isReading=false
        let utterance
        let paused=false
        let lastFocusedEl=null

        function setFontPressed(size){
            const ids={small:'fs-small',normal:'fs-normal',large:'fs-large'}
            Object.values(ids).forEach(id=>{const b=document.getElementById(id);if(b)b.setAttribute('aria-pressed','false')})
            const active=document.getElementById(ids[size]);if(active)active.setAttribute('aria-pressed','true')
        }

        function toggleAccessibilityPanel(){
            const panel=document.getElementById('accessibility-panel')
            const appRoot=document.getElementById('app-root')
            const toggleBtn=document.getElementById('a11yToggle')
            const closeBtn=document.getElementById('access-close')
            const isHidden=panel.classList.contains('access-panel-hidden')
            if(isHidden){
                lastFocusedEl=document.activeElement
                panel.classList.remove('access-panel-hidden')
                panel.classList.add('access-panel-visible')
                panel.removeAttribute('aria-hidden')
                appRoot.setAttribute('inert','')
                toggleBtn.setAttribute('aria-expanded','true')
                panel.focus()
            }else{
                panel.classList.add('access-panel-hidden')
                panel.classList.remove('access-panel-visible')
                panel.setAttribute('aria-hidden','true')
                appRoot.removeAttribute('inert')
                toggleBtn.setAttribute('aria-expanded','false')
                if(lastFocusedEl)lastFocusedEl.focus()
            }
            function trap(e){
                if(panel.getAttribute('aria-hidden')==='true')return
                if(e.key==='Escape'){e.preventDefault();toggleAccessibilityPanel()}
                if(e.key==='Tab'){
                    const focusables=panel.querySelectorAll('button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])')
                    const list=Array.prototype.slice.call(focusables).filter(el=>!el.hasAttribute('disabled'))
                    if(!list.length)return
                    const first=list[0];const last=list[list.length-1]
                    if(e.shiftKey && document.activeElement===first){e.preventDefault();last.focus()}
                    else if(!e.shiftKey && document.activeElement===last){e.preventDefault();first.focus()}
                }
            }
            if(!panel._boundTrap){document.addEventListener('keydown',trap);panel._boundTrap=true}
            if(closeBtn && !closeBtn._boundClose){closeBtn.addEventListener('click',()=>toggleAccessibilityPanel());closeBtn._boundClose=true}
        }

        function setFontSize(size){
            let html=document.querySelector('html')
            if(size==='small')html.style.fontSize='14px'
            if(size==='normal')html.style.fontSize='16px'
            if(size==='large')html.style.fontSize='18px'
            localStorage.setItem('fontSize',size)
            setFontPressed(size)
            const live=document.getElementById('a11y-live')
            if(live)live.textContent='Taille du texte réglée sur '+(size==='small'?'petit':size==='large'?'grand':'normal')
        }

        function toggleContrast(){
            document.body.classList.toggle('contrast-high')
            const active=document.body.classList.contains('contrast-high')
            localStorage.setItem('contrast',active?'on':'off')
            const label=document.getElementById('contrastLabel')
            const icon=document.getElementById('contrastIcon')
            const btn=document.getElementById('btn-contrast')
            if(label)label.innerText=active?'Désactiver contraste':'Contraste élevé'
            if(icon)icon.className=active?'fas fa-eye-slash ml-2':'fas fa-eye ml-2'
            if(btn)btn.setAttribute('aria-pressed',active?'true':'false')
            const live=document.getElementById('a11y-live')
            if(live)live.textContent=active?'Contraste élevé activé':'Contraste élevé désactivé'
        }

        function toggleDyslexicFont(){
            const isActive=document.body.classList.toggle('dyslexic-font')
            localStorage.setItem('dyslexic',isActive?'on':'off')
            const label=document.getElementById('dyslexicLabel')
            const icon=document.getElementById('dyslexicIcon')
            const btn=document.getElementById('btn-dys')
            if(label)label.innerText=isActive?'Désactiver police':'Police dyslexique'
            if(icon)icon.className=isActive?'fas fa-font ml-2 text-red-400':'fas fa-font ml-2'
            if(btn)btn.setAttribute('aria-pressed',isActive?'true':'false')
            const live=document.getElementById('a11y-live')
            if(live)live.textContent=isActive?'Police OpenDyslexic activée':'Police OpenDyslexic désactivée'
        }

        function toggleReading(){
            const icon=document.getElementById('readingIcon')
            const label=document.getElementById('readLabel')
            const btn=document.getElementById('btn-read')
            const lang=document.documentElement.lang||'fr-FR'
            const main=document.getElementById('main-content')
            if(!isReading && !paused){
                const text=main?main.innerText:document.body.innerText
                utterance=new SpeechSynthesisUtterance(text)
                utterance.lang=lang
                utterance.onend=()=>{isReading=false;paused=false;icon.classList.remove('fa-pause');icon.classList.add('fa-play');label.innerText='Lire à voix haute';if(btn)btn.setAttribute('aria-pressed','false')}
                speechSynthesis.speak(utterance)
                isReading=true;paused=false
                icon.classList.remove('fa-play');icon.classList.add('fa-pause');label.innerText='Pause de lecture'
                if(btn)btn.setAttribute('aria-pressed','true')
            }else if(isReading){
                speechSynthesis.pause();isReading=false;paused=true
                icon.classList.remove('fa-pause');icon.classList.add('fa-play');label.innerText='Continuer la lecture'
                if(btn)btn.setAttribute('aria-pressed','false')
            }else if(paused){
                speechSynthesis.resume();isReading=true;paused=false
                icon.classList.remove('fa-play');icon.classList.add('fa-pause');label.innerText='Pause de lecture'
                if(btn)btn.setAttribute('aria-pressed','true')
            }
        }

        function resetAccessibility(){
            document.querySelector('html').style.fontSize='16px'
            document.body.classList.remove('contrast-high')
            document.body.classList.remove('dyslexic-font')
            localStorage.removeItem('fontSize')
            localStorage.removeItem('contrast')
            localStorage.removeItem('dyslexic')
            speechSynthesis.cancel()
            isReading=false;paused=false
            document.getElementById('readingIcon').classList.remove('fa-pause')
            document.getElementById('readingIcon').classList.add('fa-play')
            document.getElementById('readLabel').innerText='Lire à voix haute'
            setFontPressed('normal')
            const live=document.getElementById('a11y-live');if(live)live.textContent='Réglages d’accessibilité restaurés'
            const btnRead=document.getElementById('btn-read');if(btnRead)btnRead.setAttribute('aria-pressed','false')
            const btnDys=document.getElementById('btn-dys');if(btnDys)btnDys.setAttribute('aria-pressed','false')
            const btnContrast=document.getElementById('btn-contrast');if(btnContrast)btnContrast.setAttribute('aria-pressed','false')
        }

        document.addEventListener('DOMContentLoaded',()=>{
            const savedFont=localStorage.getItem('fontSize');if(savedFont)setFontSize(savedFont);else setFontPressed('normal')
            if(localStorage.getItem('contrast')==='on'){
                document.body.classList.add('contrast-high')
                const btn=document.getElementById('btn-contrast');if(btn)btn.setAttribute('aria-pressed','true')
                const label=document.getElementById('contrastLabel');const icon=document.getElementById('contrastIcon')
                if(label)label.innerText='Désactiver contraste';if(icon)icon.className='fas fa-eye-slash ml-2'
            }
            if(localStorage.getItem('dyslexic')==='on'){
                document.body.classList.add('dyslexic-font')
                const btn=document.getElementById('btn-dys');if(btn)btn.setAttribute('aria-pressed','true')
                const label=document.getElementById('dyslexicLabel');const icon=document.getElementById('dyslexicIcon')
                if(label)label.innerText='Désactiver police';if(icon)icon.className='fas fa-font ml-2 text-red-400'
            }
        })

        document.addEventListener('keydown',(e)=>{ if(e.altKey && e.key==='a'){ e.preventDefault(); toggleAccessibilityPanel() } })
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
