<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">Nouvel Incident</h2>
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

        <form method="POST" action="{{ route('resolveur.incidents.store') }}" @if($errors->any()) aria-describedby="form-errors" @endif>
            @csrf

            <div class="mb-4">
                <label for="titre" class="block font-medium">Titre</label>
                <input id="titre" type="text" name="titre" required autocomplete="off"
                       value="{{ old('titre') }}"
                       @if($errors->has('titre')) aria-invalid="true" aria-describedby="titre-error" @else aria-invalid="false" @endif
                       class="w-full border px-4 py-2" />
                @error('titre')
                    <p id="titre-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block font-medium">Description</label>
                <textarea id="description" name="description" rows="5" required
                          @if($errors->has('description')) aria-invalid="true" aria-describedby="description-error" @else aria-invalid="false" @endif
                          class="w-full border px-4 py-2">{{ old('description') }}</textarea>
                @error('description')
                    <p id="description-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="utilisateur_id" class="block font-medium">Utilisateur</label>
                <select id="utilisateur_id" name="utilisateur_id" required
                        @if($errors->has('utilisateur_id')) aria-invalid="true" aria-describedby="utilisateur_id-error" @else aria-invalid="false" @endif
                        class="w-full border px-4 py-2">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('utilisateur_id')==$user->id)>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('utilisateur_id')
                    <p id="utilisateur_id-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" aria-label="Créer l’incident">Créer</button>
        </form>
    </div>
</x-app-layout>
