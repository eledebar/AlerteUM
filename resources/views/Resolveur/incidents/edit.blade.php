<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">
            Gérer l'incident
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div id="toast" class="fixed top-4 right-4 z-50 hidden rounded-lg px-4 py-3 shadow-lg text-sm font-medium bg-green-600 text-white dark:bg-green-500" role="status" aria-live="polite" aria-atomic="true">
                <span id="toastText">OK</span>
                <button type="button" id="toastClose" class="ml-3 inline-flex items-center rounded bg-white/20 px-2 py-0.5 focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-white" aria-label="Fermer la notification">×</button>
            </div>
            <span id="screenreader-live" class="sr-only" aria-live="polite" aria-atomic="true"></span>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6" role="region" aria-labelledby="infos-title">
                <h3 id="infos-title" class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informations</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Titre</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $incident->titre }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Catégorie</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $incident->categorie }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                        <p class="text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $incident->description }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6" role="region" aria-labelledby="actions-title">
                <h3 id="actions-title" class="font-semibold text-gray-800 dark:text-white mb-4">Actions</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="statut" class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Statut</label>
                        <select id="statut" class="w-full rounded border px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-white" aria-describedby="statut-help">
                            <option value="nouveau" @selected($incident->statut==='nouveau')>Nouveau</option>
                            <option value="en_cours" @selected($incident->statut==='en_cours')>En cours</option>
                            <option value="résolu"  @selected($incident->statut==='résolu')>Résolu</option>
                        </select>
                        <p id="statut-help" class="mt-2 text-xs text-gray-500 dark:text-gray-400">Le statut « fermé » n’est pas disponible ici.</p>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Priorité</label>
                        @php
                            $currentPriority = strtolower($incident->priority ?? ($incident->priorite ?? ''));
                            $currentPriority = str_replace(['é','É'],'e',$currentPriority);
                        @endphp
                        <select id="priority" class="w-full rounded border px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-white" aria-describedby="priority-help">
                            <option value="" @selected($currentPriority==='')>Non défini</option>
                            <option value="low"      @selected($currentPriority==='low')>Low</option>
                            <option value="medium"   @selected($currentPriority==='medium')>Medium</option>
                            <option value="high"     @selected($currentPriority==='high')>High</option>
                            <option value="critical" @selected($currentPriority==='critical')>Critical</option>
                        </select>
                        <p id="priority-help" class="text-xs text-gray-500 dark:text-gray-400 mt-2">(Recalcule le SLA en fonction de la priorité)</p>
                    </div>

                    <div>
                        <label for="to_resolveur_id" class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Réassigner (Escalader)</label>
                        <div class="flex gap-2">
                            <select id="to_resolveur_id" class="w-full rounded border px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-white" aria-describedby="escalade-help">
                                <option value="">— Sélectionner —</option>
                                @foreach($resolveurs as $r)
                                    <option value="{{ $r->id }}" @selected($incident->attribue_a == $r->id)>{{ $r->name }}</option>
                                @endforeach
                            </select>
                            <button id="escalateBtn" type="button" class="shrink-0 inline-flex items-center gap-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2" aria-live="polite" aria-busy="false">
                                <span id="escalateText">Escalader</span>
                                <svg id="escalateSpin" class="hidden animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </button>
                        </div>
                        <p id="escalade-help" class="text-xs text-gray-500 dark:text-gray-400 mt-2">Réassigne l’incident à un autre résolveur et enregistre l’historique.</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <a href="{{ route('resolveur.incidents.index') }}" class="rounded bg-gray-600 hover:bg-gray-700 text-white px-4 py-2">← Retour</a>

                    <button id="saveBtn" type="button" class="inline-flex items-center gap-2 rounded bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-5 py-2" aria-disabled="true" aria-live="polite" aria-busy="false">
                        <span id="saveText">Enregistrer les modifications</span>
                        <svg id="saveSpin" class="hidden animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6" role="region" aria-labelledby="comments-title">
                <h3 id="comments-title" class="font-semibold text-gray-800 dark:text-white mb-4">Commentaires</h3>

                <form method="POST" action="{{ route('resolveur.incidents.comment', $incident) }}" class="space-y-3" aria-describedby="comment-help">
                    @csrf
                    <label for="commentaire" class="sr-only">Commentaire</label>
                    <textarea id="commentaire" name="commentaire" rows="3" class="w-full rounded border dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="Ajouter un commentaire…" required aria-required="true"></textarea>
                    <p id="comment-help" class="text-xs text-gray-500 dark:text-gray-400">Le commentaire sera ajouté à l’historique de l’incident.</p>
                    <div class="flex items-center justify-end">
                        <button type="submit" class="rounded bg-green-600 hover:bg-green-700 text-white px-4 py-2">Publier</button>
                    </div>
                </form>

                @if(!empty($incident->commentaires) && $incident->commentaires->count())
                    <ul class="mt-6 space-y-3">
                        @foreach ($incident->commentaires as $commentaire)
                            <li class="border-t pt-3 dark:border-gray-700">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <strong class="text-gray-700 dark:text-gray-200">{{ $commentaire->auteur->name ?? '—' }}</strong>
                                    · <time datetime="{{ $commentaire->created_at->toIso8601String() }}">{{ $commentaire->created_at->format('d/m/Y H:i') }}</time>
                                </div>
                                <div class="text-gray-900 dark:text-gray-100">{{ $commentaire->commentaire }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <form id="statusForm" class="hidden" method="POST" action="{{ route('resolveur.incidents.status', $incident) }}">
        @csrf
        <input type="hidden" name="statut" aria-label="statut">
    </form>
    <form id="priorityForm" class="hidden" method="POST" action="{{ route('resolveur.incidents.priority', $incident) }}">
        @csrf
        <input type="hidden" name="priority" aria-label="priority">
    </form>
    <form id="escalateForm" class="hidden" method="POST" action="{{ route('resolveur.incidents.escalate', $incident) }}">
        @csrf
        <input type="hidden" name="to_resolveur_id" aria-label="to resolveur id">
    </form>

    <script>
        (function () {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const toast = document.getElementById('toast');
            const toastText = document.getElementById('toastText');
            const toastClose = document.getElementById('toastClose');
            const live = document.getElementById('screenreader-live');
            let toastTimer;
            function showToast(msg, ok=true) {
                toast.classList.remove('bg-red-600','dark:bg-red-500','hidden');
                toast.classList.add(ok ? 'bg-green-600' : 'bg-red-600');
                if (!ok) toast.classList.add('dark:bg-red-500');
                toast.setAttribute('aria-live','polite');
                toastText.textContent = msg;
                if (live) live.textContent = msg;
                toast.style.opacity = '1';
                clearTimeout(toastTimer);
                toastTimer = setTimeout(()=> {
                    toast.style.opacity = '0';
                    setTimeout(()=>toast.classList.add('hidden'), 250);
                }, 2500);
            }
            if (toastClose) toastClose.addEventListener('click', ()=>{ toast.classList.add('hidden'); });

            @if (session('success')) showToast(@json(session('success')), true); @endif
            @if($errors->any()) showToast(@json($errors->first()), false); @endif

            const statut   = document.getElementById('statut');
            const priority = document.getElementById('priority');
            const saveBtn  = document.getElementById('saveBtn');
            const saveText = document.getElementById('saveText');
            const saveSpin = document.getElementById('saveSpin');

            const escalateBtn   = document.getElementById('escalateBtn');
            const escalateText  = document.getElementById('escalateText');
            const escalateSpin  = document.getElementById('escalateSpin');
            const toResolveur   = document.getElementById('to_resolveur_id');

            const initial = { statut: statut.value, priority: priority.value };

            function updateDirty() {
                const changed = (statut.value !== initial.statut) || (priority.value !== initial.priority);
                saveBtn.disabled = !changed;
                saveBtn.setAttribute('aria-disabled', changed ? 'false' : 'true');
                return changed;
            }
            statut.addEventListener('change', updateDirty);
            priority.addEventListener('change', updateDirty);
            updateDirty();

            async function postForm(url, data) {
                return await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'text/html;charset=UTF-8' },
                    body: new URLSearchParams(data)
                });
            }

            saveBtn.addEventListener('click', async () => {
                if (!updateDirty()) return;
                if (['fermé','ferme'].includes((statut.value || '').toLowerCase())) {
                    showToast('Le statut "fermé" ne peut pas être sélectionné par le résolveur', false);
                    return;
                }
                saveBtn.disabled = true;
                saveBtn.setAttribute('aria-busy','true');
                saveText.textContent = 'Enregistrement…';
                saveSpin.classList.remove('hidden');
                try {
                    if (statut.value !== initial.statut) {
                        const res1 = await postForm(document.getElementById('statusForm').action, { statut: statut.value });
                        if (!res1.ok) throw new Error('Erreur en mettant à jour le statut');
                        initial.statut = statut.value;
                    }
                    if (priority.value !== initial.priority) {
                        const res2 = await postForm(document.getElementById('priorityForm').action, { priority: priority.value });
                        if (!res2.ok) throw new Error('Erreur en enregistrant la priorité');
                        initial.priority = priority.value;
                    }
                    showToast('Modifications enregistrées ✅', true);
                } catch (e) {
                    showToast(e.message || 'Erreur lors de la sauvegarde', false);
                } finally {
                    saveText.textContent = 'Enregistrer les modifications';
                    saveSpin.classList.add('hidden');
                    saveBtn.removeAttribute('aria-busy');
                    updateDirty();
                }
            });

            escalateBtn.addEventListener('click', async () => {
                const dest = toResolveur.value;
                if (!dest) { showToast('Sélectionnez un résolveur', false); return; }
                if (!confirm('Réassigner cet incident ?')) return;
                escalateBtn.disabled = true;
                escalateBtn.setAttribute('aria-busy','true');
                escalateSpin.classList.remove('hidden');
                try {
                    const res = await postForm(document.getElementById('escalateForm').action, { to_resolveur_id: dest });
                    if (!res.ok) throw new Error('Erreur lors de la réassignation');
                    showToast('Incident réassigné ✅', true);
                } catch (e) {
                    showToast(e.message || 'Erreur lors de la réassignation', false);
                } finally {
                    escalateBtn.disabled = false;
                    escalateBtn.removeAttribute('aria-busy');
                    escalateSpin.classList.add('hidden');
                }
            });
        })();
    </script>
</x-app-layout>
