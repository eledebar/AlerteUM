<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mes incidents
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('utilisateur.incidents.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded">
            + Nouvel incident
        </a>

        <!-- Filtro par statut -->
        <form method="GET" class="mb-6">
            <label for="statut" class="mr-2 font-medium">Filtrer par statut:</label>
            <select name="statut" id="statut" onchange="this.form.submit()" class="border px-3 py-1">
                <option value="">-- Tous --</option>
                <option value="nouveau" {{ request('statut') == 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="résolu" {{ request('statut') == 'résolu' ? 'selected' : '' }}>Résolu</option>
            </select>
        </form>

        @if (session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white shadow-md rounded">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left font-bold">Titre</th>
                    <th class="px-6 py-3 text-left font-bold">Statut</th>
                    <th class="px-6 py-3 text-left font-bold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($incidents as $incident)
                    <tr class="border-b">
                        <td class="px-6 py-4">{{ $incident->titre }}</td>
                        <td class="px-6 py-4 capitalize">{{ $incident->statut }}</td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('utilisateur.incidents.show', $incident) }}" class="text-gray-700">👁️ Voir</a>

                            @if($incident->statut === 'nouveau')
                                <a href="{{ route('utilisateur.incidents.edit', $incident) }}" class="text-blue-500">✏️ Modifier</a>

                                <form action="{{ route('utilisateur.incidents.destroy', $incident) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet incident ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">🗑️ Supprimer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucun incident trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6">
            {{ $incidents->appends(['statut' => request('statut')])->links() }}
        </div>
    </div>
</x-app-layout>
