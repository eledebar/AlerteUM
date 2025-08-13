@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        Soporte (Résolveurs)
    </h2>
@endsection

@section('content')
<div class="max-w-7xl mx-auto p-6" x-data>
    {{-- Flash éxito --}}
    @if (session('success'))
        <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold">Gestion des résolveurs</h1>
            <span class="text-sm text-gray-500 dark:text-gray-400">({{ $resolveurs->total() }} au total)</span>
        </div>

        <div class="flex items-center gap-3">
            {{-- Filtro rápido (cliente) --}}
            <label class="sr-only" for="q">Recherche</label>
            <input id="q" type="search" placeholder="Rechercher nom ou email…"
                   class="w-64 rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                   oninput="
                      const q=this.value.toLowerCase();
                      document.querySelectorAll('#resolveursTable tbody tr').forEach(tr=>{
                        const tx=tr.innerText.toLowerCase();
                        tr.style.display = tx.includes(q) ? '' : 'none';
                      });
                   ">

            <a href="{{ route('admin.resolveurs.create') }}"
               class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                + Créer
            </a>
        </div>
    </div>

    <div class="overflow-hidden rounded border bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table id="resolveursTable" class="min-w-full table-auto text-sm text-gray-900 dark:text-gray-100">
                <thead>
                    <tr class="border-b bg-gray-50 dark:border-gray-800 dark:bg-gray-800/60">
                        <th scope="col" class="p-3 text-left font-semibold">Nom</th>
                        <th scope="col" class="p-3 text-left font-semibold">Email</th>
                        <th scope="col" class="p-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resolveurs as $r)
                        <tr class="border-b last:border-0 dark:border-gray-800">
                            <td class="p-3 align-middle">{{ $r->name }}</td>
                            <td class="p-3 align-middle">{{ $r->email }}</td>
                            <td class="p-3 align-middle">
                                <div class="flex items-center gap-2">
                                    {{-- Editar --}}
                                    <a href="{{ route('admin.resolveurs.edit', $r) }}"
                                       class="h-8 w-8 transform transition hover:scale-110"
                                       title="Éditer" aria-label="Éditer">
                                        <img src="{{ asset('edit.webp') }}" alt="Éditer" class="h-full w-full rounded object-contain">
                                    </a>

                                    {{-- Eliminar --}}
                                    <form method="POST" action="{{ route('admin.resolveurs.destroy', $r) }}"
                                          onsubmit="return confirm('Supprimer ce résolveur ?')"
                                          class="h-8 w-8 transform transition hover:scale-110">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer" aria-label="Supprimer" class="h-full w-full">
                                            <img src="{{ asset('delete.webp') }}" alt="Supprimer" class="h-full w-full rounded object-contain">
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center text-gray-500">Aucun résolveur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="border-t p-3 dark:border-gray-800">
            {{ $resolveurs->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection
