<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2C10.343 2 9 3.343 9 5v1.071A7.002 7.002 0 005 13v3l-2 2v1h18v-1l-2-2v-3a7.002 7.002 0 00-4-6.929V5c0-1.657-1.343-3-3-3zm0 20a2.5 2.5 0 01-2.45-2h4.9A2.5 2.5 0 0112 22z"/>
            </svg>
            Notifications
        </h2>
    </x-slot>

    @php
        if (!function_exists('format_notif_message')) {
            function format_notif_message(array $data): string
            {
                $message = $data['message']
                    ?? $data['content']
                    ?? $data['texte']
                    ?? $data['text']
                    ?? $data['title']
                    ?? null;

                if ($message) return (string) $message;

                $incidentId = $data['incident_id'] ?? ($data['incident']['id'] ?? null);

                $user = $data['user_name']
                    ?? $data['user']
                    ?? $data['by']
                    ?? $data['auteur']
                    ?? $data['author']
                    ?? null;

                $actionRaw = strtolower((string)($data['action'] ?? $data['event'] ?? $data['type'] ?? ''));
                $action = match ($actionRaw) {
                    'reopened', 'reopen', 'réouvert', 'reouvert'      => "a réouvert l'incident",
                    'status_changed', 'status', 'etat', 'state'       => "a mis à jour le statut de l'incident",
                    'comment', 'commented'                            => "a commenté l'incident",
                    'created', 'create'                               => "a créé l'incident",
                    'updated', 'update'                               => "a mis à jour l'incident",
                    default                                           => "a réalisé une action",
                };

                $parts = [];
                if ($user)        $parts[] = trim($user);
                $parts[] = $action;
                if ($incidentId)  $parts[] = "#{$incidentId}";

                $built = trim(implode(' ', $parts));
                return $built !== '' ? $built : "Nouvelle notification";
            }
        }

        $unread = auth()->user()->unreadNotifications;
        $read = auth()->user()->readNotifications;
        $unreadCount = $unread->count();
        $readCount = $read->count();
    @endphp

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data x-cloak>
        <div class="sr-only" aria-live="polite" aria-atomic="true">Il y a {{ $unreadCount }} notification{{ $unreadCount>1?'s':'' }} non lue{{ $unreadCount>1?'s':'' }}.</div>

        <section aria-labelledby="unread-title">
            <h3 id="unread-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Non lues</h3>

            @forelse($unread as $notification)
                @php
                    $data = (array) ($notification->data ?? []);
                    $msg  = format_notif_message($data);
                    $url  = $data['url'] ?? null;
                    $msgId = 'msg-'.$notification->id;
                @endphp

                <article class="bg-blue-100 dark:bg-blue-900 p-4 rounded shadow text-gray-900 dark:text-gray-100 transition-all" role="listitem" aria-labelledby="{{ $msgId }}">
                    <p id="{{ $msgId }}" class="font-semibold">{{ $msg }}</p>

                    <small class="text-gray-700 dark:text-gray-300">
                        <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
                    </small><br>

                    <div class="mt-1 flex items-center gap-2">
                        @if($url)
                            <a href="{{ $url }}" class="text-blue-700 dark:text-blue-300 underline focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" aria-label="Voir l’incident lié">Voir l'incident</a>
                        @endif

                        <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-800 dark:text-gray-200 underline focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-describedby="{{ $msgId }}">Marquer comme lue</button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="text-gray-600 dark:text-gray-400">Aucune nouvelle notification.</p>
            @endforelse
        </section>

        @if($readCount)
            <hr class="border-gray-300 dark:border-gray-700">

            <section aria-labelledby="read-title">
                <h3 id="read-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Notifications lues</h3>

                <ul role="list" class="space-y-4">
                    @foreach($read as $notification)
                        @php
                            $data = (array) ($notification->data ?? []);
                            $msg  = format_notif_message($data);
                            $url  = $data['url'] ?? null;
                            $rmsgId = 'rmsg-'.$notification->id;
                        @endphp

                        <li class="bg-gray-100 dark:bg-gray-800 p-4 rounded shadow text-gray-900 dark:text-gray-100 transition-all" role="listitem" aria-labelledby="{{ $rmsgId }}">
                            <p id="{{ $rmsgId }}" class="font-medium">{{ $msg }}</p>

                            <small class="text-gray-700 dark:text-gray-300">
                                <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
                            </small><br>

                            @if($url)
                                <a href="{{ $url }}" class="text-blue-700 dark:text-blue-300 underline focus:focus-visible:outline-2 focus-visible:outline-offset-2 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" aria-label="Voir l’incident lié">Voir l'incident</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
</x-app-layout>
