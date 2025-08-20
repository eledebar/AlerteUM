<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Tableau de bord</h2>
    </x-slot>

    @php
        $cats = collect($parCategorie ?? []);
        $evo  = collect($evolution ?? []);
        $users = collect($parUtilisateur ?? []);

        $ariaTypes = $cats->map(function($r){
            $c = data_get($r,'categorie','—');
            $t = data_get($r,'total',0);
            return $c.' '.$t;
        })->implode(', ');

        $ariaDates = $evo->map(function($r){
            $d = data_get($r,'date','—');
            $t = data_get($r,'total',0);
            return $d.' '.$t;
        })->implode(', ');

        $ariaUsers = $users->map(function($r){
            $n = data_get($r,'name','—');
            $t = data_get($r,'total',0);
            return $n.' '.$t;
        })->implode(', ');
    @endphp

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
                    <button type="button" onclick="exportAllCharts()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full" aria-describedby="exportHint">
                        Exporter Graphiques
                    </button>
                    <span id="exportHint" class="sr-only">Télécharge tous les graphiques visibles au format PNG.</span>
                </div>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4" role="region" aria-label="Indicateurs clés">
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
                <select id="chartType" onchange="updateAllCharts()" class="px-3 py-2 rounded border dark:bg-gray-800 text-gray-900 dark:text-white" aria-controls="chartsRegion" aria-describedby="chartTypeHelp">
                    <option value="bar">Barre</option>
                    <option value="line">Ligne</option>
                    <option value="doughnut">Doughnut</option>
                    <option value="pie">Tarte</option>
                </select>
                <span id="chartTypeHelp" class="sr-only">Change la représentation visuelle. Les mêmes données sont disponibles dans des tableaux accessibles.</span>
            </div>

            <div id="chartsRegion" class="bg-white dark:bg-gray-800 p-6 rounded shadow space-y-10" role="region" aria-label="Graphiques et tableaux associés">
                <figure role="group" aria-labelledby="h-statut" class="space-y-2">
                    <h3 id="h-statut" class="text-lg font-bold text-gray-800 dark:text-white mb-2">Incidents par statut</h3>
                    <canvas
                        id="statutChart"
                        class="w-full max-h-[350px]"
                        role="img"
                        aria-label="Incidents par statut : Nouveaux {{ $nouveaux }}, En cours {{ $enCours }}, Résolus {{ $resolus }}, Fermés {{ $fermes }}.">
                    </canvas>
                    <table class="sr-only">
                        <caption>Données du graphique Incidents par statut</caption>
                        <thead>
                            <tr>
                                <th scope="col">Statut</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Nouveaux</td><td>{{ $nouveaux }}</td></tr>
                            <tr><td>En cours</td><td>{{ $enCours }}</td></tr>
                            <tr><td>Résolus</td><td>{{ $resolus }}</td></tr>
                            <tr><td>Fermés</td><td>{{ $fermes }}</td></tr>
                        </tbody>
                    </table>
                </figure>

                <figure role="group" aria-labelledby="h-type" class="space-y-2">
                    <h3 id="h-type" class="text-lg font-bold text-gray-800 dark:text-white mb-2">Répartition par type</h3>
                    <canvas
                        id="typeChart"
                        class="w-full max-h-[350px]"
                        role="img"
                        aria-label="Répartition par type : {{ $ariaTypes }}.">
                    </canvas>
                    <table class="sr-only">
                        <caption>Données du graphique Répartition par type</caption>
                        <thead>
                            <tr>
                                <th scope="col">Type</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($cats as $row)
                            <tr>
                                <td>{{ data_get($row,'categorie') }}</td>
                                <td>{{ data_get($row,'total') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </figure>

                <figure id="dateChartContainer" role="group" aria-labelledby="h-date" class="space-y-2">
                    <h3 id="h-date" class="text-lg font-bold text-gray-800 dark:text-white mb-2">Évolution par date</h3>
                    <canvas
                        id="dateChart"
                        class="w-full max-h-[350px]"
                        role="img"
                        aria-label="Évolution par date : {{ $ariaDates }}.">
                    </canvas>
                    <table class="sr-only">
                        <caption>Données du graphique Évolution par date</caption>
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($evo as $row)
                            <tr>
                                <td>{{ data_get($row,'date') }}</td>
                                <td>{{ data_get($row,'total') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </figure>

             

            <span id="chartsLive" class="sr-only" aria-live="polite" aria-atomic="true"></span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const statutLabels = ['Nouveaux', 'En cours', 'Résolus', 'Fermés'];
        const statutData = [{{ $nouveaux }}, {{ $enCours }}, {{ $resolus }}, {{ $fermes }}];

        const typeLabels = @json($cats->map(fn($r)=>data_get($r,'categorie'))->values());
        const typeData   = @json($cats->map(fn($r)=>data_get($r,'total',0))->values());
        const dateLabels = @json($evo->map(fn($r)=>data_get($r,'date'))->values());
        const dateData   = @json($evo->map(fn($r)=>data_get($r,'total',0))->values());
        const userLabels = @json($users->map(fn($r)=>data_get($r,'name'))->values());
        const userData   = @json($users->map(fn($r)=>data_get($r,'total',0))->values());

        let charts = {};

        function createChart(ctxId, labels, data, type = 'bar') {
            const ctx = document.getElementById(ctxId);
            if (!ctx || !labels || !labels.length) return;
            if (charts[ctxId]) charts[ctxId].destroy();

            const colors = generateColors(labels.length);
            const legendVisible = (
                (ctxId === 'statutChart' && (type === 'doughnut' || type === 'pie')) ||
                ((ctxId === 'typeChart' || ctxId === 'userChart') && (type === 'doughnut' || type === 'pie'))
            );

            if (ctxId === 'statutChart' && (type === 'bar' || type === 'line')) {
                charts[ctxId] = new Chart(ctx, {
                    type,
                    data: { labels, datasets: [{ label: 'Total', data, backgroundColor: colors, borderWidth: 1, categoryPercentage:.5, barPercentage:.5 }] },
                    options: {
                        responsive:true, maintainAspectRatio:false,
                        plugins:{ legend:{ display:false } },
                        scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1, precision:0 } }, x:{ grid:{ display:false } } }
                    }
                });
                return;
            }

            charts[ctxId] = new Chart(ctx, {
                type,
                data: { labels, datasets: [{ label: 'Total', data, backgroundColor: colors, borderWidth:1 }] },
                options: {
                    responsive:true, maintainAspectRatio:false,
                    plugins:{ legend:{ display: legendVisible } },
                    scales: (type==='bar'||type==='line') ? { y:{ beginAtZero:true, ticks:{ stepSize:1, precision:0 } } } : {}
                }
            });
        }

        function generateColors(count) {
            const palette = ['#f87171','#60a5fa','#34d399','#9ca3af','#facc15','#fb923c','#a5b4fc'];
            return Array.from({length: count}, (_,i)=>palette[i%palette.length]);
        }

        function updateAllCharts(){
            const t = document.getElementById('chartType').value;
            const live = document.getElementById('chartsLive');

            createChart('statutChart', statutLabels, statutData, t);
            createChart('typeChart',   typeLabels,   typeData,   t);

            const dateContainer = document.getElementById('dateChartContainer');
            if (t === 'line') {
                dateContainer.style.display = 'block';
                dateContainer.setAttribute('aria-hidden', 'false');
                createChart('dateChart', dateLabels, dateData, 'line');
            } else {
                dateContainer.style.display = 'none';
                dateContainer.setAttribute('aria-hidden', 'true');
                if (charts['dateChart']) { charts['dateChart'].destroy(); delete charts['dateChart']; }
            }

            @if(auth()->user()->role === 'admin' || auth()->user()->estResolveur())
                createChart('userChart', userLabels, userData, t);
            @endif

            if (live) live.textContent = 'Graphiques mis à jour, type ' + (t === 'bar' ? 'barre' : t === 'line' ? 'ligne' : t === 'pie' ? 'tarte' : 'doughnut') + '.';
        }

        function exportAllCharts(){
            Object.entries(charts).forEach(([key, c]) => {
                const a = document.createElement('a');
                a.href = c.toBase64Image();
                a.download = key + '.png';
                a.click();
            });
        }

        const from = document.getElementById('from');
        const to   = document.getElementById('to');

        if (from && to){
            from.addEventListener('change', ()=>{
                if (to.value && to.value < from.value) to.value = from.value;
                to.min = from.value;
            });
            to.addEventListener('change', ()=>{
                if (from.value && from.value > to.value) from.value = to.value;
                from.max = to.value;
            });
        }

        window.onload = updateAllCharts;
    </script>
</x-app-layout>
