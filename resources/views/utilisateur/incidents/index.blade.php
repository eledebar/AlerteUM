<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Mes incidents') }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 p-6 rounded shadow mt-4">
        <!-- Botones de acción -->
        <div class="mb-6 flex flex-wrap gap-4">
            <a href="{{ route('utilisateur.incidents.categories') }}"
               class="bg-violet-600 text-white px-4 py-2 rounded hover:bg-violet-700 transition">
                + Signaler un incident
            </a>
            <a href="{{ route('utilisateur.incidents.export.csv', request()->only(['statut', 'type', 'titre', 'date_debut', 'date_fin'])) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                ⬇️ Exporter en CSV
            </a>
        </div>

        <!-- Formulario de filtrado -->
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
                <label for="date_debut" class="mr-2 font-medium text-gray-800 dark:text-gray-200">Du:</label>
                <input type="date" id="date_debut" name="date_debut" value="{{ request('date_debut') }}"
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-1 rounded">
            </div>

            <div>
                <label for="date_fin" class="mr-2 font-medium text-gray-800 dark:text-gray-200">Au:</label>
                <input type="date" id="date_fin" name="date_fin" value="{{ request('date_fin') }}"
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-1 rounded">
            </div>

            <div>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                    Filtrer
                </button>
            </div>
        </form>

        <!-- Buscador -->
        <div class="mb-6">
            <input type="text" id="searchInput" placeholder="🔍 Rechercher par titre ou statut..."
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded">
        </div>

        <!-- Mensaje de éxito -->
        @if (session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabla -->
        <div class="overflow-x-auto rounded shadow-md">
            <table class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded" id="incidentTable">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
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
                            <div class="flex items-center justify-start gap-2">
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
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $incidents->appends(request()->all())->links() }}
        </div>
    </div>

    <!-- Buscador funcional JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function () {
                    const value = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#incidentTable tbody tr');
                    rows.forEach(row => {
                        const title = row.querySelector('.incident-title')?.textContent.toLowerCase() || '';
                        const statut = row.querySelector('.incident-statut')?.textContent.toLowerCase() || '';
                        row.style.display = (title.includes(value) || statut.includes(value)) ? '' : 'none';
                    });
                });
            }
        });
    </script>
</x-app-layout>
