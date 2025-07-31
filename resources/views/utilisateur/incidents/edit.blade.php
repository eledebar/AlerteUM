<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Modifier l'incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('utilisateur.incidents.update', $incident) }}" id="incidentForm">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Titre</label>
                <input type="text" name="titre" id="titre"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded"
                       value="{{ $incident->titre }}" required>
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Description</label>
                <textarea name="description" id="description"
                          class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded"
                          rows="5" required>{{ $incident->description }}</textarea>
            </div>

            @if (auth()->user()->estAdmin())
                <div class="mb-4">
                    <label class="block font-medium text-gray-800 dark:text-gray-200">Statut</label>
                    <select name="statut" id="statut"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded" required>
                        <option value="nouveau" @selected($incident->statut == 'nouveau')>Nouveau</option>
                        <option value="en_cours" @selected($incident->statut == 'en_cours')>En cours</option>
                        <option value="résolu" @selected($incident->statut == 'résolu')>Résolu</option>
                    </select>
                </div>
            @endif

            <div class="flex gap-4 mt-6">
                <a href="{{ route('utilisateur.incidents.index') }}"
                   class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition">
                    ← Retour
                </a>

                <button type="submit" id="saveBtn"
                        class="bg-green-600 text-white px-4 py-2 rounded opacity-50 cursor-not-allowed"
                        disabled>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('incidentForm');
        const saveBtn = document.getElementById('saveBtn');
        const originalData = {
            titre: document.getElementById('titre').value,
            description: document.getElementById('description').value,
            statut: document.getElementById('statut')?.value || ''
        };

        form.addEventListener('input', () => {
            const current = {
                titre: document.getElementById('titre').value,
                description: document.getElementById('description').value,
                statut: document.getElementById('statut')?.value || ''
            };

            const isChanged = current.titre !== originalData.titre ||
                              current.description !== originalData.description ||
                              current.statut !== originalData.statut;

            saveBtn.disabled = !isChanged;
            saveBtn.classList.toggle('opacity-50', !isChanged);
            saveBtn.classList.toggle('cursor-not-allowed', !isChanged);
        });
    </script>
</x-app-layout>
