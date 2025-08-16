<?php

namespace App\Services\Assistant;


class Catalogue
{
    public static function definitions(): array
    {
        return [
            'communication' => [
                'syn'   => ['mail','email','courriel','correo','messagerie','outlook','o365','teams','teams canal','canal teams','onedrive','partage de fichiers','fichiers partagés','drive','visio','réunion'],
                'types' => [
                    'Messagerie'         => ['mail','correo','enviar','recibir','boîte','buzón','quota','boîte pleine','buzón lleno'],
                    'Outils collaboratifs'=> ['teams','canal','onedrive','partage','fichiers','visio','réunion'],
                    'Calendrier'          => ['agenda','calendrier','invitation'],
                ],
            ],
            'acces' => [
                'syn'   => ['wifi','wi-fi','wlan','reseau','réseau','network','internet','lan','ethernet','vpn','anyconnect','globalprotect','login','authentification','mot de passe','contraseña','password','compte','cuenta','bloqué','bloqueado'],
                'types' => [
                    'Wi-Fi'         => ['wifi','ssid'],
                    'Réseau'                => ['reseau','réseau','internet','lan','ethernet','câble'],
                    'VPN'                   => ['vpn','anyconnect','globalprotect'],
                    'Compte / mot de passe' => ['compte','login','auth','mot de passe','password','contraseña','bloqué','bloqueado'],
                ],
            ],
            'plateformes' => [
                'syn'   => ['erp','moodle','siga','siges','webapp','plateforme','plataforma','sso','403','401'],
                'types' => [
                    'Accès'  => ['no entra','login','sso','403','401'],
                    'Erreur' => ['erreur','error','bug','exception'],
                ],
            ],
            'equipements' => [
                'syn'   => ['pc','ordinateur','ordenador','poste','portátil','laptop','imprimante','impresora','printer','périphérique','periférico','driver','pilote'],
                'types' => [
                    'Matériel défectueux' => ['panne','no enciende','no arranca','pantalla negra','se apaga','no funciona'],
                    'Périphériques'       => ['imprimante','impresora','scanner','webcam','clavier','souris'],
                    'Installation logiciel'=> ['installer','installation','driver','pilote','licence'],
                ],
            ],
            'enseignement' => [
                'syn'   => ['laboratoire','laboratorio','atelier','salle tp','tp','cours','classe'],
                'types' => [
                    'Salles de TP'            => ['salle tp','tp','atelier'],
                    'Logiciels de recherche'  => ['logiciel','licence','calcul','simulation'],
                ],
            ],
            'assistance' => [
                'syn'   => ['aide','helpdesk','support','soporte'],
                'types' => [
                    'Demande d’aide' => ['aide','soporte','support','helpdesk'],
                ],
            ],
        ];
    }

    public static function deviner(string $texte): array
    {
        $defs = self::definitions();
        $t = mb_strtolower($texte);
        $best = [null, null, 0.0];

        foreach ($defs as $cat => $def) {
            $scoreCat = 0.0;
            foreach (($def['syn'] ?? []) as $kw) {
                if (mb_strpos($t, mb_strtolower($kw)) !== false) { $scoreCat = max($scoreCat, 1.0); }
            }

            $bestType = [null, 0.0];
            foreach (($def['types'] ?? []) as $type => $kws) {
                foreach ($kws as $kw) {
                    if (mb_strpos($t, mb_strtolower($kw)) !== false) { $bestType = [$type, 1.0]; break; }
                }
                if ($bestType[1] === 1.0) break;
            }

            $score = $scoreCat + $bestType[1];
            if ($score > $best[2]) $best = [$cat, $bestType[0], $score];
        }

        return [
            'categorie' => $best[0],
            'type'      => $best[1],
        ];
    }
}
