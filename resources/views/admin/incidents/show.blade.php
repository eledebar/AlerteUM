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
            <h2 id="page-title" class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Détail de l'incident
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8" role="region" aria-labelledby="page-title">
        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" role="region" aria-labelledby="infos-title">
                <h3 id="infos-title" class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informations générales</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="label-titre">Titre</p>
                        <p class="text-base text-gray-900 dark:text-gray-100" aria-labelledby="label-titre">{{ $incident->titre }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="label-categorie">Catégorie</p>
                        <p class="text-base text-gray-900 dark:text-gray-100" aria-labelledby="label-categorie">{{ $incident->categorie }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="label-type">Type</p>
                        <p class="text-base text-gray-900 dark:text-gray-100" aria-labelledby="label-type">{{ $incident->type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="label-statut">Statut</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusChip }}" aria-label="Statut : {{ $statusLabel }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" role="region" aria-labelledby="desc-title">
                <h3 id="desc-title" class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Description</h3>
                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-line" aria-labelledby="desc-title">{{ $incident->description }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" role="region" aria-labelledby="hist-title">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="hist-title" class="text-lg font-semibold text-gray-800 dark:text-white">Historique</h3>
                    <a href="{{ route('admin.incidents.logs.export.csv', $incident) }}"
                       class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded"
                       aria-label="Exporter l’historique en CSV">
                        Exporter l’historique
                    </a>
                </div>

                @php
                    $logs = $incident->logs()->with('user')->latest()->take(30)->get();
                @endphp

                <div class="space-y-2 text-sm" role="list" aria-labelledby="hist-title">
                    @forelse ($logs as $log)
                        <div class="text-gray-700 dark:text-gray-300" role="listitem">
                            <span class="text-gray-500 dark:text-gray-400">
                                <time datetime="{{ $log->created_at->toIso8601String() }}">{{ $log->created_at->format('Y-m-d H:i:s') }}</time> —
                            </span>
                            @if($log->user)
                                <span class="text-gray-400">{{ $log->user->name }}</span> ·
                            @endif
                            <span>{{ $log->human_details }}</span>
                        </div>
                    @empty
                        <div class="text-gray-500 dark:text-gray-400">Aucun historique.</div>
                    @endforelse
                </div>
            </div>

            <div>
                <a href="{{ route('admin.incidents.index') }}"
                   class="inline-block bg-gray-700 hover:bg-gray-900 text-white font-medium px-4 py-2 rounded transition"
                   aria-label="Retour à la liste des incidents">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
