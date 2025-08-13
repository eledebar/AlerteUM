<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Détail de l'incident
            </h2>
            <a href="{{ route('resolveur.incidents.edit', $incident) }}"
               class="inline-flex items-center gap-2 rounded bg-blue-600 hover:bg-blue-700 text-white px-3 py-2">
                <img src="{{ asset('edit.webp') }}" alt="" class="h-5 w-5"> Modifier
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            {{-- Informations principales --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informations générales</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Titre</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $incident->titre }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Catégorie</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $incident->categorie }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $incident->type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                        <p class="text-base capitalize text-gray-900 dark:text-gray-100">{{ $incident->statut }}</p>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Description</h3>
                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $incident->description }}</p>
            </div>

            {{-- Utilisateur & Gestionnaire --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Utilisateurs</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Créé par</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">
                            {{ $incident->utilisateur->name ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Assigné à</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">
                            {{ $incident->gestionnaire->name ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Commentaires --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Commentaires</h3>

                @if ($incident->commentaires->isEmpty())
                    <p class="text-gray-600 dark:text-gray-300">Aucun commentaire pour cet incident.</p>
                @else
                    @foreach ($incident->commentaires as $comment)
                        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $comment->auteur->name ?? 'resolveur' }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <p class="text-gray-900 dark:text-gray-100">{{ $comment->commentaire }}</p>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Historique (con nombres) --}}
            @if(method_exists($incident, 'logs'))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Historique</h3>

                    @php
                        // Cargamos los usuarios para no hacer N+1 al pintar el actor
                        $logs = $incident->logs()->with('user')->latest()->take(30)->get();
                    @endphp

                    <div class="space-y-2 text-sm">
                        @forelse ($logs as $log)
                            <div class="text-gray-700 dark:text-gray-300">
                                <span class="text-gray-500 dark:text-gray-400">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }} —
                                </span>

                                {{-- Actor (quien hizo la acción), si existe --}}
                                @if($log->user)
                                    <span class="text-gray-400">{{ $log->user->name }}</span> ·
                                @endif

                                <span>{{ $log->action ?? $log->type ?? 'event' }}</span>

                                @php $details = trim($log->human_details); @endphp
                                @if($details !== '')
                                    : <span class="italic">{{ $details }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="text-gray-500 dark:text-gray-400">Aucun historique.</div>
                        @endforelse
                    </div>
                </div>
            @endif

            <div>
                <a href="{{ route('resolveur.incidents.index') }}"
                   class="inline-block bg-gray-700 hover:bg-gray-900 text-white font-medium px-4 py-2 rounded transition">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
