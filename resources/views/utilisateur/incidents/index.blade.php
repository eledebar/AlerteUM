@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">Mes incidents</h2>
@endsection

@section('content')
<style>
    input[type="date"]::-webkit-date-and-time-value{ text-align:left; }
    input[type="date"]::-webkit-datetime-edit-text{ padding:0 2px; }
    .dark input[type="date"]::-webkit-calendar-picker-indicator{ filter:invert(1); opacity:.9; }
</style>

<div class="max-w-7xl mx-auto p-6" x-data>
    @if (session('success'))
        <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <a href="{{ route('utilisateur.incidents.categories') }}"
           class="inline-flex items-center gap-2 rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus-visible:focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-indigo-600"
           aria-label="Signaler un nouvel incident">
            + Signaler un incident
        </a>

        <a href="{{ route('utilisateur.incidents.export.csv', request()->query()) }}"
           class="ml-auto inline-flex items-center gap-2 rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700 focus-visible:focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-green-600"
           aria-label="Exporter la liste en CSV">
            ⬇ Exporter en CSV
        </a>
    </div>

    <form id="filtersForm" method="GET" class="mb-5 space-y-4" role="search" aria-label="Filtres de recherche">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">

            <div class="lg:col-span-2">
                @php $st = request('statut'); @endphp
                <label for="statut" class="block text-sm text-gray-700 dark:text-gray-200">Statut</label>
                <select id="statut" name="statut"
                        class="auto-submit w-full rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                    <option value="">Tous</option>
                    <option value="nouveau"  {{ $st==='nouveau'  ? 'selected':'' }}>
                        {{ config('itil.labels.status.nouveau','Nouveau') }}
                    </option>
                    <option value="en_cours" {{ $st==='en_cours' ? 'selected':'' }}>
                        {{ config('itil.labels.status.en_cours','En cours') }}
                    </option>
                    <option value="résolu"   {{ $st==='résolu'   ? 'selected':'' }}>
                        {{ config('itil.labels.status.résolu','Résolu') }}
                    </option>
                    <option value="fermé"    {{ $st==='fermé'    ? 'selected':'' }}>
                        {{ config('itil.labels.status.fermé','Fermé') }}
                    </option>
                </select>
            </div>

            @php $pr = strtolower(request('priority', request('priorite', ''))); @endphp
            <div class="lg:col-span-4">
                <fieldset>
                    <legend id="prio-label" class="block text-sm text-gray-700 dark:text-gray-200">Priorité</legend>

                    @php
                        $opts = ['' => 'all', 'low'=>'low', 'medium'=>'medium', 'high'=>'high', 'critical'=>'critical'];
                        $label = fn($val) => $val === 'all' ? 'Toutes' : config('itil.labels.priority.'.$val, ucfirst($val));
                        $base = 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold border transition focus-within:ring-2 focus-within:ring-indigo-600';
                        $map = [
                            ''         => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700/40 dark:text-gray-100 dark:border-gray-600',
                            'low'      => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-500/20 dark:text-gray-100 dark:border-gray-600',
                            'medium'   => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-500/20 dark:text-blue-200 dark:border-blue-400',
                            'high'     => 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-500/20 dark:text-orange-200 dark:border-orange-400',
                            'critical' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-500/20 dark:text-red-200 dark:border-red-400',
                        ];
                    @endphp

                    <div class="mt-1 flex flex-wrap gap-2" role="radiogroup" aria-labelledby="prio-label">
                        @foreach($opts as $val => $raw)
                            @php
                                $active = ($pr===$val) || ($val==='' && $pr==='');
                                $cls = ($map[$val] ?? $map['']) . ' ' . ($active ? 'ring-2 ring-offset-1 ring-blue-600 dark:ring-blue-400' : '');
                                $id = 'prio_'.($val===''?'all':$val);
                            @endphp
                            <label for="{{ $id }}" class="{{ $base }} {{ $cls }}">
                                <input id="{{ $id }}" type="radio" name="priority" value="{{ $val }}"
                                       class="sr-only auto-submit"
                                       {{ $active ? 'checked' : '' }}>
                                <span>{{ $label($raw) }}</span>
                            </label>
                        @endforeach
                    </div>

                    <input type="hidden" name="priorite" id="prioriteMirror" value="{{ e($pr) }}" aria-label="priorite">
                </fieldset>
            </div>

            <div class="lg:col-span-3">
                <label for="from" class="block text-sm text-gray-700 dark:text-gray-200">Du</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}"
                       class="auto-submit w-full rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
            </div>

            <div class="lg:col-span-3">
                <label for="to" class="block text-sm text-gray-700 dark:text-gray-200">Au</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}"
                       class="auto-submit w-full rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="w-full md:flex-1 md:min-w-[280px]">
                <label for="qSearch" class="sr-only">Recherche</label>
                <input type="search" name="q" id="qSearch" value="{{ request('q') }}" placeholder="Rechercher titre, code…"
                       class="w-full rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700"
                       aria-label="Rechercher par titre ou code">
            </div>

            @php $sort = request('sort','prio'); $dir = request('dir','desc'); @endphp
            <div class="flex items-end gap-2">
                <div>
                    <label for="sortBy" class="block text-sm text-gray-700 dark:text-gray-200">Trier par</label>
                    <select id="sortBy" name="sort" class="auto-submit rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                        <option value="prio" {{ $sort==='prio' ? 'selected':'' }}>Priorité</option>
                        <option value="date" {{ $sort==='date' ? 'selected':'' }}>Date</option>
                    </select>
                </div>
                <div>
                    <label for="sortDir" class="block text-sm text-gray-700 dark:text-gray-200">Ordre</label>
                    <select id="sortDir" name="dir" class="auto-submit rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                        <option value="asc"  {{ $dir==='asc' ? 'selected':'' }}>Asc</option>
                        <option value="desc" {{ $dir==='desc'? 'selected':'' }}>Desc</option>
                    </select>
                </div>
            </div>

            <a href="{{ url()->current() }}" class="rounded bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 focus-visible:focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-gray-700" aria-label="Réinitialiser les filtres">Réinitialiser</a>
        </div>
    </form>

    <div class="overflow-hidden rounded border bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm text-gray-900 dark:text-gray-100">
                <caption class="sr-only">Liste de mes incidents</caption>
                <thead>
                    <tr class="border-b bg-gray-50 dark:border-gray-800 dark:bg-gray-800/60">
                        <th scope="col" class="p-3 text-left font-semibold">Code</th>
                        <th scope="col" class="p-3 text-left font-semibold">Titre</th>
                        <th scope="col" class="p-3 text-left font-semibold">Priorité</th>
                        <th scope="col" class="p-3 text-left font-semibold">Statut</th>
                        <th scope="col" class="p-3 text-left font-semibold">Créé</th>
                        <th scope="col" class="p-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidents as $i)
                        @php
                            $public = $i->public_id ?? ('INC-' . str_pad($i->id, 4, '0', STR_PAD_LEFT));
                            $prioKey = strtolower($i->priority ?? $i->priorite ?? '');
                            $prioKey = str_replace(['é','É'],'e',$prioKey);
                            $prioLabel = $prioKey ? config('itil.labels.priority.'.$prioKey, ucfirst($prioKey)) : '—';
                            $prioBadge = [
                                'low'      => 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-100',
                                'medium'   => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-200',
                                'high'     => 'bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-200',
                                'critical' => 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-200',
                            ][$prioKey] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-100';
                            $statusKey   = $i->statut ?? '';
                            $statusLabel = config('itil.labels.status.'.$statusKey) ?: \Illuminate\Support\Str::of($statusKey)->replace(['_','-'],' ')->title();
                            $isOwner   = auth()->id() === ($i->utilisateur_id ?? $i->user_id ?? null);
                            $canEdit   = $isOwner && $statusKey === 'nouveau';
                        @endphp
                        <tr class="border-b last:border-0 dark:border-gray-800">
                            <td class="p-3 align-middle">{{ $public }}</td>
                            <td class="p-3 align-middle">{{ $i->titre ?? $i->title ?? \Illuminate\Support\Str::limit($i->description, 80) }}</td>
                            <td class="p-3 align-middle">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $prioBadge }}">
                                    {{ $prioLabel }}
                                </span>
                            </td>
                            <td class="p-3 align-middle">{{ $statusLabel }}</td>
                            <td class="p-3 align-middle">{{ optional($i->created_at)->format('Y-m-d') }}</td>
                            <td class="p-3 align-middle">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('utilisateur.incidents.show', $i) }}"
                                       class="h-10 w-10 transform transition hover:scale-110 focus-visible:focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-indigo-600 rounded-md"
                                       title="Voir" aria-label="Voir l’incident {{ $public }}">
                                        <img src="{{ asset('eye.webp') }}" alt="Voir" class="h-full w-full rounded object-contain">
                                    </a>

                                    @if($canEdit)
                                        <a href="{{ route('utilisateur.incidents.edit', $i) }}"
                                           class="h-10 w-10 transform transition hover:scale-110 focus-visible:focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-indigo-600 rounded-md"
                                           title="Éditer" aria-label="Éditer l’incident {{ $public }}">
                                            <img src="{{ asset('edit.webp') }}" alt="Éditer" class="h-full w-full rounded object-contain">
                                        </a>

                                        <form method="POST" action="{{ route('utilisateur.incidents.destroy', $i) }}"
                                              class="h-10 w-10"
                                              aria-label="Supprimer l’incident {{ $public }}">
                                            @csrf @method('DELETE')
                                            <button class="h-10 w-10 transform transition hover:scale-110 focus-visible:focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-red-600 rounded-md"
                                                    title="Supprimer" aria-label="Supprimer l’incident {{ $public }}"
                                                    onclick="return confirm('Supprimer cet incident ?')">
                                                <img src="{{ asset('delete.webp') }}" alt="Supprimer" class="h-full w-full rounded object-contain">
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-600 dark:text-gray-300">Aucun incident</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t p-3 dark:border-gray-800">
            {{ $incidents->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>

<script>
(function(){
    const form = document.getElementById('filtersForm');

    const debounce = (fn, ms=400) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms);} };

    document.querySelectorAll('.auto-submit').forEach(el=>{
        el.addEventListener('change', ()=>{
            if (el.name === 'priority') {
                const mirror = document.getElementById('prioriteMirror');
                if (mirror) mirror.value = el.value || '';
            }
            form.submit();
        });
    });

    const q = document.getElementById('qSearch');
    if (q) q.addEventListener('input', debounce(()=> form.submit(), 450));

    const from = document.getElementById('from');
    const to   = document.getElementById('to');

    function syncMinMax(){
        if (from && to){
            to.min   = from.value || '';
            from.max = to.value   || '';
        }
    }
    function ensureOrderAndSubmit(changed){
        if (!from || !to) return;
        if (from.value && to.value && to.value < from.value){
            if (changed === 'from')  to.value = from.value;
            else                     from.value = to.value;
        }
        syncMinMax();
        form.submit();
    }
    if (from){ from.addEventListener('change', ()=> ensureOrderAndSubmit('from')); }
    if (to){   to.addEventListener('change',   ()=> ensureOrderAndSubmit('to'));   }
    syncMinMax();
})();
</script>
@endsection
