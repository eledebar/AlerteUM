<section x-data="{ open:false }">
    <header class="mb-5">
        <h2 class="text-lg font-semibold text-red-600">
            {{ __('Zone dangereuse') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Supprimer définitivement votre compte et toutes les données associées.') }}
        </p>
    </header>

    <button @click="open=!open" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
        {{ __('Supprimer le compte') }}
    </button>

    <div x-cloak x-show="open" class="mt-4 space-y-4">
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <x-input-label for="password" :value="__('Mot de passe')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->userDeletion->get('password') ?? []" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <x-danger-button>{{ __('Supprimer définitivement') }}</x-danger-button>
                <button type="button" @click="open=false" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200">
                    {{ __('Annuler') }}
                </button>
            </div>
        </form>
    </div>
</section>
