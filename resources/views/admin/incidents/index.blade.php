<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Gestion des incidents (Admin)
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        @if (session('success'))
            <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form id="filtersForm" method="GET" class="mb-5 space-y-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-sm text-gray-600 dark:text-gray-300">Statut</label>
                    @php $st = request('statut'); @endphp
                    <select name="statut"
                            class="auto-submit w-full rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                        <option value="">Tous</option>
                        <option value="nouveau"  {{ $st==='nouveau'  ? 'selected':'' }}>Nouveau</option>
                        <option value="en_cours" {{ $st==='en_cours' ? 'selected':'' }}>En cours</option>
                        <option value="résolu"   {{ $st==='résolu'   ? 'selected':'' }}>Résolu</option>
                        <option value="fermé"    {{ $st==='fermé'    ? 'selected':'' }}>Fermé</option>
                    </select>
                </div>

                @php $pr = strtolower(request('priority', request('priorite',''))); @endphp
                <div class="lg:col-span-7">
                    <label class="block text-sm text-gray-600 dark:text-gray-300">Priorité</label>
                    @php
                        $opts = ['' => 'toutes', 'low'=>'faible', 'medium'=>'moyenne', 'high'=>'haute', 'critical'=>'critique'];
                        $badge = [
                            ''         => 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-200',
                            'low'      => 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-200',
                            'medium'   => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                            'high'     => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-300',
                            'critical' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
                        ];
                        $label = fn($raw) => $raw === 'toutes' ? 'Toutes' : ucfirst($raw);
                    @endphp
                    <div class="mt-1 flex flex-wrap gap-2">
                        @foreach($opts as $val => $raw)
                            @php
                                $active = ($pr===$val) || ($val==='' && $pr==='');
                                $classes = $badge[$val].' '.($active ? 'ring-2 ring-offset-1 ring-blue-500 dark:ring-blue-400' : '');
                            @endphp
                            <button type="button"
                                    class="priority-pill inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $classes }}"
                                    data-value="{{ $val }}">
                                {{ $label($raw) }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="priority"  id="priorityValue"  value="{{ e($pr) }}">
                    <input type="hidden" name="priorite"  id="prioriteMirror" value="{{ e($pr) }}">
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm text-gray-600 dark:text-gray-300">Du</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}"
                           class="auto-submit w-full rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-sm text-gray-600 dark:text-gray-300">Au</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}"
                           class="auto-submit w-full rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm text-gray-600 dark:text-gray-300">Page</label>
                    <select name="per_page" class="auto-submit w-full rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                        @foreach([10,15,25,50] as $pp)
                            <option value="{{ $pp }}" @selected((int)request('per_page',10)===$pp)>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <input type="search" name="q" id="qSearch" value="{{ request('q') }}"
                       placeholder="🔎 Rechercher titre / code / description…"
                       class="w-full md:flex-1 md:min-w-[280px] rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">

                @php $sort = request('sort','prio'); $dir = request('dir','desc'); @endphp
                <label class="text-sm text-gray-600 dark:text-gray-300">Trier</label>
                <select name="sort" class="auto-submit rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                    <option value="prio" {{ $sort==='prio' ? 'selected':'' }}>Priorité</option>
                    <option value="date" {{ $sort==='date' ? 'selected':'' }}>Date</option>
                </select>
                <select name="dir" class="auto-submit rounded border px-3 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                    <option value="asc"  {{ $dir==='asc' ? 'selected':'' }}>Asc</option>
                    <option value="desc" {{ $dir==='desc'? 'selected':'' }}>Desc</option>
                </select>

                <a href="{{ url()->current() }}" class="rounded bg-gray-600 hover:bg-gray-700 text-white px-4 py-2">
                    Réinitialiser
                </a>

                @if(Route::has('admin.incidents.export.csv'))
                    <a href="{{ route('admin.incidents.export.csv', request()->query()) }}"
                       class="ml-auto inline-flex items-center gap-2 rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                        ⬇ Exporter en CSV
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded border bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-gray-900 dark:text-gray-100">
                    <thead>
                        <tr class="border-b bg-gray-50 dark:border-gray-800 dark:bg-gray-800/60">
                            <th class="p-3 text-left font-semibold">Code</th>
                            <th class="p-3 text-left font-semibold">Titre</th>
                            <th class="p-3 text-left font-semibold">Priorité</th>
                            <th class="p-3 text-left font-semibold">Statut</th>
                            <th class="p-3 text-left font-semibold">SLA</th>
                            <th class="p-3 text-left font-semibold">Assigné à</th>
                            <th class="p-3 text-left font-semibold">Créé par</th>
                            <th class="p-3 text-left font-semibold">Créé</th>
                            <th class="p-3 text-left font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $i)
                            @php
                                $code  = $i->public_id ?? ('INC-'.str_pad($i->id, 4, '0', STR_PAD_LEFT));
                                $prio  = strtolower($i->priority ?? $i->priorite ?? '');
                                $prioLabelMap = ['low'=>'Faible','medium'=>'Moyenne','high'=>'Haute','critical'=>'Critique'];
                                $prioLabel = $prioLabelMap[$prio] ?? '—';
                                $prioBadge = [
                                    'low'      => 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-200',
                                    'medium'   => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                    'high'     => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-300',
                                    'critical' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
                                ][$prio] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-200';

                                $statusKey   = $i->statut ?? '';
                                $statusLabel = \Illuminate\Support\Str::of($statusKey)->replace(['_','-'],' ')->title();

                                $hasSla = !empty($i->sla_due_at ?? null);
                                $isClosedForSla = in_array($statusKey, ['résolu','resolu','fermé','ferme']);

                                $slaTxt = '—'; $slaClass='';
                                if ($hasSla && !$isClosedForSla) {
                                    $deadline = \Illuminate\Support\Carbon::parse($i->sla_due_at);
                                    if (now()->lessThanOrEqualTo($deadline)) {
                                        $slaTxt='OK'; $slaClass='bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300';
                                    } else {
                                        $slaTxt='Breach'; $slaClass='bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300';
                                    }
                                }
                            @endphp
                            <tr class="border-b last:border-0 dark:border-gray-800">
                                <td class="p-3 align-middle">{{ $code }}</td>
                                <td class="p-3 align-middle">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $i->titre ?? \Illuminate\Support\Str::limit($i->description, 80) }}</span>
                                        @if($i->is_reopened)
                                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-500/20 dark:text-red-300">
                                                Réouvert
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3 align-middle">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $prioBadge }}">
                                        {{ $prioLabel }}
                                    </span>
                                </td>
                                <td class="p-3 align-middle">{{ $statusLabel }}</td>
                                <td class="p-3 align-middle">
                                    @if($hasSla && !$isClosedForSla)
                                        <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-semibold {{ $slaClass }}">{{ $slaTxt }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="p-3 align-middle">{{ optional($i->assignedUser)->name ?? '—' }}</td>
                                <td class="p-3 align-middle">{{ optional($i->utilisateur)->name ?? '—' }}</td>
                                <td class="p-3 align-middle">{{ optional($i->created_at)->format('Y-m-d') }}</td>
                                <td class="p-3 align-middle">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.incidents.show', $i) }}"
                                           class="h-8 w-8 transform transition hover:scale-110" title="Voir" aria-label="Voir">
                                            <img src="{{ asset('eye.webp') }}" alt="Voir" class="h-full w-full rounded object-contain">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-4 text-center text-gray-500">Aucun incident</td>
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
            document.querySelectorAll('.auto-submit').forEach(el=>{ el.addEventListener('change', ()=> form.submit()); });
            const q = document.getElementById('qSearch'); if (q) q.addEventListener('input', debounce(()=> form.submit(), 450));
            const priorityValue  = document.getElementById('priorityValue');
            const prioriteMirror = document.getElementById('prioriteMirror');
            document.querySelectorAll('.priority-pill').forEach(btn=>{
                btn.addEventListener('click', ()=>{
                    const val = btn.dataset.value || '';
                    priorityValue.value  = val;
                    prioriteMirror.value = val;
                    form.submit();
                });
            });
            const from = document.getElementById('from');
            const to   = document.getElementById('to');
            function syncMinMax(){ if (from && to){ to.min = from.value || ''; from.max = to.value || ''; } }
            function ensureOrderAndSubmit(changed){
                if (!from || !to) return;
                if (from.value && to.value && to.value < from.value){
                    if (changed === 'from')  to.value = from.value; else from.value = to.value;
                }
                syncMinMax(); form.submit();
            }
            if (from){ from.addEventListener('change', ()=> ensureOrderAndSubmit('from')); }
            if (to){   to.addEventListener('change',   ()=> ensureOrderAndSubmit('to'));   }
            syncMinMax();
        })();
    </script>
</x-app-layout>
