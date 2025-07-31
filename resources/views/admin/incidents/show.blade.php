<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Détail de l'incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Titre</h3>
            <p class="text-gray-900 dark:text-gray-100">{{ $incident->titre }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Description</h3>
            <p class="text-gray-900 dark:text-gray-100">{{ $incident->description }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Statut</h3>
            <p class="capitalize text-gray-900 dark:text-gray-100">{{ $incident->statut }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Créé par</h3>
            <p class="text-gray-900 dark:text-gray-100">{{ $incident->utilisateur->name ?? '—' }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Commentaires</h3>

            @if ($incident->commentaires->isEmpty())
                <p class="text-gray-600 dark:text-gray-300">Aucun commentaire pour cet incident.</p>
            @else
                @foreach ($incident->commentaires as $comment)
                    <div class="mb-4 border-t border-gray-300 dark:border-gray-700 pt-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                            <span class="font-semibold">{{ $comment->auteur->name ?? 'Admin' }}</span>
                            — <span class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        </p>
                        <p class="text-gray-900 dark:text-gray-100">{{ $comment->commentaire }}</p>
                    </div>
                @endforeach
            @endif
        </div>

        <a href="{{ route('admin.incidents.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition">
            ← Retour
        </a>
    </div>
</x-app-layout>
