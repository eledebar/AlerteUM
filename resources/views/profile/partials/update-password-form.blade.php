<section x-data="{ showOld:false, showNew:false, showConfirm:false }" aria-labelledby="pwd-title">
    <header class="mb-5">
        <h2 id="pwd-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Modifier le mot de passe') }}
        </h2>
        <p id="pwd-help" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Utilisez un mot de passe long et unique.') }}
        </p>
    </header>

    @php
        $bag = $errors->updatePassword ?? null;
        $errCur = $bag?->get('current_password') ?? [];
        $errNew = $bag?->get('password') ?? [];
        $errConf = $bag?->get('password_confirmation') ?? [];
        $idCurErr = 'err-current_password';
        $idNewErr = 'err-password';
        $idConfErr = 'err-password_confirmation';
    @endphp

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" :value="__('Mot de passe actuel')" />
            <div class="relative">
                <x-text-input
                    id="current_password"
                    name="current_password"
                    :type="showOld ? 'text' : 'password'"
                    class="mt-1 block w-full pr-12"
                    autocomplete="current-password"
                    required
                    aria-required="true"
                    aria-describedby="pwd-help{{ $errCur ? ' '.$idCurErr : '' }}"
                    aria-invalid="{{ $errCur ? 'true' : 'false' }}"
                />
                <button
                    type="button"
                    @click="showOld=!showOld"
                    :aria-pressed="showOld ? 'true' : 'false'"
                    class="absolute inset-y-0 right-0 my-1 mr-1 px-3 rounded-md text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                    aria-label="{{ __('Afficher ou masquer le mot de passe actuel') }}"
                    title="{{ __('Afficher/Masquer') }}"
                >
                    <span x-show="!showOld">{{ __('Afficher') }}</span>
                    <span x-show="showOld">{{ __('Masquer') }}</span>
                </button>
            </div>
            <x-input-error :messages="$errCur" id="{{ $idCurErr }}" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Nouveau mot de passe')" />
            <div class="relative">
                <x-text-input
                    id="password"
                    name="password"
                    :type="showNew ? 'text' : 'password'"
                    class="mt-1 block w-full pr-12"
                    autocomplete="new-password"
                    required
                    aria-required="true"
                    aria-describedby="pwd-help{{ $errNew ? ' '.$idNewErr : '' }}"
                    aria-invalid="{{ $errNew ? 'true' : 'false' }}"
                    minlength="8"
                />
                <button
                    type="button"
                    @click="showNew=!showNew"
                    :aria-pressed="showNew ? 'true' : 'false'"
                    class="absolute inset-y-0 right-0 my-1 mr-1 px-3 rounded-md text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                    aria-label="{{ __('Afficher ou masquer le nouveau mot de passe') }}"
                    title="{{ __('Afficher/Masquer') }}"
                >
                    <span x-show="!showNew">{{ __('Afficher') }}</span>
                    <span x-show="showNew">{{ __('Masquer') }}</span>
                </button>
            </div>
            <x-input-error :messages="$errNew" id="{{ $idNewErr }}" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
            <div class="relative">
                <x-text-input
                    id="password_confirmation"
                    name="password_confirmation"
                    :type="showConfirm ? 'text' : 'password'"
                    class="mt-1 block w-full pr-12"
                    autocomplete="new-password"
                    required
                    aria-required="true"
                    aria-describedby="pwd-help{{ $errConf ? ' '.$idConfErr : '' }}"
                    aria-invalid="{{ $errConf ? 'true' : 'false' }}"
                    minlength="8"
                />
                <button
                    type="button"
                    @click="showConfirm=!showConfirm"
                    :aria-pressed="showConfirm ? 'true' : 'false'"
                    class="absolute inset-y-0 right-0 my-1 mr-1 px-3 rounded-md text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                    aria-label="{{ __('Afficher ou masquer la confirmation du mot de passe') }}"
                    title="{{ __('Afficher/Masquer') }}"
                >
                    <span x-show="!showConfirm">{{ __('Afficher') }}</span>
                    <span x-show="showConfirm">{{ __('Masquer') }}</span>
                </button>
            </div>
            <x-input-error :messages="$errConf" id="{{ $idConfErr }}" class="mt-2" />
        </div>

        <x-primary-button aria-describedby="pwd-help">{{ __('Enregistrer') }}</x-primary-button>
    </form>
</section>
