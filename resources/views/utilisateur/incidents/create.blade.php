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

        <form method="POST" action="{{ route('utilisateur.incidents.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">
                    Catégorie sélectionnée
                </label>
                <input type="text" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white border rounded px-4 py-2" value="{{ ucfirst($categorie) }}" disabled>
                <input type="hidden" name="categorie" value="{{ $categorie }}">
            </div>

            <div class="mb-4">
                <label for="type" class="block font-medium text-gray-800 dark:text-gray-200">Type de problème</label>
                <select name="type" id="type" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border rounded px-4 py-2" required>
                    <option value="">-- Sélectionnez un type --</option>
                    @foreach ($types as $key => $desc)
                        <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach
                </select>
            </div>

            <div id="info-section" class="mb-6 hidden">
                <h3 class="block font-medium text-gray-800 dark:text-gray-200">Détails du problème sélectionné</h3>
                <div id="type-description" class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-4 whitespace-pre-line"></div>
            </div>

            <div class="mb-4">
                <label for="titre" class="block font-medium text-gray-800 dark:text-gray-200">Titre</label>
                <input type="text" name="titre" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border rounded px-4 py-2" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block font-medium text-gray-800 dark:text-gray-200">Description</label>
                <textarea name="description" id="description" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border rounded px-4 py-2" rows="5" required></textarea>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
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

        typeSelect.addEventListener('change', function () {
            const selected = this.value;
            if (explanationTexts[selected]) {
                descriptionBox.textContent = explanationTexts[selected];
                infoSection.classList.remove('hidden');
            } else {
                descriptionBox.textContent = '';
                infoSection.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
