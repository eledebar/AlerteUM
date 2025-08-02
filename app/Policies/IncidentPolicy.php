<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
 
    public function update(User $user, Incident $incident): bool
    {
        return $user->id === $incident->utilisateur_id;
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->id === $incident->utilisateur_id;
    }
}
