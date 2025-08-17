<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Tableau de bord</h2>
    </x-slot>

    @php
        $resolveurs = $resolveurs ?? \App\Models\User::where('role','resolveur')->orderBy('name')->get(['id','name']);
        $resolveurId = $resolveurId ?? request()->integer('resolveur_id');
        $from = $from ?? request('from');
        $to = $to ?? request('to');
        $nouveaux = $nouveaux ?? 0;
        $enCours = $enCours ?? 0;
        $resolus = $resolus ?? 0;
        $fermes = $fermes ?? 0;
        $parCategorie = $parCategorie ?? collect();
        $evolution = $evolution ?? collect();
    @endphp

    <div class="py-10 bg-gray-50 dark:bg-gray-900 space-y-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="flex flex-col">
                    <label for="resolveur_id" class="text-gray-700 dark:text-white">Résolveur :</label>
                    <select name="resolveur_id" id="resolveur_id" class="mt-1 px-3 py-2 rounded border dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="">Tous</option>
                        @foreach($resolveurs as $r)
                            <option value="{{ $r->id }}" @selected((int)($resolveurId ?? 0) === (int)$r->id)>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="from" class="text-gray-700 dark:text-white">Du :</label>
                    <input type="date" name="from" id="from" value="{{ $from }}" class="mt-1 px-3 py-2 rounded border dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>

                <div class="flex flex-col">
                    <label for="to" class="text-gray-700 dark:text-white">Au :</label>
                    <input type="date" name="to" id="to" value="{{ $to }}" class="mt-1 px-3 py-2 rounded border dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>

                <div class="flex flex-col justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                        Appliquer
                    </button>
                </div>

                <div class="flex flex-col justify-end">
                    <button type="button" onclick="exportAllCharts()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full">
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

                <div id="dateChartContainer" style="display:none">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Évolution par date</h3>
                    <canvas id="dateChart" class="w-full max-h-[350px]"></canvas>
                </div>
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

        let charts = {};

        function createChart(id, labels, data, type = 'bar') {
            const el = document.getElementById(id);
            if (!el) return;
            if (charts[id]) charts[id].destroy();

            const palette = ['#f87171','#60a5fa','#34d399','#9ca3af','#facc15','#fb923c','#a5b4fc'];
            const colors = Array.from({length: labels.length}, (_,i)=>palette[i%palette.length]);

            const showLegend = (id === 'statutChart' && (type === 'doughnut' || type === 'pie')) || ((id === 'typeChart') && (type === 'doughnut' || type === 'pie'));

            if (id === 'statutChart' && (type === 'bar' || type === 'line')) {
                charts[id] = new Chart(el, {
                    type,
                    data: { labels, datasets: [{ label: 'Total', data, backgroundColor: colors, borderWidth: 1, categoryPercentage:.5, barPercentage:.5 }] },
                    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true, ticks:{stepSize:1, precision:0}}, x:{grid:{display:false}} } }
                });
                return;
            }

            charts[id] = new Chart(el, {
                type,
                data: { labels, datasets: [{ label: 'Total', data, backgroundColor: colors, borderWidth: 1 }] },
                options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:showLegend}}, scales: (type==='bar'||type==='line') ? { y:{beginAtZero:true, ticks:{stepSize:1, precision:0}} } : {} }
            });
        }

        function updateAllCharts(){
            const t = document.getElementById('chartType').value;

            createChart('statutChart', statutLabels, statutData, t);
            createChart('typeChart',   typeLabels,   typeData,   t);

            const dateContainer = document.getElementById('dateChartContainer');
            if (t === 'line') {
                dateContainer.style.display = 'block';
                createChart('dateChart', dateLabels, dateData, 'line');
            } else {
                dateContainer.style.display = 'none';
                if (charts['dateChart']) {
                    charts['dateChart'].destroy();
                    delete charts['dateChart'];
                }
            }
        }

        function exportAllCharts(){
            Object.values(charts).forEach(c => {
                const a = document.createElement('a');
                a.href = c.toBase64Image();
                a.download = 'chart.png';
                a.click();
            });
        }

        const fromEl = document.getElementById('from');
        const toEl = document.getElementById('to');
        if (fromEl && toEl){
            fromEl.addEventListener('change', ()=>{ if (toEl.value && toEl.value < fromEl.value) toEl.value = fromEl.value; toEl.min = fromEl.value; });
            toEl.addEventListener('change', ()=>{ if (fromEl.value && fromEl.value > toEl.value) fromEl.value = toEl.value; fromEl.max = toEl.value; });
        }

        window.onload = updateAllCharts;
    </script>
</x-app-layout>
