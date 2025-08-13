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

    $hasSla   = !empty($incident->sla_due_at);
    $isClosed = in_array(Incident::normalizeStatus($incident->statut), [Incident::STATUT_RESOLU, Incident::STATUT_FERME]);
    $slaTxt   = '—'; $slaClass='';
    if ($hasSla && !$isClosed) {
        $deadline = \Illuminate\Support\Carbon::parse($incident->sla_due_at);
        if (now()->lessThanOrEqualTo($deadline)) {
            $slaTxt='OK';    $slaClass='bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300';
        } else {
            $slaTxt='Breach';$slaClass='bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300';
        }
    }
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
        {{-- flashes --}}
        @if(session('success'))
            <div class="mb-5 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-900">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-rose-900">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Columna principal (3/4) --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="rounded-2xl border border-gray-200/60 bg-white/70 backdrop-blur dark:bg-gray-900/60 dark:border-gray-800 shadow-sm">
                    <div class="p-6 space-y-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusChip }}">
                                {{ $statusLabel }}
                            </span>

                            @if($hasSla && !$isClosed)
                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-semibold {{ $slaClass }}">
                                    {{ $slaTxt }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    due {{ optional($incident->sla_due_at)->format('Y-m-d H:i') }}
                                </span>
                            @endif
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

                {{-- Comentarios en estilo “timeline” limpio --}}
                <div class="rounded-2xl border border-gray-200/60 bg-white/70 backdrop-blur dark:bg-gray-900/60 dark:border-gray-800 shadow-sm">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Commentaires</h3>

                        @if ($incident->commentaires->isEmpty())
                            <div class="mt-3 text-gray-600 dark:text-gray-300">Aucun commentaire.</div>
                        @else
                            <ol class="relative mt-4 border-s border-gray-200 dark:border-gray-700">
                                @foreach ($incident->commentaires as $c)
                                    <li class="mb-6 ms-6">
                                        <span class="absolute -start-3.5 flex h-7 w-7 items-center justify-center rounded-full bg-indigo-600 text-white text-xs shadow ring-2 ring-white dark:ring-gray-900">
                                            {{ strtoupper(mb_substr($c->auteur->name ?? 'U', 0, 1)) }}
                                        </span>
                                        <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-3">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                <strong class="text-gray-700 dark:text-gray-200">{{ $c->auteur->name ?? '—' }}</strong>
                                                · {{ $c->created_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="mt-1 leading-relaxed">{{ $c->commentaire }}</div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Panel lateral (1/4), sticky --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-4">
                    {{-- Sólo para el declarante y si está RESOLU --}}
                    @if (Auth::id() === $incident->utilisateur_id && Incident::normalizeStatus($incident->statut) === Incident::STATUT_RESOLU)
                        <div class="rounded-2xl border border-amber-300 bg-amber-50/80 dark:bg-amber-900/20 dark:border-amber-700 p-5">
                            <h4 class="font-semibold mb-3 text-amber-900 dark:text-amber-200">
                                Le ticket est-il vraiment résolu ?
                            </h4>

                            <form method="POST" action="{{ route('utilisateur.incidents.confirmClose', $incident) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 font-medium transition">
                                    ✅ Oui, fermer définitivement
                                </button>
                            </form>

                            <div class="my-3 h-px bg-amber-200/60 dark:bg-amber-800/60"></div>

                            <details class="group rounded-xl border border-rose-300 bg-white dark:bg-gray-900 dark:border-rose-700">
                                <summary class="list-none cursor-pointer rounded-t-xl bg-rose-600 px-4 py-3 text-white hover:bg-rose-700">
                                    ❌ Non, le problème persiste
                                </summary>
                                <form method="POST" action="{{ route('utilisateur.incidents.rejectClose', $incident) }}"
                                      class="p-4 space-y-3">
                                    @csrf
                                    <textarea name="reason" rows="4" required
                                              class="w-full rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-800"
                                              placeholder="Explique brièvement pourquoi ce n’est pas résolu…"></textarea>
                                    <button class="w-full rounded-md bg-rose-600 px-4 py-2 font-medium text-white hover:bg-rose-700">
                                        Envoyer et rouvrir
                                    </button>
                                </form>
                            </details>
                        </div>
                    @else
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5 text-sm text-gray-600 dark:text-gray-300">
                            Aucun action disponible pour cet incident.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
