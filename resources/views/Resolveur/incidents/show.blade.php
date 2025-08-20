@php
    use App\Models\Incident;
    $statusLabel = Incident::labelForStatus($incident->statut);
    $statusChip = match(Incident::normalizeStatus($incident->statut)) {
        Incident::STATUT_NOUVEAU => 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-200',
        Incident::STATUT_EN_COURS => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        Incident::STATUT_RESOLU   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
        Incident::STATUT_FERME    => 'bg-slate-200 text-slate-800 dark:bg-slate-600/40 dark:text-slate-100',
        default                   => 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-200',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Détail de l'incident
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">

            {{-- Informations générales --}}
            <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
                     aria-labelledby="info-title" role="region">
                <h3 id="info-title" class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" role="list" aria-label="Attributs de l'incident">
                    <div role="listitem" aria-labelledby="lbl-titre">
                        <p id="lbl-titre" class="text-sm text-gray-500 dark:text-gray-400">Titre</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $incident->titre }}</p>
                    </div>
                    <div role="listitem" aria-labelledby="lbl-categorie">
                        <p id="lbl-categorie" class="text-sm text-gray-500 dark:text-gray-400">Catégorie</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $incident->categorie }}</p>
                    </div>
                    <div role="listitem" aria-labelledby="lbl-type">
                        <p id="lbl-type" class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $incident->type }}</p>
                    </div>
                    <div role="listitem" aria-labelledby="lbl-statut">
                        <p id="lbl-statut" class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusChip }}"
                              aria-label="Statut : {{ $statusLabel }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </section>

            {{-- Description --}}
            <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
                     aria-labelledby="desc-title" role="region">
                <h3 id="desc-title" class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Description
                </h3>
                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-line">
                    {{ $incident->description }}
                </p>
            </section>

            {{-- Historique --}}
            <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
                     aria-labelledby="hist-title" role="region">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="hist-title" class="text-lg font-semibold text-gray-800 dark:text-white">
                        Historique
                    </h3>
                    <a href="{{ route('resolveur.incidents.logs.export.csv', $incident) }}"
                       class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded"
                       aria-label="Exporter l’historique de l’incident en CSV">
                        Exporter l’historique
                    </a>
                </div>

                @php
                    $logs = $incident->logs()->with('user')->latest()->take(30)->get();
                @endphp

                @if($logs->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">Aucun historique.</p>
                @else
                    <ul class="space-y-2 text-sm" role="list" aria-label="Derniers événements">
                        @foreach ($logs as $log)
                            <li class="text-gray-700 dark:text-gray-300" role="listitem">
                                <time class="text-gray-500 dark:text-gray-400"
                                      datetime="{{ $log->created_at->toIso8601String() }}">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </time>
                                <span> — </span>
                                @if($log->user)
                                    <span class="text-gray-400">{{ $log->user->name }}</span>
                                    <span aria-hidden="true"> · </span>
                                @endif
                                <span>{{ $log->human_details }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Commentaires --}}
            <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
                     aria-labelledby="comments-title" role="region">
                <h3 id="comments-title" class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Commentaires
                </h3>

                @if ($incident->commentaires->isEmpty())
                    <p class="text-gray-600 dark:text-gray-300">Aucun commentaire pour cet incident.</p>
                @else
                    @foreach ($incident->commentaires as $idx => $comment)
                        @php $cid = $comment->id ?? $idx; @endphp
                        <article class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700"
                                 aria-labelledby="cmt-ttl-{{ $cid }}">
                            <header class="flex justify-between items-center mb-1">
                                <span id="cmt-ttl-{{ $cid }}" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $comment->auteur->name ?? 'resolveur' }}
                                </span>
                                <time class="text-xs text-gray-500 dark:text-gray-400"
                                      datetime="{{ $comment->created_at->toIso8601String() }}">
                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                </time>
                            </header>
                            <p class="text-gray-900 dark:text-gray-100">
                                {{ $comment->commentaire }}
                            </p>
                        </article>
                    @endforeach
                @endif
            </section>

            <div>
                <a href="{{ route('resolveur.incidents.index') }}"
                   class="inline-block bg-gray-700 hover:bg-gray-900 text-white font-medium px-4 py-2 rounded transition"
                   aria-label="Retour à la liste des incidents">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
