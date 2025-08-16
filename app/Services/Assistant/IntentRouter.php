<?php
namespace App\Services\Assistant;

use App\Services\Assistant\Tools\IncidentTool;
use App\Services\Assistant\Tools\NotificationTool;
use App\Services\Assistant\Tools\ExportTool;
use App\Services\Assistant\ContactInfo;
use Illuminate\Support\Str;

class IntentRouter
{
    public function __construct(
        protected IncidentTool $incidents = new IncidentTool(),
        protected NotificationTool $notifs = new NotificationTool(),
        protected ExportTool $export = new ExportTool(),
        protected ContactInfo $contact = new ContactInfo(),
    ) {}

    public function traiter(array $intent, $user, DialogueState $state): array
    {
        $nom   = $intent['nom']  ?? 'fallback';
        $slots = $intent['slots']?? [];

        return match ($nom) {
            'faq.generale'               => $this->faq(),
            'contact.info'               => $this->contactInfo(),
            'incident.ouvrir.assiste'    => $this->ouvrirAssiste($slots),
            'incident.etat.demande'      => ['texte'=>"Indiquez le code public (INC-…).", 'suggestions'=>['Mes incidents']],
            'incident.etat'              => $this->incidentEtat($slots, $user),
            'incident.liste'             => $this->liste($slots['etat'] ?? null, $user),
            'incident.reouvrir.assiste'  => $this->reouvrirAssiste($slots['code'] ?? null, $user),
            'incident.rechercher'        => $this->rechercherIncidents($slots['q'] ?? '', $user),
            'incident.export.csv'        => [
                'texte' => "Export CSV prêt.",
                'actions' => [
                    ['label'=>"Télécharger CSV", 'href'=>$this->export->urlCsvUtilisateur()],
                ],
            ],
            'notifications.liste'        => $this->notifsListe($user),
            default                      => $this->fallback(),
        };
    }

    protected function faq(): array
    {
        return [
            'texte' => "Sélectionnez le problème dans le catalogue. Je vous indique la catégorie probable d’après votre texte.",
            'actions' => [
                ['label'=>'Ouvrir le catalogue','href'=>route('utilisateur.incidents.categories')],
                ['label'=>'Contact du support','command'=>'/contact'],
            ],
            'suggestions' => ['Ouvrir le catalogue','Mes incidents','Contact'],
        ];
    }

    protected function labelChemin(?string $cat, ?string $type): ?string
    {
        if (!$cat && !$type) return null;
        $cats = [
            'communication' => 'Communication et collaboration',
            'acces'         => 'Identification et accès',
            'plateformes'   => 'Plateformes web et services numériques',
            'equipements'   => 'Équipements et logiciels',
            'enseignement'  => 'Enseignement et recherche',
            'assistance'    => 'Assistance générale et demandes',
        ];
        $c = $cats[$cat] ?? null;
        if ($c && $type) return $c.' → '.$type;
        return $c ?: $type;
    }

    protected function ouvrirAssiste(array $slots): array
    {
        $cat  = $slots['categorie'] ?? null;
        $type = $slots['type'] ?? null;

        $chemin = $this->labelChemin($cat, $type);
        $ligne = "Sélectionnez le problème dans le catalogue.";
        if ($chemin) $ligne = "Sélectionnez le problème dans le catalogue. Probablement : ".$chemin.".";

        return [
            'texte' => $ligne,
            'actions' => [
                ['label'=>'Ouvrir le catalogue','href'=>route('utilisateur.incidents.categories')],
            ],
            'suggestions' => ['Ouvrir le catalogue','Mes incidents','Contact'],
        ];
    }

    protected function contactInfo(): array
    {
        $c = $this->contact->get();
        $pieces = [];
        if ($c['phone']) $pieces[] = "📞 ".$c['phone'];
        if ($c['email']) $pieces[] = "✉️ ".$c['email'];
        if ($c['hours']) $pieces[] = "🕘 ".$c['hours'];
        $txt = $pieces ? implode(" — ", $pieces) : "Contact indisponible.";

        $actions = [];
        if ($c['url']) {
            $actions[] = ['label' => 'Plus d’infos', 'href' => $c['url']];
        }

        return ['texte'=>$txt, 'actions'=>$actions];
    }

    protected function incidentEtat(array $slots, $user): array
    {
        $code = $slots['code'] ?? null;
        $id   = $slots['id']   ?? null;

        $data = null;
        if ($code) $data = $this->incidents->etatIncidentParCode($user, $code);
        elseif ($id) $data = $this->incidents->etatIncident($user, (int)$id);

        if (!$data) return ['texte'=>"Aucun incident correspondant pour votre compte.", 'suggestions'=>['Mes incidents']];

        $emoji = match (Str::lower($data['statut'])) {
            'en_cours' => '🟡',
            'resolu','résolu' => '🟢',
            'ferme','fermée'  => '⚪',
            default           => '🔵',
        };

        return [
            'texte' => "{$emoji} {$data['public']} — « {$data['titre']} ».",
            'actions' => [['label'=>'Ouvrir la fiche', 'href'=>route('utilisateur.incidents.show', $data['id'])]],
            'suggestions' => ['Mes incidents'],
        ];
    }

    protected function liste(?string $etat, $user): array
    {
        $arr = $this->incidents->lister($user, $etat ?: null, 8);
        if (!$arr) return ['texte'=>"Aucun incident à afficher.", 'suggestions'=>['Mes incidents']];

        $lignes = collect($arr)->map(function($i){
            $st = Str::lower($i['statut'] ?? '');
            $badge = match ($st) { 'en_cours'=>'🟡','resolu','résolu'=>'🟢','ferme','fermée'=>'⚪', default=>'🔵' };
            return "• {$badge} {$i['public']} — {$i['titre']}";
        })->implode("\n");

        return [
            'texte' => $lignes === '' ? "Aucun incident à afficher." : $lignes,
            'actions' => [['label'=>'Voir tous', 'href'=>route('utilisateur.incidents.index')]],
            'suggestions' => ['Mes incidents'],
        ];
    }

    protected function reouvrirAssiste(?string $code, $user): array
    {
        if (!$code) return ['texte'=>"Indiquez le code public (INC-…).", 'suggestions'=>['Mes incidents']];
        $data = $this->incidents->etatIncidentParCode($user, $code);
        if (!$data) return ['texte'=>"Je ne trouve pas {$code} pour votre compte.", 'suggestions'=>['Mes incidents']];

        return [
            'texte' => "Ouvrez la fiche {$data['public']} et choisissez « Non, le problème persiste ».",
            'actions' => [['label'=>'Ouvrir la fiche', 'href'=>route('utilisateur.incidents.show', $data['id'])]],
            'suggestions' => ['Mes incidents'],
        ];
    }

    protected function rechercherIncidents(string $q, $user): array
    {
        $q = trim($q);
        if ($q === '') return ['texte'=>"Que souhaitez-vous chercher ?", 'suggestions'=>['Mes incidents']];
        $arr = $this->incidents->rechercher($user, $q, 8);
        if (!$arr) return ['texte'=>"Aucun résultat.", 'suggestions'=>['Mes incidents']];
        $lignes = collect($arr)->map(fn($i)=>"• {$i['public']} — {$i['titre']}")->implode("\n");
        return [
            'texte' => $lignes,
            'actions' => [['label'=>'Voir mes incidents', 'href'=>route('utilisateur.incidents.index')]],
            'suggestions' => ['Mes incidents'],
        ];
    }

    protected function notifsListe($user): array
    {
        $arr = $this->notifs->lister($user);
        if (!$arr) return ['texte'=>"Aucune notification récente."];
        $lignes = collect($arr)->map(fn($n)=>"• ".($n['message'] ?: 'Notification')." (".$n['date'].")")->implode("\n");
        return ['texte' => $lignes];
    }

    protected function fallback(): array
    {
        return [
            'texte' => "Sélectionnez le problème dans le catalogue. Je vous indiquerai la catégorie probable.",
            'actions' => [
                ['label'=>'Ouvrir le catalogue','href'=>route('utilisateur.incidents.categories')],
                ['label'=>'Contact du support','command'=>'/contact'],
            ],
            'suggestions' => ['Ouvrir le catalogue','Mes incidents','Contact'],
        ];
    }
}
