<x-guest-layout>
    @php
        $errName   = $errors->has('name');
        $errEmail  = $errors->has('email');
        $errPass   = $errors->has('password');
        $errPass2  = $errors->has('password_confirmation');
    @endphp

    <form method="POST" action="{{ route('register') }}" aria-describedby="register-help">
        @csrf

        <p id="register-help" class="sr-only">
            {{ __('Tous les champs sont requis pour créer votre compte.') }}
        </p>

        <div>
            <x-input-label for="name" :value="__('Nom complet')" />
            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                aria-required="true"
                aria-invalid="{{ $errName ? 'true' : 'false' }}"
                aria-describedby="name-help {{ $errName ? 'name-error' : '' }}"
            />
            <p id="name-help" class="mt-1 text-xs text-gray-500">
                {{ __('Entrez vos prénom et nom.') }}
            </p>
            @if($errName)
                <div id="name-error" class="mt-2" role="alert" aria-live="assertive">
                    <x-input-error :messages="$errors->get('name')" />
                </div>
            @else
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            @endif
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Adresse e-mail')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="email"
                inputmode="email"
                spellcheck="false"
                aria-required="true"
                aria-invalid="{{ $errEmail ? 'true' : 'false' }}"
                aria-describedby="email-help {{ $errEmail ? 'email-error' : '' }}"
            />
            <p id="email-help" class="mt-1 text-xs text-gray-500">
                {{ __('Utilisez votre adresse e-mail universitaire.') }}
            </p>
            @if($errEmail)
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
                autocomplete="new-password"
                aria-required="true"
                aria-invalid="{{ $errPass ? 'true' : 'false' }}"
                aria-describedby="password-help {{ $errPass ? 'password-error' : '' }}"
            />
            <p id="password-help" class="mt-1 text-xs text-gray-500">
                {{ __('Choisissez un mot de passe robuste.') }}
            </p>
            @if($errPass)
                <div id="password-error" class="mt-2" role="alert" aria-live="assertive">
                    <x-input-error :messages="$errors->get('password')" />
                </div>
            @else
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            @endif
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                aria-required="true"
                aria-invalid="{{ $errPass2 ? 'true' : 'false' }}"
                aria-describedby="password2-help {{ $errPass2 ? 'password2-error' : '' }}"
            />
            <p id="password2-help" class="mt-1 text-xs text-gray-500">
                {{ __('Répétez exactement le mot de passe saisi ci-dessus.') }}
            </p>
            @if($errPass2)
                <div id="password2-error" class="mt-2" role="alert" aria-live="assertive">
                    <x-input-error :messages="$errors->get('password_confirmation')" />
                </div>
            @else
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            @endif
        </div>

        <div class="flex items-center justify-end mt-4">
            <a
                class="underline text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}"
            >
                {{ __('Déjà inscrit ?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Créer un compte') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
