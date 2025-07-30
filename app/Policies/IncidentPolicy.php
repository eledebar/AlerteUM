<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    /**
     * L'utilisateur peut modifier son propre incident
     */
    public function update(User $user, Incident $incident): bool
    {
        return $user->id === $incident->utilisateur_id;
    }

    /**
     * L'utilisateur peut supprimer son propre incident
     */
    public function delete(User $user, Incident $incident): bool
    {
        return $user->id === $incident->utilisateur_id;
    }
}
