<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">Modifier l'incident</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('admin.incidents.update', $incident) }}" id="adminIncidentForm">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Titre</label>
                <input type="text" name="titre" id="titre"
                       value="{{ $incident->titre }}"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded"
                       required>
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Description</label>
                <textarea name="description" id="description"
                          class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded"
                          rows="5" required>{{ $incident->description }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Statut</label>
                <select name="statut" id="statut"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded" required>
                    <option value="nouveau" @selected($incident->statut == 'nouveau')>Nouveau</option>
                    <option value="en_cours" @selected($incident->statut == 'en_cours')>En cours</option>
                    <option value="résolu" @selected($incident->statut == 'résolu')>Résolu</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Commentaire (optionnel)</label>
                <textarea name="commentaire" id="commentaire"
                          class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded"
                          rows="4"></textarea>
            </div>

            @if ($incident->commentaires->count())
                <div class="mb-6">
                    <h3 class="font-bold mb-2 text-gray-800 dark:text-gray-100">Commentaires précédents :</h3>
                    <ul class="list-disc pl-5 text-sm text-gray-700 dark:text-gray-300">
                        @foreach ($incident->commentaires as $commentaire)
                            <li>
                                <strong>{{ $commentaire->auteur->name }}:</strong>
                                {{ $commentaire->commentaire }}
                                <em class="text-gray-500 dark:text-gray-400 text-xs">({{ $commentaire->created_at->format('d/m/Y H:i') }})</em>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex gap-4 mt-6">
                <a href="{{ route('admin.incidents.index') }}"
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
        const form = document.getElementById('adminIncidentForm');
        const saveBtn = document.getElementById('saveBtn');

        const originalData = {
            titre: document.getElementById('titre').value,
            description: document.getElementById('description').value,
            statut: document.getElementById('statut').value,
        };

        form.addEventListener('input', () => {
            const titre = document.getElementById('titre').value;
            const description = document.getElementById('description').value;
            const statut = document.getElementById('statut').value;
            const commentaire = document.getElementById('commentaire').value.trim();

            const hasChanges = titre !== originalData.titre ||
                               description !== originalData.description ||
                               statut !== originalData.statut ||
                               commentaire.length > 0;

            saveBtn.disabled = !hasChanges;
            saveBtn.classList.toggle('opacity-50', !hasChanges);
            saveBtn.classList.toggle('cursor-not-allowed', !hasChanges);
        });
    </script>
</x-app-layout>
