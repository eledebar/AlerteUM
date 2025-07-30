<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl">Modifier l’incident</h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('incidents.update', $incident) }}">
            @csrf @method('PUT')

            <div class="mt-4">
                <label>Titre</label>
                <input type="text" name="titre" value="{{ $incident->titre }}" class="border p-2 w-full">
            </div>

            <div class="mt-4">
                <label>Description</label>
                <textarea name="description" class="border p-2 w-full">{{ $incident->description }}</textarea>
            </div>

            <div class="mt-4">
                <label>Statut</label>
                <select name="statut" class="border p-2 w-full">
                    <option value="nouveau" @selected($incident->statut === 'nouveau')>Nouveau</option>
                    <option value="en_cours" @selected($incident->statut === 'en_cours')>En cours</option>
                    <option value="résolu" @selected($incident->statut === 'résolu')>Résolu</option>
                </select>
            </div>

            <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">Mettre à jour</button>
        </form>
    </div>
</x-app-layout>
