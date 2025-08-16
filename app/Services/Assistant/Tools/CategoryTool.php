<?php
namespace App\Services\Assistant\Tools;

use App\Services\Assistant\Utils\Fuzzy;

class CategoryTool
{
    protected array $map = [
        'communication' => [
            'syn' => ['mail','messagerie','courriel','email','teams','fichiers partagés','onedrive','teams canal','visio'],
            'types' => ['Mail','Teams','Partage de fichiers','Calendrier'],
        ],
        'acces' => [
            'syn' => ['wifi','reseau','réseau','vpn','mot de passe','mdp','login','authentification'],
            'types' => ['Wi-Fi','VPN','Compte / mot de passe','Réseau'],
        ],
        'plateformes' => [
            'syn' => ['moodle','portail','plateforme','forms','formulaire en ligne','campus','intranet'],
            'types' => ['Moodle','Portails','Formulaires','Autres plateformes'],
        ],
        'equipements' => [
            'syn' => ['imprimante','impression','pc','ordinateur','portable','ecran','écran','projecteur','scanner'],
            'types' => ['Imprimante','Poste de travail','Écran / Projecteur','Périphériques'],
        ],
        'enseignement' => [
            'syn' => ['laboratoire','tp','salle info','logiciel cours','licence','spss','autocad'],
            'types' => ['TP / Laboratoires','Logiciels enseignement','Salles informatiques'],
        ],
        'assistance' => [
            'syn' => ['aide','support','orientation','conseil'],
            'types' => ['Assistance générale','Conseil'],
        ],
    ];

    public function devinerCategorie(string $texte): ?string
    {
        $best = [null, 0.0];
        foreach ($this->map as $cat => $def) {
            foreach ($def['syn'] as $kw) {
                $score = Fuzzy::jaroWinkler($texte, $kw);
                if ($score > $best[1]) $best = [$cat, $score];
            }
        }
        return $best[1] >= 0.85 ? $best[0] : null;
    }

    public function typesPour(string $categorie): array
    {
        $k = strtolower($categorie);
        return $this->map[$k]['types'] ?? [];
    }
}
