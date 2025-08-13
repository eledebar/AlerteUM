@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Gestion des incidents (Admin)</h2>
@endsection

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">

    {{-- Filtros --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
        <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Statut</label>
            <select name="statut" class="w-full rounded border dark:bg-gray-800 dark:text-white">
                @php $st = request('statut'); @endphp
                <option value="">Tous</option>
                <option value="nouveau"  {{ $st==='nouveau' ? 'selected':'' }}>Nouveau</option>
                <option value="en_cours" {{ $st==='en_cours'? 'selected':'' }}>En_cours</option>
                <option value="résolu"   {{ $st==='résolu'  ? 'selected':'' }}>Résolu</option>
                <option value="fermé"    {{ $st==='fermé'   ? 'selected':'' }}>Fermé</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Priorité</label>
            <select name="priorite" class="w-full rounded border dark:bg-gray-800 dark:text-white">
                @php $pr = request('priorite'); @endphp
                <option value="">Toutes</option>
                <option value="Low"    {{ $pr==='Low'    ? 'selected':'' }}>Low</option>
                <option value="Medium" {{ $pr==='Medium' ? 'selected':'' }}>Medium</option>
                <option value="High"   {{ $pr==='High'   ? 'selected':'' }}>High</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Du</label>
            <input type="date" name="from" value="{{ request('from') }}"
                   class="w-full rounded border dark:bg-gray-800 dark:text-white">
        </div>

        <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Au</label>
            <input type="date" name="to" value="{{ request('to') }}"
                   class="w-full rounded border dark:bg-gray-800 dark:text-white">
        </div>

        <div class="lg:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300">Chercher (code/titre)</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="INC-… / titre"
                   class="w-full rounded border dark:bg-gray-800 dark:text-white">
        </div>

        <div class="lg:col-span-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-300">Trier</label>
                <select name="sort" class="rounded border dark:bg-gray-800 dark:text-white">
                    @php $sort = request('sort','date'); @endphp
                    <option value="date"  {{ $sort==='date'  ? 'selected':'' }}>Fecha</option>
                    <option value="prio"  {{ $sort==='prio'  ? 'selected':'' }}>Priorité</option>
                </select>
                <select name="dir" class="rounded border dark:bg-gray-800 dark:text-white">
                    @php $dir = request('dir','desc'); @endphp
                    <option value="asc"  {{ $dir==='asc' ? 'selected':'' }}>Asc</option>
                    <option value="desc" {{ $dir==='desc'? 'selected':'' }}>Desc</option>
                </select>
            </div>

            <a href="{{ route('admin.incidents.export', request()->query()) }}"
               class="inline-flex items-center gap-2 rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                <span>⬇</span> Exporter en CSV
            </a>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded border bg-white dark:bg-gray-900 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 dark:bg-gray-800/60 dark:border-gray-800">
                        <th class="p-3 text-left">Code</th>
                        <th class="p-3 text-left">Titre</th>
                        <th class="p-3 text-left">Priorité</th>
                        <th class="p-3 text-left">Statut</th>
                        <th class="p-3 text-left">SLA</th>
                        <th class="p-3 text-left">Assigné</th>
                        <th class="p-3 text-left">Créé par</th>
                        <th class="p-3 text-left">Créé</th>
                        <th class="p-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidents as $i)
                        <tr class="border-b last:border-0 dark:border-gray-800">
                            <td class="p-3 align-middle">{{ $i->public_id ?? ('INC-' . str_pad($i->id, 4, '0', STR_PAD_LEFT)) }}</td>
                            <td class="p-3 align-middle">{{ $i->titre ?? $i->title ?? $i->description }}</td>
                            <td class="p-3 align-middle">{{ $i->priorite ?? $i->priorité ?? $i->prioridad ?? $i->priority ?? '—' }}</td>
                            <td class="p-3 align-middle">{{ $i->statut }}</td>
                            <td class="p-3 align-middle">{{ $i->sla ?? '—' }}</td>
                            <td class="p-3 align-middle">{{ optional($i->assignedUser ?? null)->name ?? '—' }}</td>
                            <td class="p-3 align-middle">{{ optional($i->creator ?? null)->name ?? '—' }}</td>
                            <td class="p-3 align-middle">{{ optional($i->created_at)->format('Y-m-d') }}</td>
                            <td class="p-3 align-middle">
                                {{-- Icono VER --}}
                                <a href="{{ route('admin.incidents.show', $i) }}"
                                   class="inline-block h-7 w-7 transform hover:scale-110"
                                   title="Ouvrir" aria-label="Ouvrir">
                                    <img src="{{ asset('eye.webp') }}" alt="Voir" class="h-full w-full object-contain">
                                </a>
                                {{-- (Admin ya no “escala” aquí) --}}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="p-4 text-center text-gray-500">Aucun incident</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t p-3 dark:border-gray-800">
            {{ $incidents->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection
