<x-guest-layout>
    <div class="mb-4 text-sm text-gray-700 dark:text-gray-300" role="status" aria-live="polite" id="verify-help">
        {{ __('Merci pour votre inscription ! Avant de commencer, veuillez vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer. Si vous n’avez pas reçu l’e-mail, nous pouvons vous en renvoyer un.') }}
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-700 dark:text-green-300" role="alert" aria-live="assertive" id="status-alert">
            {{ __('Un nouveau lien de vérification a été envoyé à l’adresse e-mail fournie lors de l’inscription.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}" aria-describedby="verify-help">
            @csrf
            <x-primary-button aria-label="{{ __('Renvoyer l’e-mail de vérification') }}">
                {{ __('Renvoyer l’e-mail de vérification') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white rounded-md focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                aria-label="{{ __('Se déconnecter') }}"
            >
                {{ __('Se déconnecter') }}
            </button>
        </form>
    </div>
</x-guest-layout>
