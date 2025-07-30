<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Liste des incidents
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('utilisateur.incidents.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded">
            + Nouvel incident
        </a>

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
                @foreach ($incidents as $incident)
                    <tr class="border-b">
                        <td class="px-6 py-4">{{ $incident->titre }}</td>
                        <td class="px-6 py-4">{{ ucfirst($incident->statut) }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('utilisateur.incidents.edit', $incident) }}" class="text-blue-500 mr-2">Modifier</a>

                            <form action="{{ route('utilisateur.incidents.destroy', $incident) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cet incident ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            {{ $incidents->links() }}
        </div>
    </div>
</x-app-layout>
