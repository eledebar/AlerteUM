<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Tableau de bord</h2>
    </x-slot>

    <div class="py-10 bg-gray-50 dark:bg-gray-900 space-y-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @if(auth()->user()->role === 'admin' || auth()->user()->estResolveur())
                    <div class="flex flex-col">
                        <label for="assigned" class="text-gray-700 dark:text-white">Assignés :</label>
                        <select name="assigned" id="assigned"
                                class="mt-1 px-3 py-2 rounded border dark:bg-gray-800 text-gray-900 dark:text-white">
                            <option value="">Tous</option>
                            <option value="me" {{ request('assigned') == 'me' ? 'selected' : '' }}>Moi</option>
                        </select>
                    </div>
                @endif

                <div class="flex flex-col">
                    <label for="from" class="text-gray-700 dark:text-white">Du :</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}"
                           class="mt-1 px-3 py-2 rounded border dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>

                <div class="flex flex-col">
                    <label for="to" class="text-gray-700 dark:text-white">Au :</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}"
                           class="mt-1 px-3 py-2 rounded border dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>

                <div class="flex flex-col justify-end">
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                        Appliquer
                    </button>
                </div>

                <div class="flex flex-col justify-end">
                    <button type="button" onclick="exportAllCharts()"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full">
                        Exporter Graphiques
                    </button>
                </div>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-yellow-100 dark:bg-yellow-300/20 p-6 rounded text-center shadow">
                    <h3 class="text-gray-700 dark:text-yellow-300">Nouveaux</h3>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $nouveaux }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-300/20 p-6 rounded text-center shadow">
                    <h3 class="text-gray-700 dark:text-blue-300">En cours</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $enCours }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-300/20 p-6 rounded text-center shadow">
                    <h3 class="text-gray-700 dark:text-green-300">Résolus</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $resolus }}</p>
                </div>
                <div class="bg-gray-100 dark:bg-gray-700/40 p-6 rounded text-center shadow">
                    <h3 class="text-gray-700 dark:text-gray-200">Fermés</h3>
                    <p class="text-3xl font-bold text-gray-700 dark:text-gray-200">{{ $fermes }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <label for="chartType" class="text-gray-700 dark:text-white">Type de graphique :</label>
                <select id="chartType" onchange="updateAllCharts()" class="px-3 py-2 rounded border dark:bg-gray-800 text-gray-900 dark:text-white">
                    <option value="bar">Barre</option>
                    <option value="line">Ligne</option>
                    <option value="doughnut">Doughnut</option>
                    <option value="pie">Tarte</option>
                </select>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded shadow space-y-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Incidents par statut</h3>
                    <canvas id="statutChart" class="w-full max-h-[350px]"></canvas>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Répartition par type</h3>
                    <canvas id="typeChart" class="w-full max-h-[350px]"></canvas>
                </div>

                <div id="dateChartContainer">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Évolution par date</h3>
                    <canvas id="dateChart" class="w-full max-h-[350px]"></canvas>
                </div>

                @if(auth()->user()->role === 'admin' || auth()->user()->estResolveur())
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Répartition par utilisateur</h3>
                        <canvas id="userChart" class="w-full max-h-[350px]"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const statutLabels = ['Nouveaux', 'En cours', 'Résolus', 'Fermés'];
        const statutData = [{{ $nouveaux }}, {{ $enCours }}, {{ $resolus }}, {{ $fermes }}];

        const typeLabels = @json($parCategorie->pluck('categorie'));
        const typeData   = @json($parCategorie->pluck('total'));
        const dateLabels = @json($evolution->pluck('date'));
        const dateData   = @json($evolution->pluck('total'));
        const userLabels = @json($parUtilisateur->pluck('name'));
        const userData   = @json($parUtilisateur->pluck('total'));

        let charts = {};

        function createChart(ctxId, labels, data, type = 'bar') {
            const ctx = document.getElementById(ctxId);
            if (!ctx || !labels.length || !data.length) return;
            if (charts[ctxId]) charts[ctxId].destroy();

            const colors = generateColors(labels.length);

            const legendVisible = (
                (ctxId === 'statutChart' && (type === 'doughnut' || type === 'pie')) ||
                ((ctxId === 'typeChart' || ctxId === 'userChart') && (type === 'doughnut' || type === 'pie'))
            );

            if (ctxId === 'statutChart' && (type === 'bar' || type === 'line')) {
                charts[ctxId] = new Chart(ctx, {
                    type,
                    data: {
                        labels,
                        datasets: [{
                            label: 'Total',
                            data,
                            backgroundColor: colors,
                            borderWidth: 1,
                            categoryPercentage: 0.5,
                            barPercentage: 0.5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } },
                            x: { grid: { display: false } }
                        }
                    }
                });
                return;
            }

            charts[ctxId] = new Chart(ctx, {
                type,
                data: {
                    labels,
                    datasets: [{
                        label: 'Total',
                        data,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: legendVisible } },
                    scales: (type === 'bar' || type === 'line') ? {
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } }
                    } : {}
                }
            });
        }

        function generateColors(count) {
            const palette = ['#f87171', '#60a5fa', '#34d399', '#9ca3af', '#facc15', '#fb923c', '#a5b4fc'];
            return Array.from({ length: count }, (_, i) => palette[i % palette.length]);
        }

        function updateAllCharts() {
            const type = document.getElementById('chartType').value;

            createChart('statutChart', statutLabels, statutData, type);
            createChart('typeChart',   typeLabels,   typeData,   type);

            const dateContainer = document.getElementById('dateChartContainer');
            if (type === 'line') {
                dateContainer.style.display = 'block';
                createChart('dateChart', dateLabels, dateData, type);
            } else {
                dateContainer.style.display = 'none';
                if (charts['dateChart']) {
                    charts['dateChart'].destroy();
                    delete charts['dateChart'];
                }
            }

            @if(auth()->user()->role === 'admin' || auth()->user()->estResolveur())
                createChart('userChart', userLabels, userData, type);
            @endif
        }

        function exportAllCharts() {
            Object.entries(charts).forEach(([key, chart]) => {
                const link = document.createElement('a');
                link.href = chart.toBase64Image();
                link.download = `${key}.png`;
                link.click();
            });
        }

        const from = document.getElementById('from');
        const to   = document.getElementById('to');

        from.addEventListener('change', () => {
            if (to.value && to.value < from.value) to.value = from.value;
            to.min = from.value;
        });

        to.addEventListener('change', () => {
            if (from.value && from.value > to.value) from.value = to.value;
            from.max = to.value;
        });

        window.onload = updateAllCharts;
    </script>
</x-app-layout>
