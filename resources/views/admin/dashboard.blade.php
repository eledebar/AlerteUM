@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Administration</h2>
@endsection

@section('content')
<div class="max-w-7xl mx-auto p-6">

    {{-- Tarjetas: si $stats incluye FERME/FERMÉ lo mostramos --}}
    @php
        $total   = $stats['TOTAL']   ?? ($stats['total']   ?? null);
        $nouveau = $stats['NOUVEAU'] ?? ($stats['nouveau'] ?? null);
        $encours = $stats['EN_COURS']?? ($stats['en_cours']?? null);
        $resolu  = $stats['RÉSOLU']  ?? ($stats['resolu']  ?? ($stats['résolu'] ?? null));
        $ferme   = $stats['FERMÉ']   ?? ($stats['FERME']   ?? ($stats['ferme'] ?? null));
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @if(!is_null($total))
            <div class="p-6 bg-white dark:bg-gray-800 border rounded-lg shadow">
                <div class="text-sm uppercase text-gray-500 dark:text-gray-300">TOTAL</div>
                <div class="mt-1 text-3xl font-semibold">{{ $total }}</div>
            </div>
        @endif
        @if(!is_null($nouveau))
            <div class="p-6 bg-yellow-50 dark:bg-yellow-300/20 border rounded-lg shadow">
                <div class="text-sm uppercase text-gray-500">NOUVEAU</div>
                <div class="mt-1 text-3xl font-semibold text-yellow-600">{{ $nouveau }}</div>
            </div>
        @endif
        @if(!is_null($encours))
            <div class="p-6 bg-blue-50 dark:bg-blue-300/20 border rounded-lg shadow">
                <div class="text-sm uppercase text-gray-500">EN_COURS</div>
                <div class="mt-1 text-3xl font-semibold text-blue-600">{{ $encours }}</div>
            </div>
        @endif
        @if(!is_null($resolu))
            <div class="p-6 bg-green-50 dark:bg-green-300/20 border rounded-lg shadow">
                <div class="text-sm uppercase text-gray-500">RÉSOLU</div>
                <div class="mt-1 text-3xl font-semibold text-green-600">{{ $resolu }}</div>
            </div>
        @endif
        @if(!is_null($ferme))
            <div class="p-6 bg-gray-100 dark:bg-gray-700/40 border rounded-lg shadow">
                <div class="text-sm uppercase text-gray-500 dark:text-gray-300">FERMÉ</div>
                <div class="mt-1 text-3xl font-semibold text-gray-800 dark:text-gray-200">{{ $ferme }}</div>
            </div>
        @endif
    </div>

    <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold">Derniers incidents</h2>
        <div class="space-x-4">
            <a class="text-blue-600" href="{{ route('admin.incidents.index') }}">Tous les incidents →</a>
            <a class="text-blue-600" href="{{ route('admin.resolveurs.index') }}">Gérer les résolveurs →</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 border rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-3">ID</th>
                    <th class="text-left p-3">Public ID</th>
                    <th class="text-left p-3">Statut</th>
                    <th class="text-left p-3">Dernier événement</th>
                    <th class="text-left p-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($incidents as $i)
                    <tr class="border-b">
                        <td class="p-3">#{{ $i->id }}</td>
                        <td class="p-3">{{ $i->public_id }}</td>
                        <td class="p-3">{{ $i->statut }}</td>
                        <td class="p-3">{{ optional($i->logs->first())->created_at }}</td>
                        <td class="p-3"><a class="text-blue-600" href="{{ route('admin.incidents.show', $i) }}">Voir</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
