@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Incident {{ $incident->public_id }}</h1>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div class="bg-white dark:bg-gray-900 border rounded p-4 space-y-2">
      <div><span class="font-semibold">Titre:</span> {{ $incident->titre }}</div>
      <div><span class="font-semibold">Statut:</span> {{ ucfirst($incident->statut) }}</div>
      <div><span class="font-semibold">Priorité:</span> {{ ucfirst($incident->priority ?? 'n/a') }}</div>
      <div><span class="font-semibold">Escalation:</span> {{ $incident->escalation_level ?? 0 }}</div>
      <div><span class="font-semibold">Assigné à:</span> {{ optional($incident->assignedUser)->name ?? 'Non assigné' }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 border rounded p-4">
      <form method="POST" action="{{ route('resolveur.incidents.status', $incident) }}" class="space-y-2">
        @csrf
        <label class="block text-sm font-semibold">Changer statut</label>
        <select name="statut" class="border rounded p-2 w-full">
          @foreach (['nouveau','en_cours','résolu','fermé'] as $s)
            <option value="{{ $s }}" @selected($incident->statut===$s)>{{ $s }}</option>
          @endforeach
        </select>
        <button class="px-3 py-2 bg-blue-600 text-white rounded">Mettre à jour</button>
      </form>
    </div>
  </div>

  <div class="bg-white dark:bg-gray-900 border rounded p-4">
    <h2 class="text-lg font-bold mb-3">Traza (logs)</h2>
    @foreach($incident->logs as $log)
      <div class="border-l-4 pl-3 mb-2 border-blue-500">
        <div class="text-xs text-gray-500">{{ optional($log->created_at)->format('Y-m-d H:i') }}</div>
        <div class="font-semibold">{{ $log->action ?? 'Update' }}</div>
        @if(!empty($log->details))
          <div class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $log->details }}</div>
        @endif
      </div>
    @endforeach
    @if($incident->logs->isEmpty())
      <div class="text-sm text-gray-500">Sin eventos en la traza.</div>
    @endif
  </div>
</div>
@endsection
