<x-guest-layout>
    @php
        $errEmail = $errors->has('email');
        $errPass  = $errors->has('password');
        $errPass2 = $errors->has('password_confirmation');
    @endphp

    <form method="POST" action="{{ route('password.store') }}" aria-describedby="reset-help">
        @csrf

        <p id="reset-help" class="sr-only">
            {{ __('Indiquez votre e-mail et définissez un nouveau mot de passe pour votre compte.') }}
        </p>

        <input type="hidden" name="token" value="{{ $request- aria-label="token">route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
                inputmode="email"
                spellcheck="false"
                aria-required="true"
                aria-invalid="{{ $errEmail ? 'true' : 'false' }}"
                aria-describedby="email-help {{ $errEmail ? 'email-error' : '' }}"
            />
            <p id="email-help" class="mt-1 text-xs text-gray-500">
                {{ __('Utilisez l’adresse e-mail associée à votre compte.') }}
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
            <x-input-label for="password" :value="__('Password')" />
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
                {{ __('Choisissez un mot de passe robuste (8+ caractères, mélange de lettres, chiffres et symboles).') }}
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
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
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
                {{ __('Répétez exactement le nouveau mot de passe.') }}
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
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
