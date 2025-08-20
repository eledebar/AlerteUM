<x-guest-layout>
    @php $hasErr = $errors->has('password'); @endphp

    <div id="confirm-desc" class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" aria-describedby="confirm-desc">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <x-text-input
                    id="password"
                    class="block w-full pr-12"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    spellcheck="false"
                    aria-required="true"
                    aria-describedby="password-help {{ $hasErr ? 'password-error' : '' }}"
                    aria-invalid="{{ $hasErr ? 'true' : 'false' }}"
                />

                <button
                    type="button"
                    id="toggle-password"
                    class="absolute inset-y-0 right-0 px-3 text-sm text-gray-600 hover:text-gray-800 focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-indigo-500 rounded-md"
                    aria-controls="password"
                    aria-pressed="false"
                >
                    {{ __('Show') }}
                </button>
            </div>

            <p id="password-help" class="mt-1 text-xs text-gray-500">
                {{ __('Enter your current account password.') }}
            </p>

            @if($hasErr)
                <div id="password-error" class="mt-2" role="alert" aria-live="assertive">
                    <x-input-error :messages="$errors->get('password')" />
                </div>
            @else
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            @endif
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button aria-label="{{ __('Confirm password') }}">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        (function () {
            const btn = document.getElementById('toggle-password');
            const input = document.getElementById('password');
            if (!btn || !input) return;
            btn.addEventListener('click', () => {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                btn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                btn.textContent = isPassword ? '{{ __('Hide') }}' : '{{ __('Show') }}';
            });
        })();
    </script>
</x-guest-layout>
