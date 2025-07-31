<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
            {{-- Icône cloche SVG --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C10.343 2 9 3.343 9 5v1.071A7.002 7.002 0 005 13v3l-2 2v1h18v-1l-2-2v-3a7.002 7.002 0 00-4-6.929V5c0-1.657-1.343-3-3-3zm0 20a2.5 2.5 0 01-2.45-2h4.9A2.5 2.5 0 0112 22z"/>
            </svg>
            Notifications
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4" x-data x-cloak>

        {{-- Notifications non lues --}}
        @forelse(auth()->user()->unreadNotifications as $notification)
            <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded shadow text-gray-900 dark:text-gray-100 transition-all">
                <p class="font-semibold">
                    {{ $notification->data['message'] ?? 'Notification sans contenu.' }}
                </p>
                <small class="text-gray-600 dark:text-gray-400">
                    {{ $notification->created_at->diffForHumans() }}
                </small><br>

                @if(isset($notification->data['url']))
                    <a href="{{ $notification->data['url'] }}" class="text-blue-600 dark:text-blue-300 underline">Voir l'incident</a> |
                @endif

                <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}" class="inline">
                    @csrf
                    <button class="text-sm text-gray-700 dark:text-gray-300 underline">Marquer comme lue</button>
                </form>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400">Aucune nouvelle notification.</p>
        @endforelse

        {{-- Séparateur --}}
        @if(auth()->user()->readNotifications->count())
            <hr class="my-6 border-gray-300 dark:border-gray-700">
            <h3 class="text-gray-600 dark:text-gray-400 font-semibold">Notifications lues</h3>
        @endif

        {{-- Notifications lues --}}
        @foreach(auth()->user()->readNotifications as $notification)
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded shadow text-gray-900 dark:text-gray-100 transition-all">
                <p class="font-medium">
                    {{ $notification->data['message'] ?? 'Notification sans contenu.' }}
                </p>
                <small class="text-gray-600 dark:text-gray-400">
                    {{ $notification->created_at->diffForHumans() }}
                </small><br>

                @if(isset($notification->data['url']))
                    <a href="{{ $notification->data['url'] }}" class="text-blue-600 dark:text-blue-300 underline">Voir l'incident</a>
                @endif
            </div>
        @endforeach

    </div>
</x-app-layout>
