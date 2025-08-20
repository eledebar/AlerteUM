<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Modifier l'incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if ($errors->any())
            <div id="form-errors" class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-red-800"
                 role="alert" aria-live="assertive" tabindex="-1">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('utilisateur.incidents.update', $incident) }}" id="incidentForm" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="titre" class="block font-medium text-gray-800 dark:text-gray-200">Titre</label>
                <input
                    type="text"
                    name="titre"
                    id="titre"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded"
                    value="{{ old('titre', $incident->titre) }}"
                    required
                    aria-required="true"
                    aria-describedby="titre_help"
                    @error('titre') aria-invalid="true" @enderror
                >
                <p id="titre_help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">Un résumé court et clair.</p>
                @error('titre')<p class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block font-medium text-gray-800 dark:text-gray-200">Description</label>
                <textarea
                    name="description"
                    id="description"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded"
                    rows="5"
                    required
                    aria-required="true"
                    aria-describedby="desc_help"
                    @error('description') aria-invalid="true" @enderror
                >{{ old('description', $incident->description) }}</textarea>
                <p id="desc_help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">Ajoutez le contexte et les étapes pour reproduire.</p>
                @error('description')<p class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>@enderror
            </div>

            @if (auth()->user()->estResolveur())
                <div class="mb-4">
                    <label for="statut" class="block font-medium text-gray-800 dark:text-gray-200">Statut</label>
                    <select
                        name="statut"
                        id="statut"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded"
                        required
                        aria-required="true"
                        aria-describedby="statut_help"
                        @error('statut') aria-invalid="true" @enderror
                    >
                        <option value="nouveau" @selected(old('statut', $incident->statut) == 'nouveau')>Nouveau</option>
                        <option value="en_cours" @selected(old('statut', $incident->statut) == 'en_cours')>En cours</option>
                        <option value="résolu" @selected(old('statut', $incident->statut) == 'résolu')>Résolu</option>
                    </select>
                    <p id="statut_help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">Choisissez l’état actuel du ticket.</p>
                    @error('statut')<p class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>@enderror
                </div>
            @endif

            <div class="flex gap-4 mt-6">
                <a href="{{ route('utilisateur.incidents.index') }}"
                   class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition">
                    ← Retour
                </a>

                <button type="submit" id="saveBtn"
                        class="bg-green-600 text-white px-4 py-2 rounded opacity-50 cursor-not-allowed"
                        disabled
                        aria-disabled="true"
                        aria-describedby="saveHint saveStatusLive">
                    Enregistrer
                </button>
                <span id="saveHint" class="sr-only">Le bouton s’active lorsque des modifications sont détectées.</span>
                <span id="saveStatusLive" class="sr-only" aria-live="polite" aria-atomic="true"></span>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const form = document.getElementById('incidentForm');
            const saveBtn = document.getElementById('saveBtn');
            const live = document.getElementById('saveStatusLive');

            const titreEl = document.getElementById('titre');
            const descEl  = document.getElementById('description');
            const statutEl = document.getElementById('statut');

            const originalData = {
                titre: titreEl.value,
                description: descEl.value,
                statut: statutEl ? (statutEl.value || '') : ''
            };

            function updateState() {
                const current = {
                    titre: titreEl.value,
                    description: descEl.value,
                    statut: statutEl ? (statutEl.value || '') : ''
                };
                const isChanged =
                    current.titre !== originalData.titre ||
                    current.description !== originalData.description ||
                    current.statut !== originalData.statut;

                saveBtn.disabled = !isChanged;
                saveBtn.setAttribute('aria-disabled', isChanged ? 'false' : 'true');
                saveBtn.classList.toggle('opacity-50', !isChanged);
                saveBtn.classList.toggle('cursor-not-allowed', !isChanged);
                if (live) live.textContent = isChanged ? 'Modifications détectées, bouton Enregistrer activé.' : 'Aucune modification, bouton Enregistrer désactivé.';
            }

            form.addEventListener('input', updateState);
            document.addEventListener('DOMContentLoaded', function(){
                const err = document.getElementById('form-errors');
                if (err) err.focus();
            });
        })();
    </script>
</x-app-layout>
