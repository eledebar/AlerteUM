<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Liste des incidents
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('incidents.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded">
            ➕ Nouvel incident
        </a>

        @if (session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Titre</th>
                    <th class="border px-4 py-2">Statut</th>
                    <th class="border px-4 py-2">Créé par</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($incidents as $incident)
                    <tr>
                        <td class="border px-4 py-2">{{ $incident->titre }}</td>
                        <td class="border px-4 py-2">{{ $incident->statut }}</td>
                        <td class="border px-4 py-2">{{ $incident->utilisateur->name ?? 'N/A' }}</td>
                        <td class="border px-4 py-2 space-x-2">
                            <a href="{{ route('incidents.edit', $incident) }}" class="text-blue-500">✏️ Modifier</a>
                            <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cet incident ?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $incidents->links() }}
        </div>
    </div>
</x-app-layout>
