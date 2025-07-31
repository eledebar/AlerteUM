<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Incident;
use App\Models\IncidentComment;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $admin = User::create(["name" => "Admin","email" => "admin@example.com","password" => Hash::make("admin123"),"role" => "admin"]);
        $admin2 = User::create(["name" => "Admin2","email" => "elen4cyber@gmail.com","password" => Hash::make("admin123"),"role" => "admin"]);

        $users = [];
        $users[0] = User::create([
            "name" => "User",
            "email" => "user@demo.com",
            "password" => Hash::make("test123"),
            "role" => "utilisateur",
        ]);
        $users[1] = User::create([
            "name" => "Eledebar",
            "email" => "eledebar@gmail.com",
            "password" => Hash::make("test123"),
            "role" => "utilisateur",
        ]);
        $users[2] = User::create([
            "name" => "Marie",
            "email" => "marie@demo.com",
            "password" => Hash::make("test123"),
            "role" => "utilisateur",
        ]);
        $users[3] = User::create([
            "name" => "Jean",
            "email" => "jean@demo.com",
            "password" => Hash::make("test123"),
            "role" => "utilisateur",
        ]);
        $users[4] = User::create([
            "name" => "Luc",
            "email" => "luc@demo.com",
            "password" => Hash::make("test123"),
            "role" => "utilisateur",
        ]);
        $users[5] = User::create([
            "name" => "Sophie",
            "email" => "sophie@demo.com",
            "password" => Hash::make("test123"),
            "role" => "utilisateur",
        ]);
        $users[6] = User::create([
            "name" => "Amine",
            "email" => "amine@demo.com",
            "password" => Hash::make("test123"),
            "role" => "utilisateur",
        ]);
        $users[7] = User::create([
            "name" => "Clara",
            "email" => "clara@demo.com",
            "password" => Hash::make("test123"),
            "role" => "utilisateur",
        ]);

        $incidents = [];
        $incidents[0] = Incident::create([
            "titre" => "Erreur lors de la validation du formulaire de stage",
            "description" => "Lorsqu'on valide le formulaire, un message d'erreur s'affiche sans raison apparente.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[0]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[1] = Incident::create([
            "titre" => "Lien cassé vers le relevé de notes",
            "description" => "Le lien présent sur le dashboard mène vers une erreur 404.",
            "statut" => "en_cours",
            "utilisateur_id" => $users[1]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[2] = Incident::create([
            "titre" => "Page de connexion au portail étudiant inaccessible",
            "description" => "Impossible d'accéder au portail étudiant depuis l'intranet.",
            "statut" => "résolu",
            "utilisateur_id" => $users[2]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[3] = Incident::create([
            "titre" => "Téléchargement du bulletin échoue sur mobile",
            "description" => "Sur téléphone, le bouton de téléchargement ne répond pas.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[3]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[4] = Incident::create([
            "titre" => "Bug lors de la sélection d’UE au second semestre",
            "description" => "Le menu déroulant ne charge pas toutes les unités d’enseignement proposées.",
            "statut" => "en_cours",
            "utilisateur_id" => $users[4]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[5] = Incident::create([
            "titre" => "Champ date non reconnu dans la demande de bourse",
            "description" => "Même au bon format, le champ date est refusé par le système.",
            "statut" => "résolu",
            "utilisateur_id" => $users[5]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[6] = Incident::create([
            "titre" => "Erreur 500 après soumission de la réinscription",
            "description" => "Après avoir rempli toutes les informations, une erreur serveur s’affiche.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[6]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[7] = Incident::create([
            "titre" => "Incohérence dans l’affichage du calendrier académique",
            "description" => "Les dates ne correspondent pas entre les semestres affichés.",
            "statut" => "en_cours",
            "utilisateur_id" => $users[7]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[8] = Incident::create([
            "titre" => "Accès refusé à la page des résultats",
            "description" => "Un message 'accès interdit' apparaît alors que l'étudiant est connecté.",
            "statut" => "résolu",
            "utilisateur_id" => $users[0]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[9] = Incident::create([
            "titre" => "Déconnexion intempestive sur la plateforme Moodle",
            "description" => "La session se ferme toute seule après quelques minutes d’utilisation.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[1]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[10] = Incident::create([
            "titre" => "Erreur lors de la validation du formulaire de stage",
            "description" => "Lorsqu'on valide le formulaire, un message d'erreur s'affiche sans raison apparente.",
            "statut" => "en_cours",
            "utilisateur_id" => $users[2]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[11] = Incident::create([
            "titre" => "Lien cassé vers le relevé de notes",
            "description" => "Le lien présent sur le dashboard mène vers une erreur 404.",
            "statut" => "résolu",
            "utilisateur_id" => $users[3]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[12] = Incident::create([
            "titre" => "Page de connexion au portail étudiant inaccessible",
            "description" => "Impossible d'accéder au portail étudiant depuis l'intranet.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[4]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[13] = Incident::create([
            "titre" => "Téléchargement du bulletin échoue sur mobile",
            "description" => "Sur téléphone, le bouton de téléchargement ne répond pas.",
            "statut" => "en_cours",
            "utilisateur_id" => $users[5]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[14] = Incident::create([
            "titre" => "Bug lors de la sélection d’UE au second semestre",
            "description" => "Le menu déroulant ne charge pas toutes les unités d’enseignement proposées.",
            "statut" => "résolu",
            "utilisateur_id" => $users[6]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[15] = Incident::create([
            "titre" => "Champ date non reconnu dans la demande de bourse",
            "description" => "Même au bon format, le champ date est refusé par le système.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[7]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[16] = Incident::create([
            "titre" => "Erreur 500 après soumission de la réinscription",
            "description" => "Après avoir rempli toutes les informations, une erreur serveur s’affiche.",
            "statut" => "en_cours",
            "utilisateur_id" => $users[0]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[17] = Incident::create([
            "titre" => "Incohérence dans l’affichage du calendrier académique",
            "description" => "Les dates ne correspondent pas entre les semestres affichés.",
            "statut" => "résolu",
            "utilisateur_id" => $users[1]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[18] = Incident::create([
            "titre" => "Accès refusé à la page des résultats",
            "description" => "Un message 'accès interdit' apparaît alors que l'étudiant est connecté.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[2]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[19] = Incident::create([
            "titre" => "Déconnexion intempestive sur la plateforme Moodle",
            "description" => "La session se ferme toute seule après quelques minutes d’utilisation.",
            "statut" => "en_cours",
            "utilisateur_id" => $users[3]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[20] = Incident::create([
            "titre" => "Erreur lors de la validation du formulaire de stage",
            "description" => "Lorsqu'on valide le formulaire, un message d'erreur s'affiche sans raison apparente.",
            "statut" => "résolu",
            "utilisateur_id" => $users[4]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[21] = Incident::create([
            "titre" => "Lien cassé vers le relevé de notes",
            "description" => "Le lien présent sur le dashboard mène vers une erreur 404.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[5]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[22] = Incident::create([
            "titre" => "Page de connexion au portail étudiant inaccessible",
            "description" => "Impossible d'accéder au portail étudiant depuis l'intranet.",
            "statut" => "en_cours",
            "utilisateur_id" => $users[6]->id,
            "attribue_a" => $admin->id,
        ]);
        $incidents[23] = Incident::create([
            "titre" => "Téléchargement du bulletin échoue sur mobile",
            "description" => "Sur téléphone, le bouton de téléchargement ne répond pas.",
            "statut" => "résolu",
            "utilisateur_id" => $users[7]->id,
            "attribue_a" => $admin2->id,
        ]);
        $incidents[24] = Incident::create([
            "titre" => "Bug lors de la sélection d’UE au second semestre",
            "description" => "Le menu déroulant ne charge pas toutes les unités d’enseignement proposées.",
            "statut" => "nouveau",
            "utilisateur_id" => $users[0]->id,
            "attribue_a" => $admin->id,
        ]);

        IncidentComment::create([
            "incident_id" => $incidents[0]->id,
            "user_id" => $admin->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[0]->id,
            "user_id" => $admin->id,
            "commentaire" => "Votre demande est bien enregistrée, traitement à venir.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[1]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Nous avons identifié l’origine du problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[2]->id,
            "user_id" => $admin->id,
            "commentaire" => "La page fonctionne désormais normalement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[3]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[4]->id,
            "user_id" => $admin->id,
            "commentaire" => "Nous avons identifié l’origine du problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[5]->id,
            "user_id" => $admin2->id,
            "commentaire" => "La page fonctionne désormais normalement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[6]->id,
            "user_id" => $admin->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[7]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Nous avons identifié l’origine du problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[7]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Le correctif est en cours de développement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[8]->id,
            "user_id" => $admin->id,
            "commentaire" => "La page fonctionne désormais normalement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[9]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[10]->id,
            "user_id" => $admin->id,
            "commentaire" => "Nous avons identifié l’origine du problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[11]->id,
            "user_id" => $admin2->id,
            "commentaire" => "La page fonctionne désormais normalement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[12]->id,
            "user_id" => $admin->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[13]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Nous avons identifié l’origine du problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[14]->id,
            "user_id" => $admin->id,
            "commentaire" => "La page fonctionne désormais normalement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[14]->id,
            "user_id" => $admin->id,
            "commentaire" => "Le bug a été corrigé dans la mise à jour du matin.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[15]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[16]->id,
            "user_id" => $admin->id,
            "commentaire" => "Nous avons identifié l’origine du problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[17]->id,
            "user_id" => $admin2->id,
            "commentaire" => "La page fonctionne désormais normalement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[18]->id,
            "user_id" => $admin->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[19]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Nous avons identifié l’origine du problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[20]->id,
            "user_id" => $admin->id,
            "commentaire" => "La page fonctionne désormais normalement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[21]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[21]->id,
            "user_id" => $admin2->id,
            "commentaire" => "Votre demande est bien enregistrée, traitement à venir.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[22]->id,
            "user_id" => $admin->id,
            "commentaire" => "Nous avons identifié l’origine du problème.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[23]->id,
            "user_id" => $admin2->id,
            "commentaire" => "La page fonctionne désormais normalement.",
        ]);
        IncidentComment::create([
            "incident_id" => $incidents[24]->id,
            "user_id" => $admin->id,
            "commentaire" => "Merci pour votre signalement, nous analysons le problème.",
        ]);
    }
}