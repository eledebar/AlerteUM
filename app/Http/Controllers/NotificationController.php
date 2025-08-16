<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $unread = $user->unreadNotifications()->get();
        $read   = $user->readNotifications()->get();

        $incidentIds = $unread->concat($read)
            ->map(fn ($n) => $n->data['incident_id'] ?? ($n->data['incident']['id'] ?? null))
            ->filter()
            ->unique()
            ->values();

        $incidents = $incidentIds->isEmpty()
            ? collect()
            : Incident::with(['utilisateur', 'user'])->whereIn('id', $incidentIds)->get()->keyBy('id');

        $format = function (DatabaseNotification $n) use ($incidents) {
            $data = (array) ($n->data ?? []);

            $message = $data['message'] ?? null;
            $incidentId = $data['incident_id'] ?? ($data['incident']['id'] ?? null);
            $incident   = $incidentId ? $incidents->get($incidentId) : null;

            $public = $data['public_id']
                ?? ($incident?->public_id ?: ($incidentId ? ('INC-'.str_pad((string)$incidentId, 4, '0', STR_PAD_LEFT)) : null));

            $titre  = $data['titre'] ?? ($incident?->titre ?? null);

            $userName = $data['user_name']
                ?? $data['user']
                ?? $data['by']
                ?? $incident?->utilisateur?->name
                ?? $incident?->user?->name
                ?? "L’utilisateur";

            if ($message === null || $message === '') {
                if (array_key_exists('reason', $data)) {
                    $message = "{$userName} a réouvert l’incident {$public}" . ($titre ? " ({$titre})" : "");
                } else {
                    $message = "Mise à jour sur l’incident {$public}" . ($titre ? " ({$titre})" : "");
                }
            }

            $url = $data['url'] ?? null;

            return [
                'id'         => $n->id,
                'message'    => $message,
                'url'        => $url,
                'created_at' => $n->created_at,
            ];
        };

        $unreadItems = $unread->map($format);
        $readItems   = $read->map($format);

        return view('notifications.index', compact('unreadItems', 'readItems'));
    }

    public function markAsRead(string $id, Request $request): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
