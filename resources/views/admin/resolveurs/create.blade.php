@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        Créer un résolveur
    </h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4">
    @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-300 bg-red-50 p-4 text-red-700" role="alert" aria-live="assertive" aria-atomic="true" id="form-errors">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.resolveurs.store') }}" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg border" @if($errors->any()) aria-describedby="form-errors" @endif>
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1" for="name">Nom complet</label>
            <input id="name" name="name" type="text" required autocomplete="name"
                   value="{{ old('name') }}"
                   @if($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @else aria-invalid="false" @endif
                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
            @error('name')
                <p id="name-error" class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="email">Email</label>
            <input id="email" name="email" type="email" required autocomplete="email"
                   value="{{ old('email') }}"
                   @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @else aria-invalid="false" @endif
                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
            @error('email')
                <p id="email-error" class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <input type="hidden" name="role" value="resolveur" aria-label="role">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       @if($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @else aria-invalid="false" @endif
                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                @error('password')
                    <p id="password-error" class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                       @if($errors->has('password_confirmation')) aria-invalid="true" aria-describedby="password_confirmation-error" @else aria-invalid="false" @endif
                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                @error('password_confirmation')
                    <p id="password_confirmation-error" class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.resolveurs.index') }}" class="px-4 py-2 rounded-md border" aria-label="Retour à la liste des résolveurs">
                Retour
            </a>
            <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
