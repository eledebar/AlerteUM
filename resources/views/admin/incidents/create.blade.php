<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Créer un nouvel incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('admin.incidents.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Titre</label>
                <input type="text" name="titre" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded" value="{{ old('titre') }}" required>
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Description</label>
                <textarea name="description" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded" rows="5" required>{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-800 dark:text-gray-200">Utilisateur</label>
                <select name="utilisateur_id" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded" required>
                    <option value="">-- Sélectionner un utilisateur --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('utilisateur_id') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                Créer
            </button>
        </form>
    </div>
</x-app-layout>
