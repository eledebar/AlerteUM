<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier l'incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-6 rounded-md border border-red-300 bg-red-50 p-4 text-red-700" role="alert" aria-live="assertive" aria-atomic="true" id="form-errors">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="mb-6 rounded-md border border-green-300 bg-green-50 p-4 text-green-800" role="status" aria-live="polite" aria-atomic="true">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('utilisateur.incidents.update', $incident) }}" @if($errors->any()) aria-describedby="form-errors" @endif>
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="titre" class="block font-medium">Titre</label>
                <input id="titre" type="text" name="titre" class="w-full border px-4 py-2" value="{{ old('titre', $incident->titre) }}" required autocomplete="off" @if($errors->has('titre')) aria-invalid="true" aria-describedby="titre-error" @else aria-invalid="false" @endif>
                @error('titre')
                    <p id="titre-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block font-medium">Description</label>
                <textarea id="description" name="description" class="w-full border px-4 py-2" rows="5" required @if($errors->has('description')) aria-invalid="true" aria-describedby="description-error" @else aria-invalid="false" @endif>{{ old('description', $incident->description) }}</textarea>
                @error('description')
                    <p id="description-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            @if (auth()->user()->estResolveur())
                @php $st = old('statut', $incident->statut); @endphp
                <div class="mb-4">
                    <label for="statut" class="block font-medium">Statut</label>
                    <select id="statut" name="statut" class="w-full border px-4 py-2" required @if($errors->has('statut')) aria-invalid="true" aria-describedby="statut-error" @else aria-invalid="false" @endif>
                        <option value="nouveau" @selected($st === 'nouveau')>Nouveau</option>
                        <option value="en_cours" @selected($st === 'en_cours')>En cours</option>
                        <option value="résolu" @selected($st === 'résolu')>Résolu</option>
                    </select>
                    @error('statut')
                        <p id="statut-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded" aria-label="Enregistrer les modifications">Enregistrer</button>
        </form>
    </div>
</x-app-layout>
