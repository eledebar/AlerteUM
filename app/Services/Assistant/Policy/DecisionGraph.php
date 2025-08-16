<?php
namespace App\Services\Assistant\Policy;

use App\Services\Assistant\DialogueState;
use App\Services\Assistant\Tools\CategoryTool;

class DecisionGraph
{
    public function __construct(protected CategoryTool $categories = new CategoryTool()) {}

    public function ouvrirFlow(DialogueState $state): array
    {
        $slots = &$state->slots['ouvrir'];
        if (!$slots['categorie']) {
            return [
                'ask' => 'Dans quelle catégorie se situe votre problème ? (communication, accès, plateformes, équipements, enseignement, assistance)',
                'suggestions' => ['Accès (wifi)','Communication (mail)','Plateformes (Moodle)','Équipements (imprimante)','Enseignement','Assistance'],
            ];
        }
        $types = $this->categories->typesPour($slots['categorie']);
        if (!$slots['type']) {
            $chips = array_map(fn($t)=>['label'=>$t,'payload'=>$t], $types);
            return [
                'ask' => 'Précisez le type de problème.',
                'chips' => $chips,
            ];
        }
        if (!$slots['description']) {
            return ['ask'=>'Décrivez brièvement le problème pour compléter le ticket.'];
        }
        $url = route('utilisateur.incidents.create') . '?categorie=' . urlencode($slots['categorie']) . '&type=' . urlencode($slots['type']) . '&description=' . urlencode($slots['description']);
        return [
            'final' => true,
            'texte' => 'Parfait, ouvrez le formulaire pour terminer.',
            'actions' => [['label'=>'Créer maintenant','href'=>$url]],
        ];
    }

    public function reouvrirFlow(DialogueState $state): array
    {
        $slots = &$state->slots['reouvrir'];
        if (!$slots['id']) {
            return ['ask'=>"Quel est l’identifiant de l’incident à réouvrir ?", 'suggestions'=>['Réouvrir 18']];
        }
        if (!$slots['motif']) {
            return ['ask'=>"Indiquez le motif de la réouverture (ex: « toujours pas résolu »)."];
        }
        return [
            'final' => true,
            'texte' => 'D’accord. Ouvrez la fiche et utilisez le bouton « Réouvrir ».',
        ];
    }
}
