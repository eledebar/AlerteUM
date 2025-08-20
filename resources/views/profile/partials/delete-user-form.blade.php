<section x-data="{ open:false, last:null }" aria-labelledby="danger-title">
    <header class="mb-5">
        <h2 id="danger-title" class="text-lg font-semibold text-red-600">
            {{ __('Zone dangereuse') }}
        </h2>
        <p id="danger-desc" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Supprimer définitivement votre compte et toutes les données associées.') }}
        </p>
    </header>

    <button
        type="button"
        @click="last=$event.currentTarget; open=true; $nextTick(()=>{$refs.delpass.focus()})"
        :aria-expanded="open ? 'true' : 'false'"
        aria-controls="danger-panel"
        class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
    >
        {{ __('Supprimer le compte') }}
    </button>

    <div
        x-cloak
        x-show="open"
        id="danger-panel"
        class="mt-4 space-y-4"
        role="region"
        aria-describedby="danger-desc"
        @keydown.escape.window="open=false; $nextTick(()=>{ if(last) last.focus() })"
    >
        <div class="rounded-md border border-red-300 bg-red-50 text-red-800 px-3 py-2 text-sm" aria-live="polite">
            {{ __('Action irréversible. Cette suppression ne peut pas être annulée.') }}
        </div>

        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <x-input-label for="password" :value="__('Mot de passe')" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                    x-ref="delpass"
                    aria-describedby="danger-desc"
                    required
                />
                <x-input-error :messages="$errors->userDeletion->get('password') ?? []" class="mt-2" />
            </div>

            <div class="flex items-center gap-2">
                <input id="confirm-del" type="checkbox" class="h-4 w-4 rounded border-gray-300 dark:border-gray-700" required>
                <label for="confirm-del" class="text-sm text-gray-700 dark:text-gray-200">{{ __('Je comprends que cette action est définitive.') }}</label>
            </div>

            <div class="flex items-center gap-3">
                <x-danger-button type="submit" aria-describedby="danger-desc">{{ __('Supprimer définitivement') }}</x-danger-button>
                <button
                    type="button"
                    @click="open=false; $nextTick(()=>{ if(last) last.focus() })"
                    class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-gray-400"
                >
                    {{ __('Annuler') }}
                </button>
            </div>
        </form>
    </div>
</section>
