@php
    use App\Models\Incident;

    $code = $incident->public_id ?? ('INC-'.str_pad($incident->id, 4, '0', STR_PAD_LEFT));

    $statusLabel = Incident::labelForStatus($incident->statut);
    $statusChip = match(Incident::normalizeStatus($incident->statut)) {
        Incident::STATUT_NOUVEAU => 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-200',
        Incident::STATUT_EN_COURS => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        Incident::STATUT_RESOLU   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
        Incident::STATUT_FERME    => 'bg-slate-200 text-slate-800 dark:bg-slate-600/40 dark:text-slate-100',
        default                   => 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-200',
    };

    $showActionPanel = (Auth::id() === $incident->utilisateur_id)
        && (Incident::normalizeStatus($incident->statut) === Incident::STATUT_RESOLU);

    $mainCols = $showActionPanel ? 'lg:col-span-3' : 'lg:col-span-4';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                Incident <span class="text-indigo-500">{{ $code }}</span>
            </h2>
            <a href="{{ route('utilisateur.incidents.index') }}"
               class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">← Retour à la liste</a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-5 rounded-xl border border-emerald-300/70 bg-emerald-50/80 px-4 py-3 text-emerald-900 dark:border-emerald-700/50 dark:bg-emerald-900/20 dark:text-emerald-100">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 rounded-xl border border-rose-300/70 bg-rose-50/80 px-4 py-3 text-rose-900 dark:border-rose-700/50 dark:bg-rose-900/20 dark:text-rose-100">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="{{ $mainCols }} space-y-6">
                <div class="rounded-2xl border border-gray-200/60 bg-white/80 backdrop-blur-md dark:bg-gray-900/60 dark:border-gray-800 shadow-sm">
                    <div class="p-6 space-y-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusChip }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Titre</div>
                                <div class="mt-1 font-medium">{{ $incident->titre }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Créé</div>
                                <div class="mt-1">{{ optional($incident->created_at)->format('Y-m-d H:i') }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Catégorie</div>
                                <div class="mt-1">{{ ucfirst($incident->categorie ?? '—') }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Type</div>
                                <div class="mt-1">{{ $incident->type ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Déclarant</div>
                                <div class="mt-1">{{ optional($incident->utilisateur)->name ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Assigné à</div>
                                <div class="mt-1">{{ optional($incident->gestionnaire)->name ?? '—' }}</div>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Description</div>
                            <div class="mt-2 whitespace-pre-line leading-relaxed">{{ $incident->description }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200/60 bg-white/80 backdrop-blur-md dark:bg-gray-900/60 dark:border-gray-800 shadow-sm">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Commentaires</h3>

                        @if ($incident->commentaires->isEmpty())
                            <div class="mt-3 text-gray-600 dark:text-gray-300">Aucun commentaire.</div>
                        @else
                            <div class="mt-4 space-y-4">
                                @foreach ($incident->commentaires as $c)
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-3">
                                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                            <strong class="text-gray-700 dark:text-gray-200">{{ $c->auteur->name ?? '—' }}</strong>
                                            <span>{{ $c->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="mt-1 leading-relaxed text-gray-900 dark:text-gray-100">{{ $c->commentaire }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($showActionPanel)
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-24">
                        <section class="rounded-2xl border border-amber-300/60 shadow-sm bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/10 dark:border-amber-700/50">
                            <header class="flex items-start gap-3 px-5 pt-5">
                                <div class="shrink-0 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-300 ring-1 ring-amber-400/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 4.418-2.239 7.5-7 9-4.761-1.5-7-4.582-7-9V7l7-4z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 12.5l2 2 4-4"/>
                                    </svg>
                                </div>
                                <div class="pt-0.5">
                                    <h3 class="font-semibold text-amber-900 dark:text-amber-100">Valider la résolution</h3>
                                    <p class="mt-1 text-sm text-amber-800/80 dark:text-amber-200/80">Confirmez si tout fonctionne ou rouvrez le ticket.</p>
                                </div>
                            </header>

                            <div class="px-5 pb-5 pt-4 space-y-3">
                                <form method="POST" action="{{ route('utilisateur.incidents.confirmClose', $incident) }}">
                                    @csrf
                                    <button type="submit" class="w-full whitespace-nowrap inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm bg-emerald-600 text-white font-medium shadow-sm ring-1 ring-inset ring-emerald-500/50 hover:bg-emerald-500 focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-emerald-400 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                                        </svg>
                                        Fermer définitivement
                                    </button>
                                </form>

                                <details class="rounded-xl border border-rose-300/70 bg-white/60 dark:bg-gray-900/60 dark:border-rose-700/50">
                                    <summary class="list-none">
                                        <div class="w-full whitespace-nowrap inline-flex items-center justify-center gap-2 rounded-t-xl px-4 py-2.5 text-sm bg-rose-600 text-white font-medium hover:bg-rose-500 transition cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                                            </svg>
                                            Rouvrir le ticket
                                        </div>
                                    </summary>
                                    <form method="POST" action="{{ route('utilisateur.incidents.rejectClose', $incident) }}" class="p-4 space-y-3">
                                        @csrf
                                        <label for="reason" class="text-sm text-gray-700 dark:text-gray-300">Expliquez brièvement le problème</label>
                                        <textarea id="reason" name="reason" rows="4" required class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-800 focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-rose-400"></textarea>
                                        <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-500 focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-rose-400 transition">
                                            Envoyer et rouvrir
                                        </button>
                                    </form>
                                </details>
                            </div>
                        </section>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
