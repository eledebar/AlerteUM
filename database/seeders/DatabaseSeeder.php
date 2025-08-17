<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Incident;
use App\Models\IncidentComment;
use App\Models\IncidentLog;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        $resolveur = User::updateOrCreate(
            ['email' => 'elen4cyber@gmail.com'],
            ['name' => 'Support Elena', 'password' => Hash::make('resol123'), 'role' => 'resolveur']
        );

        $supports = [$resolveur];
        for ($k = 1; $k <= 10; $k++) {
            $pad = str_pad((string)$k, 3, '0', STR_PAD_LEFT);
            $supports[] = User::updateOrCreate(
                ['email' => "support{$pad}@example.com"],
                ['name' => "Support {$pad}", 'password' => Hash::make('resol123'), 'role' => 'resolveur']
            );
        }

        $utilisateurs = [];
        $utilisateurs[] = User::updateOrCreate(
            ['email' => 'user@demo.com'],
            ['name' => 'User', 'password' => Hash::make('test123'), 'role' => 'utilisateur']
        );
        $eledebar = User::updateOrCreate(
            ['email' => 'eledebar@gmail.com'],
            ['name' => 'Eledebar', 'password' => Hash::make('test123'), 'role' => 'utilisateur']
        );
        $utilisateurs[] = $eledebar;
        for ($u = 1; $u <= 10; $u++) {
            $pad = str_pad((string)$u, 3, '0', STR_PAD_LEFT);
            $utilisateurs[] = User::updateOrCreate(
                ['email' => "utilisateur{$pad}@demo.com"],
                ['name' => "Utilisateur {$pad}", 'password' => Hash::make('test123'), 'role' => 'utilisateur']
            );
        }

        $lignes = [
            ['Accès à la base Scopus indisponible', "L'accès à la base de données Scopus échoue malgré une connexion VPN.", 'enseignement', 'Accès à bases de données'],
            ['Mot de passe refusé malgré réinitialisation', "Après réinitialisation, le mot de passe n’est toujours pas accepté.", 'acces', 'Problèmes de mot de passe'],
            ["Erreur sur le drive partagé du département", "Impossible d'accéder aux documents partagés.", 'communication', 'Outils collaboratifs'],
            ['Site Moodle inaccessible depuis campus', "Moodle inaccessbile depuis le campus.", 'plateformes', 'Sites web universitaires'],
            ['Logiciel statistique non installé', "SPSS indisponible dans le labo.", 'equipements', 'Logiciels manquants'],
            ['Calendrier non synchronisé', "Le calendrier ne se synchronise pas.", 'communication', 'Outils collaboratifs'],
            ['Connexion Wi-Fi instable', "La connexion saute régulièrement.", 'acces', 'Connexion Internet'],
            ["Aide pour Zoom", "Besoin d’aide pour installer Zoom.", 'assistance', 'Orientation numérique'],
            ["Erreur formulaire Erasmus", "Le bouton de soumission ne réagit pas.", 'plateformes', 'Formulaires en ligne'],
            ['Licence AutoCAD expirée', "AutoCAD indique une licence expirée.", 'equipements', 'Problème de licence'],
            ["Assistance Moodle", "Comment rendre un devoir sur Moodle ?", 'assistance', 'Demande d’assistance'],
        ];

        $delaisHeures = ['low'=>72,'medium'=>48,'high'=>8,'critical'=>4];
        $priorites = ['low','medium','high','critical'];

        foreach ($lignes as $i => [$titre, $description, $categorie, $type]) {
            $user = $utilisateurs[$i % count($utilisateurs)];
            $res  = $supports[$i % count($supports)];
            $prio = $priorites[$i % count($priorites)];

            $statut = match (true) {
                $i % 17 === 0 => 'fermé',
                $i % 11 === 0 => 'résolu',
                $i % 5 === 0  => 'nouveau',
                default       => 'en_cours',
            };

            $hours = $delaisHeures[$prio];
            $ok = ($i % 3 !== 0);
            $cree = $ok ? now()->subHours(intval($hours/2) + ($i%6)) : now()->subHours($hours + 2 + ($i%6));
            $due  = (clone $cree)->addHours($hours);

            $incident = Incident::create([
                'titre' => $titre,
                'description' => $description,
                'categorie' => $categorie,
                'type' => $type,
                'statut' => 'nouveau',
                'priority' => $prio,
                'utilisateur_id' => $user->id,
                'attribue_a' => $res->id,
                'sla_due_at' => in_array($statut, ['résolu','resolu','fermé','ferme']) ? null : $due,
            ]);

            $incident->created_at = $cree;
            $incident->updated_at = (clone $cree)->addMinutes(2);
            $incident->save();

            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => $user->id,
                'action'      => 'created',
                'details'     => 'Création par l’utilisateur',
            ]);

            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => $res->id,
                'action'      => 'assigned',
                'details'     => 'Pris en charge par '.$res->name,
            ]);

            if ($statut !== 'nouveau') {
                IncidentLog::create([
                    'incident_id' => $incident->id,
                    'user_id'     => $res->id,
                    'action'      => 'status_changed',
                    'from_status' => 'nouveau',
                    'to_status'   => $statut,
                    'details'     => '',
                ]);
                $incident->statut = $statut;
                $incident->save();
            }

            IncidentComment::create([
                'incident_id' => $incident->id,
                'user_id'     => $res->id,
                'commentaire' => "Prise en charge par {$res->name}.",
            ]);

            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => $res->id,
                'action'      => 'comment_added',
                'details'     => "Commentaire ajouté",
            ]);
        }

        $sujets = [
            ['Problème ENT', 'assistance', 'Demande d’assistance'],
            ['Connexion Wi-Fi', 'acces', 'Connexion Internet'],
            ['Drive partagé', 'communication', 'Outils collaboratifs'],
            ['Erreur formulaire', 'plateformes', 'Formulaires en ligne'],
            ['Licence invalide', 'equipements', 'Problème de licence'],
            ['Accès base', 'enseignement', 'Accès à bases de données'],
        ];

        for ($i = 1; $i <= 30; $i++) {
            [$titreBase, $cat, $type] = $sujets[$i % count($sujets)];
            $titre = $titreBase;
            $res = $supports[$i % count($supports)];
            $prio = $priorites[$i % count($priorites)];
            $statut = $i % 9 === 0 ? 'résolu' : ($i % 14 === 0 ? 'fermé' : 'en_cours');

            $hours = $delaisHeures[$prio];
            $cree = now()->subHours(($hours/2) + ($i%5));
            $due  = (clone $cree)->addHours($hours);

            $incident = Incident::create([
                'titre' => $titre,
                'description' => 'Incident généré pour démonstration.',
                'categorie' => $cat,
                'type' => $type,
                'statut' => 'nouveau',
                'priority' => $prio,
                'utilisateur_id' => $eledebar->id,
                'attribue_a' => $res->id,
                'sla_due_at' => in_array($statut, ['résolu','resolu','fermé','ferme']) ? null : $due,
            ]);

            $incident->created_at = $cree;
            $incident->updated_at = (clone $cree)->addMinutes(3);
            $incident->save();

            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => $eledebar->id,
                'action'      => 'created',
                'details'     => 'Création par l’utilisateur',
            ]);

            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => $res->id,
                'action'      => 'assigned',
                'details'     => 'Pris en charge par '.$res->name,
            ]);

            if ($statut !== 'nouveau') {
                IncidentLog::create([
                    'incident_id' => $incident->id,
                    'user_id'     => $res->id,
                    'action'      => 'status_changed',
                    'from_status' => 'nouveau',
                    'to_status'   => $statut,
                    'details'     => '',
                ]);
                $incident->statut = $statut;
                $incident->save();
            }

            IncidentComment::create([
                'incident_id' => $incident->id,
                'user_id'     => $res->id,
                'commentaire' => "Suivi par {$res->name}.",
            ]);

            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => $res->id,
                'action'      => 'comment_added',
                'details'     => "Commentaire ajouté",
            ]);
        }
    }
}
