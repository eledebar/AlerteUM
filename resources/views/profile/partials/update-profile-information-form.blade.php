<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations du profil</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Mettre à jour le nom et l’email du compte.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm text-gray-700 dark:text-gray-300">Nom</label>
            <input id="name" name="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" name="email" type="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="flex items-center gap-4">
            <button class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Enregistrer</button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600 dark:text-gray-400">Enregistré.</p>
            @endif
        </div>
    </form>
</section>
