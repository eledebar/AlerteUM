<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">
            Modifier l'incident
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8">
                <form method="POST" action="{{ route('resolveur.incidents.update', $incident) }}" id="resolveurIncidentForm">
                    @csrf
                    @method('PUT')

                    {{-- Titre --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-200 font-medium mb-2">Titre</label>
                        <input type="text" value="{{ old('titre', $incident->titre) }}" disabled
                               class="w-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg cursor-not-allowed">
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-200 font-medium mb-2">Description</label>
                        <textarea disabled class="w-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg cursor-not-allowed"
                                  rows="4">{{ old('description', $incident->description) }}</textarea>
                    </div>

                    {{-- Statut --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-200 font-medium mb-2">Statut</label>
                        <select name="statut" id="statut"
                                class="change-tracked w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg"
                                required>
                            <option value="nouveau" @selected($incident->statut == 'nouveau')>Nouveau</option>
                            <option value="en_cours" @selected($incident->statut == 'en_cours')>En cours</option>
                            <option value="résolu" @selected($incident->statut == 'résolu')>Résolu</option>
                        </select>
                    </div>

                    {{-- Assigner --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-200 font-medium mb-2">Assigner à</label>
                        <select name="attribue_a" id="attribue_a"
                                class="change-tracked w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg">
                            <option value="">— Aucun —</option>
                            @foreach($resolveurs as $resolveur)
                                <option value="{{ $resolveur->id }}" @selected($incident->attribue_a == $resolveur->id)>
                                    {{ $resolveur->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Commentaire --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-200 font-medium mb-2">Commentaire (optionnel)</label>
                        <textarea name="commentaire" id="commentaire"
                                  class="change-tracked w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg"
                                  rows="4"></textarea>
                    </div>

                    {{-- Commentaires précédents --}}
                    @if ($incident->commentaires->count())
                        <div class="mb-6">
                            <h3 class="text-gray-800 dark:text-gray-100 font-semibold mb-3">Commentaires précédents :</h3>
                            <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-300">
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

                    {{-- Botones --}}
                    <div class="flex justify-between">
                        <a href="{{ route('resolveur.incidents.index') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            ← Retour
                        </a>

                        <button type="submit" id="saveBtn" disabled
                                class="bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('resolveurIncidentForm');
            const saveBtn = document.getElementById('saveBtn');

            const initialValues = {
                statut: form.statut.value,
                attribue_a: form.attribue_a.value,
                commentaire: form.commentaire.value.trim()
            };

            const checkChanges = () => {
                const statutChanged = form.statut.value !== initialValues.statut;
                const attribueChanged = form.attribue_a.value !== initialValues.attribue_a;
                const commentaireChanged = form.commentaire.value.trim().length > 0;

                saveBtn.disabled = !(statutChanged || attribueChanged || commentaireChanged);
            };

            form.querySelectorAll('.change-tracked').forEach(el => {
                el.addEventListener('input', checkChanges);
                el.addEventListener('change', checkChanges);
            });
        });
    </script>
</x-app-layout>
