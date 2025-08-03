<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Mes incidents
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ route('utilisateur.incidents.categories') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition text-center">
                + Signaler un incident
            </a>
            <a href="{{ route('utilisateur.incidents.export.csv', request()->only(['statut', 'date_debut', 'date_fin'])) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition text-center">
                ⬇️ Exporter en CSV
            </a>
        </div>

        <form method="GET" class="mb-6 flex flex-col lg:flex-row lg:items-end gap-4">
            <div>
                <label for="statut" class="block mb-1 font-medium text-white">Filtrer par statut:</label>
                <select name="statut" id="statut"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 rounded">
                    <option value="">Tous</option>
                    <option value="nouveau" {{ request('statut') == 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="résolu" {{ request('statut') == 'résolu' ? 'selected' : '' }}>Résolu</option>
                </select>
            </div>

            <div>
                <label for="date_debut" class="block mb-1 font-medium text-white">Du:</label>
                <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}"
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 rounded">
            </div>

            <div>
                <label for="date_fin" class="block mb-1 font-medium text-white">Au:</label>
                <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}"
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 rounded">
            </div>

            <div>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                    Filtrer
                </button>
            </div>
        </form>

        <div class="mb-6">
            <input type="text" id="searchInput" placeholder="🔍 Rechercher par titre ou statut..."
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded">
        </div>

        @if (session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto lg:overflow-x-hidden">
            <table class="min-w-full table-auto bg-white dark:bg-gray-800 shadow-md rounded text-gray-900 dark:text-gray-100" id="incidentTable">
                <thead>
                <tr class="bg-gray-100 dark:bg-gray-700">
                    <th class="px-6 py-3 text-left font-bold">Titre</th>
                    <th class="px-6 py-3 text-left font-bold">Statut</th>
                    <th class="px-6 py-3 text-left font-bold">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($incidents as $incident)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 incident-title">{{ $incident->titre }}</td>
                        <td class="px-6 py-4 capitalize incident-statut">{{ str_replace('_', ' ', ucfirst($incident->statut)) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('utilisateur.incidents.show', $incident) }}"
                                   class="w-8 h-8 transition-transform transform hover:scale-110">
                                    <img src="{{ asset('eye.webp') }}" alt="Voir" class="w-full h-full object-contain rounded" />
                                </a>
                                @if ($incident->statut === 'nouveau')
                                    <a href="{{ route('utilisateur.incidents.edit', $incident) }}"
                                       class="w-8 h-8 transition-transform transform hover:scale-110">
                                        <img src="{{ asset('edit.webp') }}" alt="Modifier" class="w-full h-full object-contain rounded" />
                                    </a>
                                    <form action="{{ route('utilisateur.incidents.destroy', $incident) }}" method="POST"
                                          onsubmit="return confirm('Supprimer cet incident ?')"
                                          class="w-8 h-8 transition-transform transform hover:scale-110">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full h-full">
                                            <img src="{{ asset('delete.webp') }}" alt="Supprimer" class="w-full h-full object-contain rounded" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun incident trouvé.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-8 flex flex-col items-center gap-2 text-white">
                <div class="text-sm">
                    Affichage de {{ $incidents->firstItem() }} à {{ $incidents->lastItem() }} sur {{ $incidents->total() }} résultats
                </div>
                <div>
                    {{ $incidents->appends(request()->all())->onEachSide(1)->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const debut = document.getElementById('date_debut');
                const fin = document.getElementById('date_fin');

                function ajusterMinMax() {
                    if (debut.value) {
                        fin.min = debut.value;
                        if (fin.value && fin.value < debut.value) fin.value = debut.value;
                    }
                    if (fin.value) {
                        debut.max = fin.value;
                        if (debut.value && debut.value > fin.value) debut.value = fin.value;
                    }
                }

                debut.addEventListener('change', ajusterMinMax);
                fin.addEventListener('change', ajusterMinMax);
                ajusterMinMax();

                document.getElementById('searchInput').addEventListener('keyup', function () {
                    const searchValue = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#incidentTable tbody tr');

                    rows.forEach(row => {
                        const title = row.querySelector('.incident-title')?.textContent.toLowerCase() || '';
                        const statut = row.querySelector('.incident-statut')?.textContent.toLowerCase() || '';

                        row.style.display = (
                            title.includes(searchValue) ||
                            statut.includes(searchValue)
                        ) ? '' : 'none';
                    });
                });
            });
        </script>
    </div>
</x-app-layout>
