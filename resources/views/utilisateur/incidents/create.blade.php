<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Nouvel incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('utilisateur.incidents.categories') }}" class="mb-4 inline-block px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            ← Retour au catalogue
        </a>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-red-800" role="alert" aria-live="assertive">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('utilisateur.incidents.store') }}">
            @csrf

            <div class="mb-4">
                <label for="categorie_display" class="block font-medium text-gray-800 dark:text-gray-200">
                    Catégorie sélectionnée
                </label>
                <input id="categorie_display" type="text" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white border rounded px-4 py-2" value="{{ ucfirst($categorie) }}" disabled aria-disabled="true" aria-describedby="categorie_help">
                <input type="hidden" name="categorie" value="{{ $categorie }}" aria-label="categorie">
                <p id="categorie_help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">Valeur fournie par le catalogue.</p>
            </div>

            <div class="mb-4">
                <label for="type" class="block font-medium text-gray-800 dark:text-gray-200">Type de problème</label>
                <select name="type" id="type" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border rounded px-4 py-2" required aria-required="true" aria-describedby="type_help type-description">
                    <option value="">-- Sélectionnez un type --</option>
                    @foreach ($types as $key => $desc)
                        <option value="{{ $key }}" @selected(old('type')===$key)>{{ $key }}</option>
                    @endforeach
                </select>
                <p id="type_help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">Choisissez le type qui correspond à votre situation.</p>
            </div>

            <div id="info-section" class="mb-6 hidden" role="region" aria-live="polite" aria-hidden="true">
                <h3 class="block font-medium text-gray-800 dark:text-gray-200">Détails du problème sélectionné</h3>
                <div id="type-description" class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-4 whitespace-pre-line" role="note"></div>
            </div>

            <div class="mb-4">
                <label for="titre" class="block font-medium text-gray-800 dark:text-gray-200">Titre</label>
                <input id="titre" type="text" name="titre" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border rounded px-4 py-2" required aria-required="true" autocomplete="off" value="{{ old('titre') }}" aria-describedby="titre_help">
                <p id="titre_help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">Un résumé court et clair.</p>
                @error('titre')<p class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block font-medium text-gray-800 dark:text-gray-200">Description</label>
                <textarea name="description" id="description" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border rounded px-4 py-2" rows="5" required aria-required="true" aria-describedby="desc_help">{{ old('description') }}</textarea>
                <p id="desc_help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">Ajoutez le contexte, les étapes pour reproduire et les messages d’erreur.</p>
                @error('description')<p class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded" aria-label="Créer l’incident">
                Créer
            </button>
        </form>
    </div>

    <script>
        const typeSelect = document.getElementById('type');
        const descriptionBox = document.getElementById('type-description');
        const infoSection = document.getElementById('info-section');
        const explanationTexts = {
            @foreach ($types as $key => $desc)
                "{{ $key }}": `{!! $desc !!}`,
            @endforeach
        };
        function toggleInfoSection(show) {
            if (show) {
                infoSection.classList.remove('hidden');
                infoSection.setAttribute('aria-hidden', 'false');
            } else {
                infoSection.classList.add('hidden');
                infoSection.setAttribute('aria-hidden', 'true');
            }
        }
        function updateTypeInfo(val){
            if (explanationTexts[val]) {
                descriptionBox.textContent = explanationTexts[val];
                toggleInfoSection(true);
            } else {
                descriptionBox.textContent = '';
                toggleInfoSection(false);
            }
        }
        typeSelect.addEventListener('change', function () { updateTypeInfo(this.value); });
        if (typeSelect.value) updateTypeInfo(typeSelect.value);
    </script>
</x-app-layout>
