<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">Modifier l'incident</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('admin.incidents.update', $incident) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-medium">Titre</label>
                <input type="text" name="titre" value="{{ $incident->titre }}" class="w-full border px-4 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Description</label>
                <textarea name="description" class="w-full border px-4 py-2" rows="5" required>{{ $incident->description }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Statut</label>
                <select name="statut" class="w-full border px-4 py-2" required>
                    <option value="nouveau" @selected($incident->statut == 'nouveau')>Nouveau</option>
                    <option value="en_cours" @selected($incident->statut == 'en_cours')>En cours</option>
                    <option value="résolu" @selected($incident->statut == 'résolu')>Résolu</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Commentaire (optionnel)</label>
                <textarea name="commentaire" class="w-full border px-4 py-2" rows="4"></textarea>
            </div>

            @if ($incident->commentaires->count())
                <div class="mb-6">
                    <h3 class="font-bold mb-2">Commentaires précédents :</h3>
                    <ul class="list-disc pl-5 text-sm text-gray-700">
                        @foreach ($incident->commentaires as $commentaire)
                            <li>
                                <strong>{{ $commentaire->auteur->name }}:</strong>
                                {{ $commentaire->commentaire }}
                                <em class="text-gray-500 text-xs">({{ $commentaire->created_at->format('d/m/Y H:i') }})</em>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Enregistrer</button>
        </form>
    </div>
</x-app-layout>
