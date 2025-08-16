<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Incident;

class IncidentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin','resolveur','utilisateur'], true);
    }

    public function view(User $user, Incident $incident): bool
    {
        $role = $user->role;
        $norm = Incident::normalizeStatus($incident->statut);

        if ($role === 'admin') return true;
        if ($role === 'resolveur') return true;
        if ($role === 'utilisateur') return (int) $incident->utilisateur_id === (int) $user->id;

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin','utilisateur','resolveur'], true);
    }

    public function update(User $user, Incident $incident): bool
    {
        $role = $user->role;
        $norm = Incident::normalizeStatus($incident->statut);

        if ($role === 'admin') return true;
        if ($role === 'resolveur') return $norm !== Incident::STATUT_FERME;
        if ($role === 'utilisateur') return (int) $incident->utilisateur_id === (int) $user->id && $norm === Incident::STATUT_NOUVEAU;

        return false;
    }

    public function delete(User $user, Incident $incident): bool
    {
        if ($user->role === 'admin') return true;

        return $user->role === 'utilisateur'
            && (int) $incident->utilisateur_id === (int) $user->id
            && in_array(Incident::normalizeStatus($incident->statut), [Incident::STATUT_NOUVEAU], true);
    }
}
