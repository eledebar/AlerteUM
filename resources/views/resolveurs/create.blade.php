@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Créer un résolveur</h1>
    <form method="POST" action="{{ route('admin.resolveurs.store') }}" class="space-y-3">
        @csrf
        <div><label class="block text-sm">Nom</label><input name="name" class="w-full border rounded p-2" required></div>
        <div><label class="block text-sm">Email</label><input type="email" name="email" class="w-full border rounded p-2" required></div>
        <div><label class="block text-sm">Mot de passe</label><input type="password" name="password" class="w-full border rounded p-2" required></div>
        <div><label class="block text-sm">Confirmer le mot de passe</label><input type="password" name="password_confirmation" class="w-full border rounded p-2" required></div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Enregistrer</button>
    </form>
</div>
@endsection
