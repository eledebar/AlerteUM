<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('logo-um.png') }}" alt="Logo" class="h-9 w-auto object-contain" style="max-height: 36px;">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                        @if(auth()->user()->estAdmin())
                            <x-nav-link :href="route('admin.incidents.index')" :active="request()->routeIs('admin.incidents.*')">
                                {{ __('Incidents') }}
                            </x-nav-link>
                        @elseif(auth()->user()->estUtilisateur())
                            <x-nav-link :href="route('utilisateur.incidents.index')" :active="request()->routeIs('utilisateur.incidents.*')">
                                {{ __('Mes Incidents') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings + Notifications -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    @php
                        $prefix = auth()->user()->estAdmin() ? 'admin.' : 'utilisateur.';
                    @endphp

                    <div class="mr-4">
                        <a href="{{ route($prefix . 'notifications.index') }}" class="relative inline-block text-gray-600 hover:text-gray-900">
                            🔔
                            @if(auth()->user()->unreadNotifications()->count() > 0)
                                <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"></span>
                            @endif
                        </a>
                    </div>
                @endauth

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center font-medium text-sm text-gray-500 hover:text-gray-700">
                            {{ Auth::user()->name }}
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path d="M5.293 7.293L10 12l4.707-4.707-1.414-1.414L10 9.172 6.707 5.879 5.293 7.293z" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>

{{-- <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"> --}}
