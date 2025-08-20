<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Créer un nouvel incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if ($errors->any())
            <div role="alert" aria-live="polite" class="mb-6 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-red-800 dark:border-red-700 dark:bg-red-900/20 dark:text-red-200">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('resolveur.incidents.store') }}" class="space-y-5" aria-describedby="form-help">
            @csrf

            <p id="form-help" class="sr-only">Tous les champs sont obligatoires</p>

            <div>
                <label for="titre" class="block font-medium text-gray-800 dark:text-gray-200">Titre</label>
                <input
                    id="titre"
                    name="titre"
                    type="text"
                    class="mt-1 w-full rounded border border-gray-300 px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    value="{{ old('titre') }}"
                    required
                    aria-required="true"
                    autocomplete="off"
                    aria-describedby="titre-help @error('titre') err-titre @enderror"
                    aria-invalid="@error('titre') true @else false @enderror"
                    autofocus
                >
                <p id="titre-help" class="mt-1 text-sm text-gray-600 dark:text-gray-400">Entrez un titre court et précis.</p>
                @error('titre')
                    <div id="err-titre" role="alert" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="description" class="block font-medium text-gray-800 dark:text-gray-200">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="mt-1 w-full rounded border border-gray-300 px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    required
                    aria-required="true"
                    aria-describedby="description-help @error('description') err-description @enderror"
                    aria-invalid="@error('description') true @else false @enderror"
                >{{ old('description') }}</textarea>
                <p id="description-help" class="mt-1 text-sm text-gray-600 dark:text-gray-400">Décrivez le problème et les étapes pour le reproduire.</p>
                @error('description')
                    <div id="err-description" role="alert" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="utilisateur_id" class="block font-medium text-gray-800 dark:text-gray-200">Utilisateur</label>
                <select
                    id="utilisateur_id"
                    name="utilisateur_id"
                    class="mt-1 w-full rounded border border-gray-300 px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    required
                    aria-required="true"
                    aria-describedby="utilisateur-help @error('utilisateur_id') err-utilisateur @enderror"
                    aria-invalid="@error('utilisateur_id') true @else false @enderror"
                >
                    <option value="" disabled @selected(old('utilisateur_id')===null)>-- Sélectionner un utilisateur --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('utilisateur_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
                <p id="utilisateur-help" class="mt-1 text-sm text-gray-600 dark:text-gray-400">Choisissez le demandeur lié à l’incident.</p>
                @error('utilisateur_id')
                    <div id="err-utilisateur" role="alert" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Créer
            </button>
        </form>
    </div>
</x-app-layout>
