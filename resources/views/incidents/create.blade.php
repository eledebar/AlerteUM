<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">Nouvel Incident</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('resolveur.incidents.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium">Titre</label>
                <input type="text" name="titre" class="w-full border px-4 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Description</label>
                <textarea name="description" class="w-full border px-4 py-2" rows="5" required></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Utilisateur</label>
                <select name="utilisateur_id" class="w-full border px-4 py-2" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Créer</button>
        </form>
    </div>
</x-app-layout>
