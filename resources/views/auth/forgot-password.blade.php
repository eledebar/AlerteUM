<x-guest-layout>
    <div id="reset-desc" class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <span class="sr-only" role="status" aria-live="polite">{{ session('status') }}</span>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" aria-describedby="reset-desc">
        @csrf

        @php $hasErr = $errors->has('email'); @endphp
        <div>
            <x-input-label for="email" :value="__('Email')" />
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
                aria-invalid="{{ $hasErr ? 'true' : 'false' }}"
                aria-describedby="email-help {{ $hasErr ? 'email-error' : '' }}"
            />
            <p id="email-help" class="mt-1 text-xs text-gray-500">
                {{ __('Enter the email associated with your account.') }}
            </p>

            @if($hasErr)
                <div id="email-error" class="mt-2" role="alert" aria-live="assertive">
                    <x-input-error :messages="$errors->get('email')" />
                </div>
            @else
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            @endif
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
