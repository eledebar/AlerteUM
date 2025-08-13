<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    /**
     * Affiche la vue des notifications avec des messages déjà formatés.
     * Si une notif n'apporte pas 'message', on le construit :
     *   "{Utilisateur} a réouvert l’incident {PUBLIC_ID} ({titre})"
     */
    public function index()
    {
        $user = auth()->user();

        /** @var Collection<int, DatabaseNotification> $unread */
        $unread = $user->unreadNotifications()->get();

        /** @var Collection<int, DatabaseNotification> $read */
        $read   = $user->readNotifications()->get();

        // Récupérer en une seule requête les incidents mentionnés
        $incidentIds = $unread->concat($read)
            ->map(fn ($n) => $n->data['incident_id'] ?? ($n->data['incident']['id'] ?? null))
            ->filter()
            ->unique()
            ->values();

        /** @var Collection<int, Incident> $incidents */
        $incidents = $incidentIds->isEmpty()
            ? collect()
            : Incident::with(['utilisateur', 'user'])->whereIn('id', $incidentIds)->get()->keyBy('id');

        // Formatter
        $format = function (DatabaseNotification $n) use ($incidents) {
            $data = (array) ($n->data ?? []);

            // 1) message fourni => on l'utilise tel quel
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

            // 2) Si pas de message, on construit un texte clair.
            if ($message === null || $message === '') {
                // Si la notif porte un 'reason', on l'interprète comme une réouverture
                if (array_key_exists('reason', $data)) {
                    $message = "{$userName} a réouvert l’incident {$public}" . ($titre ? " ({$titre})" : "");
                } else {
                    // Autre type : message générique mais propre
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

    /**
     * Marquer une notification comme lue (sécurisé pour l'utilisateur courant).
     */
    public function markAsRead(string $id, Request $request): RedirectResponse
    {
        /** @var DatabaseNotification $notification */
        $notification = $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * (Optionnel) Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
