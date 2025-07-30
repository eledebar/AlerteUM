<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvel incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('utilisateur.incidents.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium">Titre</label>
                <input type="text" name="titre" class="w-full border px-4 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Description</label>
                <textarea name="description" class="w-full border px-4 py-2" rows="5" required></textarea>
            </div>

            {{-- No hay campo para "statut", lo pondremos en el controlador automáticamente como "nouveau" --}}

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Créer
            </button>
        </form>
    </div>
</x-app-layout>
