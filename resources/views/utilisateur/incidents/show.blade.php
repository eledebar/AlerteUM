<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">
            Détail de l'incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-bold mb-2">Titre</h3>
            <p class="mb-4">{{ $incident->titre }}</p>

            <h3 class="text-lg font-bold mb-2">Description</h3>
            <p class="mb-4">{{ $incident->description }}</p>

            <h3 class="text-lg font-bold mb-2">Statut</h3>
            <p class="mb-4 capitalize">{{ str_replace('_', ' ', $incident->statut) }}</p>

            <h3 class="text-lg font-bold mb-2">Commentaires</h3>
            @if ($incident->commentaires->count())
                <ul class="list-disc pl-5 text-gray-700 dark:text-gray-300">
                    @foreach ($incident->commentaires as $commentaire)
                        <li class="mb-2">
                            <strong>{{ $commentaire->auteur->name }}:</strong>
                            {{ $commentaire->commentaire }}
                            <span class="text-gray-500 dark:text-gray-400 text-sm">({{ $commentaire->created_at->format('d/m/Y H:i') }})</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucun commentaire pour l’instant.</p>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('utilisateur.incidents.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                ← Retour à la liste
            </a>
        </div>
    </div>
</x-app-layout>
