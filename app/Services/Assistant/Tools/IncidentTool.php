<?php
namespace App\Services\Assistant\Tools;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class IncidentTool
{
    public function etatIncident($user, int $id): ?array
    {
        $i = Incident::where('utilisateur_id', $user->id)->find($id);
        return $this->formatIncident($i);
    }

    public function etatIncidentParCode($user, string $code): ?array
    {
        $i = Incident::where('utilisateur_id', $user->id)
            ->whereRaw('UPPER(public_id) = ?', [mb_strtoupper($code)])
            ->first();
        return $this->formatIncident($i);
    }

    public function etatIncidentParRef($user, string $ref)
    {
        if (preg_match('/^\d+$/', $ref)) return $this->etatIncident($user, (int)$ref);
        return $this->etatIncidentParCode($user, $ref);
    }

    public function creerIncident($user, ?string $categorie, ?string $type, string $titre, string $description): array
    {
        $incident = new Incident();
        $incident->utilisateur_id = $user->id;
        $incident->titre          = $titre;
        $incident->description    = $description;
        $incident->categorie      = $categorie;
        $incident->type           = $type;
        $incident->statut         = Incident::STATUT_NOUVEAU;
        $incident->save();

        return $this->formatIncident($incident) ?? [];
    }

    public function lister($user, ?string $etat = null, int $limit = 10): array
    {
        $q = Incident::where('utilisateur_id', $user->id)->orderByDesc('id');
        if ($etat) $q->where('statut', $etat);
        return $q->take($limit)->get(['id','public_id','titre','statut'])->map(function($i){
            return $this->mapLite($i);
        })->all();
    }

    public function rechercher($user, string $q, int $limit = 10): array
    {
        $builder = Incident::where('utilisateur_id', $user->id)
            ->where(function($qq) use ($q){
                $like = '%' . $q . '%';
                $qq->where('titre','like',$like)
                   ->orWhere('description','like',$like)
                   ->orWhere('categorie','like',$like)
                   ->orWhere('type','like',$like)
                   ->orWhere('public_id','like',$like);
            })
            ->orderByDesc('id');

        return $builder->take($limit)->get(['id','public_id','titre','statut'])->map(function($i){
            return $this->mapLite($i);
        })->all();
    }


    protected function mapLite($i): array
    {
        $public = $i->public_id ?? ('INC-'.str_pad((string)$i->id,4,'0',STR_PAD_LEFT));
        return [
            'id'     => (int) $i->id,
            'public' => $public,
            'titre'  => $i->titre ?? '',
            'statut' => $i->statut ?? '',
        ];
    }

    protected function formatIncident($i): ?array
    {
        if (!$i) return null;
        $public = $i->public_id ?? ('INC-'.str_pad((string)$i->id,4,'0',STR_PAD_LEFT));
        return [
            'id'     => (int)$i->id,
            'public' => $public,
            'titre'  => $i->titre ?? '',
            'statut' => $i->statut ?? '',
        ];
    }
}
