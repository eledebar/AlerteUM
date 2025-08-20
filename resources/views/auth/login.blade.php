<x-guest-layout>
    <span class="sr-only" role="status" aria-live="polite">{{ session('status') }}</span>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @php
        $emailErr = $errors->has('email');
        $pwdErr   = $errors->has('password');
    @endphp

    <form method="POST" action="{{ route('login') }}" aria-describedby="login-help">
        @csrf

        <p id="login-help" class="sr-only">
            {{ __('Tous les champs sont obligatoires pour se connecter.') }}
        </p>

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
                inputmode="email"
                spellcheck="false"
                aria-required="true"
                aria-invalid="{{ $emailErr ? 'true' : 'false' }}"
                aria-describedby="email-help {{ $emailErr ? 'email-error' : '' }}"
            />
            <p id="email-help" class="mt-1 text-xs text-gray-500">
                {{ __('Entrez votre adresse e-mail universitaire.') }}
            </p>

            @if($emailErr)
                <div id="email-error" class="mt-2" role="alert" aria-live="assertive">
                    <x-input-error :messages="$errors->get('email')" />
                </div>
            @else
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            @endif
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                aria-required="true"
                aria-invalid="{{ $pwdErr ? 'true' : 'false' }}"
                aria-describedby="password-help {{ $pwdErr ? 'password-error' : '' }}"
            />
            <p id="password-help" class="mt-1 text-xs text-gray-500">
                {{ __('Saisissez votre mot de passe.') }}
            </p>

            @if($pwdErr)
                <div id="password-error" class="mt-2" role="alert" aria-live="assertive">
                    <x-input-error :messages="$errors->get('password')" />
                </div>
            @else
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            @endif
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember"
                    aria-describedby="remember-help"
                >
                <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
            </label>
            <p id="remember-help" class="sr-only">
                {{ __('Rester connecté sur cet appareil.') }}
            </p>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Mot de passe oublié ?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Connexion') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
