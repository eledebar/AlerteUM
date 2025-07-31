<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Filtros --}}
            @if (Auth::user()->estAdmin())
                <form method="GET" class="mb-6 flex flex-wrap gap-4 items-center">
                    <label for="assigned" class="font-medium text-gray-800 dark:text-white">Filtrer:</label>
                    <select name="assigned" id="assigned" onchange="this.form.submit()"
                            class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 rounded">
                        <option value="">Tous les incidents</option>
                        <option value="me" {{ request('assigned') == 'me' ? 'selected' : '' }}>Assignés à moi</option>
                    </select>
                </form>
            @endif

            {{-- Résumé numérique --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Résumé des incidents</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div class="bg-yellow-100 dark:bg-yellow-300/20 p-4 rounded shadow">
                        <p class="text-sm text-gray-600 dark:text-yellow-200">Nouveaux</p>
                        <p class="text-2xl font-semibold text-yellow-800 dark:text-yellow-400">{{ $pendientes }}</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-300/20 p-4 rounded shadow">
                        <p class="text-sm text-gray-600 dark:text-blue-200">En cours</p>
                        <p class="text-2xl font-semibold text-blue-800 dark:text-blue-400">{{ $enProceso }}</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-300/20 p-4 rounded shadow">
                        <p class="text-sm text-gray-600 dark:text-green-200">Résolus</p>
                        <p class="text-2xl font-semibold text-green-800 dark:text-green-400">{{ $resueltas }}</p>
                    </div>
                </div>
            </div>

            {{-- Graphique en barres --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Incidents par statut</h3>
                <div class="max-w-2xl mx-auto">
                    <canvas id="barChart" width="400" height="300"></canvas>
                </div>
            </div>

            {{-- Graphique circulaire --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Répartition des incidents</h3>
                <div class="max-w-2xl mx-auto">
                    <canvas id="pieChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = ['Nouveaux', 'En cours', 'Résolus'];
        const values = [{{ $pendientes }}, {{ $enProceso }}, {{ $resueltas }}];

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#facc15', '#60a5fa', '#34d399'],
                    borderColor: ['#eab308', '#3b82f6', '#10b981'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: v => Number.isInteger(v) ? v : null
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Répartition des incidents par statut',
                        font: { size: 16 }
                    }
                }
            }
        });

        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#facc15', '#60a5fa', '#34d399'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Répartition des incidents',
                        font: { size: 16 }
                    }
                }
            }
        });
    </script>
</x-app-layout>
