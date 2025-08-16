@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto p-6"
     x-data="resolveurTable({
        endpoint: '{{ route('resolveur.incidents.datatable') }}',
        exportUrl: '{{ route('resolveur.incidents.export.csv') }}'
     })"
     x-init="init()">

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Gestion des incidents (Résolveur)</h1>
    <a :href="exportHref" class="px-3 py-2 rounded bg-green-600 text-white hover:bg-green-700">
      ⬇️ Exporter en CSV
    </a>
  </div>

  <form @submit.prevent class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-4">
    <div class="col-span-1">
      <label class="block text-sm text-gray-300">Assignés</label>
      <select x-model="filters.assigned" class="w-full border rounded p-2 bg-white dark:bg-gray-800 dark:text-white">
        <option value="">Tous</option>
        <option value="me">Moi</option>
      </select>
    </div>

    <div class="col-span-1">
      <label class="block text-sm text-gray-300">Statut</label>
      <select x-model="filters.statut" class="w-full border rounded p-2 bg-white dark:bg-gray-800 dark:text-white">
        <option value="">Tous</option>
        <template x-for="s in ['nouveau','en_cours','résolu','fermé']" :key="s">
          <option :value="s" x-text="s"></option>
        </template>
      </select>
    </div>

    <div class="col-span-1">
      <label class="block text-sm text-gray-300">Priorité</label>
      <select x-model="filters.priority" class="w-full border rounded p-2 bg-white dark:bg-gray-800 dark:text-white">
        <option value="">Toutes</option>
        <template x-for="p in ['low','medium','high','critical']" :key="p">
          <option :value="p" x-text="p"></option>
        </template>
      </select>
    </div>

    <div class="col-span-1">
      <label class="block text-sm text-gray-300">Du</label>
      <input type="date" x-model="filters.from" class="w-full border rounded p-2 bg-white dark:bg-gray-800 dark:text-white">
    </div>

    <div class="col-span-1">
      <label class="block text-sm text-gray-300">Au</label>
      <input type="date" x-model="filters.to" class="w-full border rounded p-2 bg-white dark:bg-gray-800 dark:text-white">
    </div>

    <div class="col-span-1">
      <label class="block text-sm text-gray-300">Chercher (code/titre)</label>
      <input x-model.debounce.400ms="filters.text" class="w-full border rounded p-2 bg-white dark:bg-gray-800 dark:text-white" placeholder="INC-... / titre">
    </div>

    <div class="col-span-1 md:col-span-2 lg:col-span-2">
      <label class="block text-sm text-gray-300">Trier</label>
      <div class="flex gap-2">
        <select x-model="filters.sort" class="border rounded p-2 bg-white dark:bg-gray-800 dark:text-white">
          <template x-for="[k,v] in Object.entries(sortOptions)" :key="k">
            <option :value="k" x-text="v"></option>
          </template>
        </select>
        <select x-model="filters.dir" class="border rounded p-2 bg-white dark:bg-gray-800 dark:text-white">
          <option value="desc">Desc</option>
          <option value="asc">Asc</option>
        </select>
      </div>
    </div>
  </form>

  <div class="bg-white dark:bg-gray-900 border rounded overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="border-b dark:border-gray-700">
          <th class="text-left p-3">Code</th>
          <th class="text-left p-3">Titre</th>
          <th class="text-left p-3">Priorité</th>
          <th class="text-left p-3">Statut</th>
          <th class="text-left p-3">SLA</th>
          <th class="text-left p-3">Esc.</th>
          <th class="text-left p-3">Assigné</th>
          <th class="text-left p-3">Créé par</th>
          <th class="text-left p-3">Créé</th>
          <th class="text-left p-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template x-if="rows.length===0">
          <tr><td colspan="10" class="p-3 text-center text-gray-500">Aucun incident</td></tr>
        </template>

        <template x-for="r in rows" :key="r.id">
          <tr class="border-b dark:border-gray-800">
            <td class="p-3 font-mono" x-text="r.public_id"></td>
            <td class="p-3" x-text="r.titre"></td>

            <td class="p-3">
              <form @submit.prevent="updatePriority(r)" class="flex items-center gap-2">
                <select x-model="r.priority" class="border rounded p-1 text-xs bg-white dark:bg-gray-800 dark:text-white">
                  <template x-for="p in ['low','medium','high','critical']" :key="p">
                    <option :value="p" x-text="p"></option>
                  </template>
                </select>
                <button class="px-2 py-1 text-xs bg-gray-200 dark:bg-gray-700 rounded">OK</button>
              </form>
            </td>

            <td class="p-3 capitalize" x-text="r.statut"></td>
            <td class="p-3" x-text="r.sla_due_at ?? '—'"></td>
            <td class="p-3" x-text="r.escalation_level"></td>
            <td class="p-3" x-text="r.assigned_to ?? '—'"></td>
            <td class="p-3" x-text="r.created_by ?? '—'"></td>
            <td class="p-3" x-text="r.created_at"></td>
            <td class="p-3 space-x-2">
              <a class="text-blue-600" :href="r.urls.show_resolveur">Voir</a>
              <button class="text-orange-600" @click="escalate(r)">Esc.</button>
            </td>
          </tr>
        </template>
      </tbody>
    </table>

    <div class="p-3 flex justify-between text-sm text-white">
      <div x-text="`Page ${meta.current_page}/${meta.last_page} — Total: ${meta.total}`"></div>
      <div class="space-x-2">
        <button class="px-2 py-1 bg-gray-700 rounded disabled:opacity-50" :disabled="meta.current_page<=1" @click="goPage(meta.current_page-1)">«</button>
        <button class="px-2 py-1 bg-gray-700 rounded disabled:opacity-50" :disabled="meta.current_page>=meta.last_page" @click="goPage(meta.current_page+1)">»</button>
      </div>
    </div>
  </div>
</div>

<script>
function resolveurTable({endpoint, exportUrl}) {
  return {
    endpoint, exportUrl,
    rows: [], meta: {current_page:1,last_page:1,total:0},
    page: 1,
    sortOptions: {
      created_at:'Fecha', priority:'Priorité', statut:'Statut',
      escalation_level:'Escalation', sla_due_at:'SLA',
      public_id:'Code', titre:'Titre'
    },
    filters: { assigned:'', text:'', priority:'', statut:'', from:'', to:'', sort:'created_at', dir:'desc' },
    get exportHref() {
      const p = new URLSearchParams({...this.filters, page:this.page});
      return `${this.exportUrl}?${p.toString()}`;
    },
    init() { this.fetch(); this.$watch('filters', () => { this.page=1; this.fetch() }, {deep:true}); },
    goPage(n){ this.page = n; this.fetch(); },
    async fetch() {
      const params = new URLSearchParams({...this.filters, page:this.page});
      const res = await fetch(`${this.endpoint}?${params.toString()}`, {headers:{'Accept':'application/json'}});
      const json = await res.json();
      this.rows = json.data; this.meta = json.meta;
    },
    async updatePriority(r) {
      const form = new FormData();
      form.append('priority', r.priority);
      const res = await fetch(r.urls.priority_resolveur, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:form});
      if (!res.ok) alert('Erreur en priorité');
      else this.fetch();
    },
    async escalate(r) {
      const res = await fetch(r.urls.escalate_resolveur, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
      if (!res.ok) alert('Erreur en escalade');
      else this.fetch();
    }
  }
}
</script>
@endsection
