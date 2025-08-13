@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        Modifier le résolveur #{{ $user->id }}
    </h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4">
    @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-300 bg-red-50 p-4 text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.resolveurs.update', $user) }}" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg border">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1" for="name">Nom complet</label>
            <input id="name" name="name" type="text" required
                   value="{{ old('name', $user->name) }}"
                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="email">Email</label>
            <input id="email" name="email" type="email" required
                   value="{{ old('email', $user->email) }}"
                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
        </div>

        {{-- Si no cambias contraseña, deja estos campos vacíos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="password">Nouveau mot de passe</label>
                <input id="password" name="password" type="password"
                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="password_confirmation">Confirmer</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
            </div>
        </div>

        <div class="flex items-center justify-between">
            <form method="POST" action="{{ route('admin.resolveurs.destroy', $user) }}" onsubmit="return confirm('Supprimer ce résolveur ?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-md border border-red-600 text-red-600">
                    Supprimer
                </button>
            </form>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.resolveurs.index') }}" class="px-4 py-2 rounded-md border">Annuler</a>
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white">Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
