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

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Enregistrer</button>
        </form>
    </div>
</x-app-layout>
