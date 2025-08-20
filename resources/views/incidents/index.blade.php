<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Liste des incidents
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('incidents.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded" aria-label="Créer un nouvel incident">
            ➕ Nouvel incident
        </a>

        @if (session('success'))
            <div class="mb-4 text-green-600" role="status" aria-live="polite" aria-atomic="true">
                {{ session('success') }}
            </div>
        @endif

        <div role="region" aria-labelledby="incidents-title">
            <h3 id="incidents-title" class="sr-only">Table des incidents</h3>
            <table class="min-w-full bg-white border">
                <caption class="sr-only">Liste des incidents</caption>
                <thead>
                    <tr>
                        <th scope="col" class="border px-4 py-2">Titre</th>
                        <th scope="col" class="border px-4 py-2">Statut</th>
                        <th scope="col" class="border px-4 py-2">Créé par</th>
                        <th scope="col" class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incidents as $incident)
                        <tr>
                            <th scope="row" class="border px-4 py-2 font-medium">{{ $incident->titre }}</th>
                            <td class="border px-4 py-2">{{ $incident->statut }}</td>
                            <td class="border px-4 py-2">{{ $incident->utilisateur->name ?? 'N/A' }}</td>
                            <td class="border px-4 py-2 space-x-2">
                                <a href="{{ route('incidents.edit', $incident) }}" class="text-blue-500" aria-label="Modifier l’incident {{ $incident->titre }}">✏️ Modifier</a>
                                <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cet incident ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500" aria-label="Supprimer l’incident {{ $incident->titre }}">🗑️ Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <nav class="mt-4" aria-label="Pagination">
            {{ $incidents->links() }}
        </nav>
    </div>
</x-app-layout>
