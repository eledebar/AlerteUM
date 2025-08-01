<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Détail de l'incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-gray-900 dark:text-white space-y-4">

            <div>
                <h3 class="font-bold">Titre</h3>
                <p>{{ $incident->titre }}</p>
            </div>

            <div>
                <h3 class="font-bold">Description</h3>
                <p>{{ $incident->description }}</p>
            </div>

            <div>
                <h3 class="font-bold">Catégorie</h3>
                <p>{{ ucfirst($incident->categorie ?? 'Non spécifiée') }}</p>
            </div>

            <div>
                <h3 class="font-bold">Type</h3>
                <p>{{ $incident->type ?? '—' }}</p>
            </div>

            <div>
                <h3 class="font-bold">Statut</h3>
                <p class="capitalize">{{ str_replace('_', ' ', $incident->statut) }}</p>
            </div>

            <div>
                <h3 class="font-bold">Commentaires</h3>
                @if ($incident->commentaires->isEmpty())
                    <p>Aucun commentaire pour l’instant.</p>
                @else
                    <ul class="space-y-2 mt-2">
                        @foreach ($incident->commentaires as $commentaire)
                            <li class="border border-gray-300 dark:border-gray-600 rounded p-2">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $commentaire->auteur->name ?? 'Utilisateur inconnu' }} - 
                                    <span class="text-xs text-gray-500">{{ $commentaire->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>{{ $commentaire->contenu }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('utilisateur.incidents.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                ← Retour à la liste
            </a>
        </div>
    </div>
</x-app-layout>
