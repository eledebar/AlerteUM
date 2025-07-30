<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Détail de l'incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-2">Titre</h3>
            <p>{{ $incident->titre }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-2">Description</h3>
            <p>{{ $incident->description }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-2">Statut</h3>
            <p class="capitalize">{{ $incident->statut }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-2">Créé par</h3>
            <p>{{ $incident->utilisateur->name ?? '—' }}</p>
        </div>

        {{-- Commentaires --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-4">Commentaires</h3>

            @if ($incident->commentaires->isEmpty())
                <p class="text-gray-600">Aucun commentaire pour cet incident.</p>
            @else
                @foreach ($incident->commentaires as $comment)
                    <div class="mb-4 border-t pt-4">
                        <p class="text-sm text-gray-700 mb-1">
                            <span class="font-semibold">{{ $comment->auteur->name ?? 'Admin' }}</span>
                            — <span class="text-xs text-gray-500">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        </p>
                        <p>{{ $comment->commentaire }}</p>
                    </div>
                @endforeach
            @endif
        </div>

        <a href="{{ route('admin.incidents.index') }}" class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
            ← Retour
        </a>
    </div>
</x-app-layout>
