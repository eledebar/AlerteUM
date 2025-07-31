<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            🔔 Notificaciones
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

        {{-- Notificaciones no leídas --}}
        @forelse(auth()->user()->unreadNotifications as $notification)
            <div class="bg-blue-100 p-4 rounded shadow">
                <p class="font-semibold">
                    {{ $notification->data['message'] ?? 'Notification sans contenu.' }}
                </p>

                <small class="text-gray-600">{{ $notification->created_at->diffForHumans() }}</small><br>

                @if(isset($notification->data['url']))
                    <a href="{{ $notification->data['url'] }}" class="text-blue-600 underline">Voir l'incident</a> |
                @endif

                <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}" class="inline">
                    @csrf
                    <button class="text-sm text-gray-700 underline">Marquer comme lue</button>
                </form>
            </div>
        @empty
            <p class="text-gray-500">Pas de nouvelles notifications.</p>
        @endforelse

        {{-- Separador --}}
        @if(auth()->user()->readNotifications->count())
            <hr class="my-6 border-gray-300">
            <h3 class="text-gray-600 font-semibold">Notifications lues</h3>
        @endif

        {{-- Notificaciones leídas --}}
        @foreach(auth()->user()->readNotifications as $notification)
            <div class="bg-gray-100 p-4 rounded shadow">
                <p class="font-medium">
                    {{ $notification->data['message'] ?? 'Notification sans contenu.' }}
                </p>
                <small class="text-gray-600">{{ $notification->created_at->diffForHumans() }}</small><br>
                @if(isset($notification->data['url']))
                    <a href="{{ $notification->data['url'] }}" class="text-blue-600 underline">Voir l'incident</a>
                @endif
            </div>
        @endforeach

    </div>
</x-app-layout>
