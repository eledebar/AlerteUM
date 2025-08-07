<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Incident;
use App\Models\IncidentComment;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $resolveur = User::updateOrCreate(
            ['email' => 'resolveur@example.com'],
            [
                "name" => "Resolveur1",
                "password" => Hash::make("resol123"),
                "role" => "resolveur"
            ]
        );

        $resolveur2 = User::updateOrCreate(
            ['email' => 'elen4cyber@gmail.com'],
            [
                "name" => "Resolveur2",
                "password" => Hash::make("resol123"),
                "role" => "resolveur"
            ]
        );

        $emails = [
            ["User", "user@demo.com"],
            ["Eledebar", "eledebar@gmail.com"],
            ["Marie", "marie@demo.com"],
            ["Jean", "jean@demo.com"],
            ["Luc", "luc@demo.com"],
            ["Sophie", "sophie@demo.com"],
            ["Amine", "amine@demo.com"],
            ["Clara", "clara@demo.com"]
        ];

        $users = [];

        foreach ($emails as $i => [$name, $email]) {
            $users[$i] = User::updateOrCreate(
                ['email' => $email],
                [
                    "name" => $name,
                    "password" => Hash::make("test123"),
                    "role" => "utilisateur"
                ]
            );
        }

        $data = [
            ["Accès à la base Scopus indisponible", "L'accès à la base de données Scopus échoue malgré une connexion VPN.", 'enseignement', 'Accès à bases de données'],
            ["Mot de passe refusé malgré réinitialisation", "Après réinitialisation, le mot de passe n’est toujours pas accepté.", 'acces', 'Problèmes de mot de passe'],
            ["Erreur sur le drive partagé du département", "Impossible d'accéder aux documents partagés sur le drive pédagogique.", 'communication', 'Outils collaboratifs'],
            ["Site Moodle inaccessible depuis campus", "Tentative de connexion à Moodle échoue systématiquement depuis le campus.", 'plateformes', 'Sites web universitaires'],
            ["Logiciel statistique non installé", "SPSS n’est pas disponible sur les ordinateurs du labo de psycho.", 'equipements', 'Logiciels manquants'],
            ["Calendrier non synchronisé sur téléphone", "Le calendrier universitaire ne se synchronise pas avec l’appli mobile.", 'communication', 'Outils collaboratifs'],
            ["Connexion Wi-Fi instable bâtiment D", "La connexion saute toutes les 5 minutes au 3ème étage du bâtiment D.", 'acces', 'Connexion Internet'],
            ["Demande d’aide pour configurer Zoom", "Je souhaite utiliser Zoom pour mon cours mais je n’arrive pas à l’installer.", 'assistance', 'Orientation numérique'],
            ["Erreur lors du dépôt d’un formulaire Erasmus", "Le bouton de soumission du formulaire Erasmus ne réagit pas.", 'plateformes', 'Formulaires en ligne'],
            ["Problème de licence sur AutoCAD", "AutoCAD signale que la licence a expiré lors du lancement.", 'equipements', 'Problème de licence'],
            ["Besoin d’assistance pour Moodle", "Je ne comprends pas comment rendre un devoir sur Moodle.", 'assistance', 'Demande d’assistance'],
            ["Scanner de la bibliothèque HS", "Le scanner près de la salle informatique ne fonctionne plus.", 'equipements', 'Matériel défectueux'],
            ["Accès refusé à ScienceDirect", "Message d’erreur lors de la tentative d'accès à ScienceDirect.", 'enseignement', 'Accès à bases de données'],
            ["Impossible de modifier mon mot de passe ENT", "Le formulaire de changement de mot de passe ne fonctionne pas.", 'acces', 'Problèmes de mot de passe'],
            ["Documents introuvables sur Teams", "Les fichiers de TD ne sont pas visibles sur Teams.", 'communication', 'Outils collaboratifs'],
            ["Formulaire de stage vide après chargement", "Aucune donnée ne s'affiche dans le formulaire de stage.", 'plateformes', 'Formulaires en ligne'],
            ["Photos floues sur le portail étudiant", "Les images affichées sur le portail sont pixelisées.", 'plateformes', 'Sites web universitaires'],
            ["Aide pour installation VPN", "Je ne sais pas comment installer le VPN de l’université.", 'assistance', 'Orientation numérique'],
            ["Erreur 403 sur le drive partagé", "Accès refusé à certains dossiers partagés sur le drive.", 'communication', 'Outils collaboratifs'],
            ["Connexion Ethernet absente salle B12", "Impossible de se connecter via câble réseau en salle B12.", 'acces', 'Connexion Internet'],
            ["Bug lors de l'enregistrement des voeux", "Les choix de spécialité ne sont pas enregistrés sur le formulaire.", 'plateformes', 'Formulaires en ligne'],
            ["Logiciel de gestion non activé", "Sage ne démarre pas, message ‘licence invalide’.", 'equipements', 'Problème de licence'],
            ["Imprimante défectueuse en salle profs", "L’imprimante affiche une erreur permanente de bourrage papier.", 'equipements', 'Matériel défectueux'],
            ["VPN coupe les connexions au bout de 10 min", "Déconnexion automatique du VPN sans message d’erreur.", 'acces', 'Connexion Internet'],
            ["Je ne comprends pas le fonctionnement de l’ENT", "Besoin d’aide pour naviguer sur le portail ENT.", 'assistance', 'Demande d’assistance'],
        ];

        foreach ($data as $i => [$titre, $description, $categorie, $type]) {
            $user = $users[$i % count($users)];
            $resolveurRef = $i % 2 === 0 ? $resolveur : $resolveur2;
            $statuts = ['nouveau', 'en_cours', 'résolu'];

            $incident = Incident::create([
                'titre' => $titre,
                'description' => $description,
                'categorie' => $categorie,
                'type' => $type,
                'statut' => $statuts[$i % 3],
                'utilisateur_id' => $user->id,
                'attribue_a' => $resolveurRef->id,
            ]);

            $commentaires = [
                "Merci pour votre signalement, nous analysons le problème.",
                $i % 3 === 0
                    ? "Le problème a été transmis à l’équipe concernée."
                    : ($i % 3 === 1
                        ? "Une solution est en cours de test."
                        : "Le dysfonctionnement est maintenant résolu.")
            ];

            foreach ($commentaires as $commentaire) {
                IncidentComment::create([
                    'incident_id' => $incident->id,
                    'user_id' => $resolveurRef->id,
                    'commentaire' => $commentaire,
                ]);
            }
        }
    }
}
