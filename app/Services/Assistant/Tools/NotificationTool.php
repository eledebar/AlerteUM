<?php
namespace App\Services\Assistant\Tools;

class NotificationTool
{
    public function lister($user, int $limit = 5): array
    {
        return $user->notifications()->latest()->take($limit)->get()->map(function($n){
            return [
                'id' => $n->id,
                'date' => optional($n->created_at)->diffForHumans(),
                'message' => $n->data['message'] ?? 'Notification',
                'url' => $n->data['url'] ?? null,
                'read' => $n->read_at !== null,
            ];
        })->all();
    }
}
