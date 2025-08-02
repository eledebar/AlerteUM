<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Gestion des incidents
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex flex-wrap gap-4">
            <a href="{{ route('admin.incidents.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Nouvel incident
            </a>

            <a href="{{ route('admin.incidents.export.csv', request()->only(['statut', 'assigne_a_moi'])) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                ⬇️ Exporter en CSV
            </a>
        </div>

        <form method="GET" class="mb-6 flex flex-wrap items-center gap-4">
            <div>
                <label for="statut" class="mr-2 font-medium text-gray-800 dark:text-gray-200">Filtrer par statut:</label>
                <select name="statut" id="statut" onchange="this.form.submit()"
                        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-1 rounded">
                    <option value="">Tous</option>
                    <option value="nouveau" {{ request('statut') == 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="résolu" {{ request('statut') == 'résolu' ? 'selected' : '' }}>Résolu</option>
                </select>
            </div>

            <div>
                <label for="assigne_a_moi" class="mr-2 font-medium text-gray-800 dark:text-gray-200">Mes incidents assignés:</label>
                <input type="checkbox" name="assigne_a_moi" id="assigne_a_moi" value="1"
                       onchange="this.form.submit()"
                       {{ request()->boolean('assigne_a_moi') ? 'checked' : '' }}>
            </div>
        </form>

        <div class="mb-6">
            <input type="text" id="searchInput" placeholder="🔍 Rechercher par titre, statut ou utilisateur..."
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded">
        </div>

        @if (session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded text-gray-900 dark:text-gray-100" id="incidentTable">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left font-bold">Titre</th>
                    <th class="px-6 py-3 text-left font-bold">Statut</th>
                    <th class="px-6 py-3 text-left font-bold">Créé par</th>
                    <th class="px-6 py-3 text-left font-bold">Assigné à</th>
                    <th class="px-6 py-3 text-left font-bold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($incidents as $incident)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 incident-title">{{ $incident->titre }}</td>
                        <td class="px-6 py-4 capitalize incident-statut">{{ str_replace('_', ' ', ucfirst($incident->statut)) }}</td>
                        <td class="px-6 py-4 incident-user">{{ $incident->utilisateur?->name ?? '—' }}</td>
                        <td class="px-6 py-4 incident-gestionnaire">{{ $incident->gestionnaire?->name ?? '—' }}</td>
                        <td class="px-6 py-4 flex flex-wrap gap-2">
                            <a href="{{ route('admin.incidents.show', $incident) }}"
                               class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800 rounded transition">
                                Voir
                            </a>
                            <a href="{{ route('admin.incidents.edit', $incident) }}"
                               class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-yellow-700 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900 hover:bg-yellow-200 dark:hover:bg-yellow-800 rounded transition">
                                Modifier
                            </a>
                            <form action="{{ route('admin.incidents.destroy', $incident) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet incident ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 rounded transition">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun incident trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6">
            {{ $incidents->appends(request()->only('statut', 'assigne_a_moi'))->links() }}
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#incidentTable tbody tr');

            rows.forEach(row => {
                const title = row.querySelector('.incident-title')?.textContent.toLowerCase() || '';
                const statut = row.querySelector('.incident-statut')?.textContent.toLowerCase() || '';
                const user = row.querySelector('.incident-user')?.textContent.toLowerCase() || '';
                const gestionnaire = row.querySelector('.incident-gestionnaire')?.textContent.toLowerCase() || '';

                row.style.display = (
                    title.includes(searchValue) ||
                    statut.includes(searchValue) ||
                    user.includes(searchValue) ||
                    gestionnaire.includes(searchValue)
                ) ? '' : 'none';
            });
        });
    </script>
</x-app-layout>
