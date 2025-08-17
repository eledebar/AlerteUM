<section>
    <header class="mb-5">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Modifier le mot de passe') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Utilisez un mot de passe long et unique.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" :value="__('Mot de passe actuel')" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password') ?? []" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Nouveau mot de passe')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password') ?? []" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation') ?? []" class="mt-2" />
        </div>

        <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
    </form>
</section>
