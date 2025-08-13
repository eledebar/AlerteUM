<nav x-data="{ open: false }" class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('logo-um.webp') }}" alt="Logo" class="h-9 w-auto object-contain" style="max-height: 36px;">
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">AlerteUM</span>
                </a>
            </div>

            <div class="hidden sm:flex space-x-8">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Tableau de bord') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.incidents.index')" :active="request()->routeIs('admin.incidents.*')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Incidents') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.resolveurs.index')" :active="request()->routeIs('admin.resolveurs.*')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Résolveurs') }}
                        </x-nav-link>

                    @elseif(auth()->user()->estResolveur())
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Tableau de bord') }}
                        </x-nav-link>
                        <x-nav-link :href="route('resolveur.incidents.index')" :active="request()->routeIs('resolveur.incidents.*')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Incidents') }}
                        </x-nav-link>

                    @elseif(auth()->user()->estUtilisateur())
                        <x-nav-link :href="route('utilisateur.home')" :active="request()->routeIs('utilisateur.home')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Accueil') }}
                        </x-nav-link>
                        <x-nav-link :href="route('utilisateur.incidents.categories')" :active="request()->routeIs('utilisateur.incidents.categories') || request()->routeIs('utilisateur.incidents.create')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Catalogue des incidents') }}
                        </x-nav-link>
                        <x-nav-link :href="route('utilisateur.incidents.index')" :active="request()->routeIs('utilisateur.incidents.index')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Mes incidents') }}
                        </x-nav-link>
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ __('Tableau de bord') }}
                        </x-nav-link>
                    @endif
                @endauth
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    {{-- Ocultamos campana en admin si no hay notificaciones para admin --}}
                    @if(auth()->user()->role !== 'admin')
                        @php $prefix = auth()->user()->estResolveur() ? 'resolveur.' : 'utilisateur.'; @endphp
                        <a href="{{ route($prefix . 'notifications.index') }}" class="relative text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400" aria-label="Notifications">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if(auth()->user()->unreadNotifications()->count() > 0)
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-600"></span>
                            @endif
                        </a>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center font-medium text-sm text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                                {{ Auth::user()->name }}
                                <svg class="ml-1 h-4 w-4 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M5.293 7.293L10 12l4.707-4.707-1.414-1.414L10 9.172 6.707 5.879 5.293 7.293z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Se déconnecter') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>

                    {{-- Botón tema --}}
                    <div x-data="{ darkMode: document.documentElement.classList.contains('dark') }">
                        <button
                            x-on:click="
                                darkMode = !darkMode;
                                if (darkMode) { document.documentElement.classList.add('dark'); localStorage.theme = 'dark'; }
                                else { document.documentElement.classList.remove('dark'); localStorage.theme = 'light'; }
                            "
                            class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600"
                            aria-label="Cambiar tema"
                            title="Cambiar tema"
                        >
                            <template x-if="!darkMode">
                                <img src="{{ asset('sun.webp') }}" alt="Modo claro" class="w-5 h-5" />
                            </template>
                            <template x-if="darkMode">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
                                </svg>
                            </template>
                        </button>
                    </div>
                @endauth

                <div class="sm:hidden flex items-center">
                    <button @click="open = !open" class="text-gray-700 dark:text-white focus:outline-none" aria-label="Abrir menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menú móvil --}}
        <div class="sm:hidden mt-2 px-4" x-show="open" x-cloak>
            <div class="flex flex-col space-y-2">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('dashboard')" class="block">{{ __('Tableau de bord') }}</x-nav-link>
                        <x-nav-link :href="route('admin.incidents.index')" class="block">{{ __('Incidents') }}</x-nav-link>
                        <x-nav-link :href="route('admin.resolveurs.index')" class="block">{{ __('Résolveurs') }}</x-nav-link>
                    @elseif(auth()->user()->estResolveur())
                        <x-nav-link :href="route('dashboard')" class="block">{{ __('Tableau de bord') }}</x-nav-link>
                        <x-nav-link :href="route('resolveur.incidents.index')" class="block">{{ __('Incidents') }}</x-nav-link>
                    @elseif(auth()->user()->estUtilisateur())
                        <x-nav-link :href="route('utilisateur.home')" class="block">{{ __('Accueil') }}</x-nav-link>
                        <x-nav-link :href="route('utilisateur.incidents.categories')" class="block">{{ __('Catalogue des incidents') }}</x-nav-link>
                        <x-nav-link :href="route('utilisateur.incidents.index')" class="block">{{ __('Mes incidents') }}</x-nav-link>
                        <x-nav-link :href="route('dashboard')" class="block">{{ __('Tableau de bord') }}</x-nav-link>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>
