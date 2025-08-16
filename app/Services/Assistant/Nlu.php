<?php
namespace App\Services\Assistant;

use App\Services\Assistant\DialogueState;

class Nlu
{
    protected array $lex = [
        'help'   => ['help','aide','ayuda','cómo','como','comment','que faire','qué puedo'],
        'csv'    => ['csv','export','exporter','excel','descargar','exportación'],
        'notifs' => ['notif','notifs','notifications','avisos','alertas'],
        'liste'  => ['liste','listar','listado','mes incidents','mis incidentes','mes tickets','mis tickets','mes cas','mis casos'],
        'ouvert' => ['ouvert','ouvertes','abiertos','abierto','en cours','pendiente','pendientes','actifs','actives'],

        'ouvrir'    => ['ouvrir','créer','creer','déclarer','declarer','abrir','crear','reportar','nuevo ticket','nuevo caso'],
        'reouvrir'  => ['reouvrir','réouvrir','rouvrir','reabrir','volver a abrir','rechazar cierre','no resuelto','persiste'],
        'fermer'    => ['fermer','clore','clôturer','cerrar','marcar como resuelto'],

        'etat'      => ['etat','état','estado','statut','status','vérifier','verifier','consulter','check status','suivi','seguimiento','estado del ticket','etat du ticket'],
        'catalogue' => ['catalogue','catálogo','catalogo','categoria','categoría','choisir la catégorie','seleccionar categoria'],

        'contact'   => ['contact','contacto','téléphone','telefono','teléfono','mail','correo','email','support','soporte','assistance','helpdesk','horaires','horario','dsi','ti','informatique'],

        'wifi'      => ['wifi','wi-fi','wlan','ssid','hotspot'],
        'reseau'    => ['réseau','reseau','network','internet','lan','ethernet','cable','câble','switch','router','routeur','conexion','conexión'],
        'vpn'       => ['vpn','anyconnect','globalprotect','forticlient','openvpn'],
        'mail'      => ['mail','email','courriel','correo','outlook','o365','office 365','exchange','imap','smtp','pop'],
        'imprim'    => ['imprimante','impresora','printer','imprimir','print','spooler','toner','tóner','papier','papel'],
        'compte'    => ['compte','cuenta','login','connexion','conexión','acceso','sso','authentification','autenticación','password','mot de passe','contraseña','bloqué','bloqueado','mfa','otp'],
        'poste'     => ['poste','pc','ordinateur','ordenador','equipo','portátil','laptop','desktop','mac','macbook','windows','linux'],
        'app'       => ['app','appli','aplicación','application','erp','moodle','siga','siges','webapp','plataforma','plateforme','portal','intranet','formulaire','formulario'],
        'video'     => ['teams','zoom','meet','webex','visio','videoconférence','videoconferencia'],
        'stockage'  => ['onedrive','drive','google drive','sharepoint','partage','compartido','nas','serveur de fichiers','servidor de archivos'],
        'lenteur'   => ['lent','lento','slow','ralenti','lag','retardo'],
        'panne'     => ['panne','caído','down','apagado','no funciona','no enciende','se apaga','hors ligne','offline','indisponible','timeout'],
        'erreur'    => ['erreur','error','bug','exception','code erreur','código de error','bsod','crash'],
        'bloquant'  => ['bloquant','bloqueante','critique','crítico','urgente'],

        'licence'   => ['licence','licencia','activation','réactivation','product key','clave'],
        'camera'    => ['caméra','camara','webcam','micro','microphone','audio'],
        'proj'      => ['vidéoprojecteur','proyector','hdmi','vga','écran externe','pantalla externa'],
        'labo'      => ['laboratoire','laboratorio','salle tp','tp','atelier','prácticas'],
        'base'      => ['base de données','base de datos','revues','ressource électronique','recurso electrónico'],
        'quota'     => ['quota','boîte pleine','buzón lleno','espacio lleno','espacio insuficiente'],
        'url'       => ['http://','https://','www.'],

        'web_site'  => ['web','site','sitio','página','pagina','portal','intranet','web institucional','sitio institucional','sede electrónica','campus virtual'],
        'web_down'  => ['caida','caído','está caida','esta caida','no carga','no abre','no entra','fuera de servicio','no disponible','hors ligne','down','offline','indisponible','timeout','no responde'],
        'http_codes'=> ['404','500','502','503','504','bad gateway','service unavailable','not found'],

        'logiciels' => [
            'autocad','revit','civil 3d','navisworks','3ds max','inventor','solidworks','ansys','catia','rhino','sketchup',
            'arcgis','qgis','matlab','simulink','octave','spss','stata','rstudio','python','anaconda',
            'photoshop','illustrator','indesign','premiere','after effects','acrobat','lightroom',
            'office','word','excel','powerpoint','onenote','outlook'
        ],
        'install'   => ['installer','installation','setup','instalar','desplegar','deploy'],
    ];

    protected function norm(string $s): string
    {
        $s = mb_strtolower(trim($s));
        return preg_replace('/\s+/u',' ', $s) ?? '';
    }

    protected function has(string $text, string $clé): bool
    {
        $text = $this->norm($text);
        foreach ($this->lex[$clé] ?? [] as $syn) if (mb_strpos($text, $syn) !== false) return true;
        return false;
    }

    protected function any(string $text, array $words): bool
    {
        $t = $this->norm($text);
        foreach ($words as $w) if (mb_strpos($t, mb_strtolower($w)) !== false) return true;
        return false;
    }

    protected function extraireCodeIncident(string $n): ?string
    {
        if (preg_match('/\binc[\s\-]?(\d{8}\-\d{4}|\d{1,6})\b/i', $n, $m)) {
            $core = strtoupper($m[1]);
            if (preg_match('/^\d{8}\-\d{4}$/', $core)) return 'INC-'.$core;
            $digits = ltrim($core,'0'); if ($digits==='') $digits='0';
            return 'INC-'.str_pad($digits,4,'0',STR_PAD_LEFT);
        }
        if (preg_match('/\bINC\-\d{8}\-\d{4}\b/i', $n, $m)) return strtoupper($m[0]);
        if (preg_match('/\bINC\-\d{4,6}\b/i', $n, $m)) return strtoupper($m[0]);
        return null;
    }

    protected function extraireIdInterne(string $n): ?int
    {
        if (preg_match('/\b(\d{1,10})\b/u', $n, $m)) return (int)$m[1];
        return null;
    }

    protected function devinerParHeuristique(string $t): ?array
    {
        $site = $this->has($t,'web_site');
        $siteDown = $site && ($this->has($t,'web_down') || $this->has($t,'http_codes'));
        $connect = $this->has($t,'wifi') || $this->has($t,'reseau') || preg_match('/\b(sin|sans)\s+internet\b/u',$t);

        if ($siteDown && !$connect) return ['categorie'=>'plateformes','type'=>'Sites web'];
        if ($site && preg_match('/formul(air|ario)|soumission|envoi|submit|captcha|validation/u',$t)) return ['categorie'=>'plateformes','type'=>'Formulaires en ligne'];
        if ($site && (preg_match('/\b(403|401)\b/',$t) || preg_match('/\b(login|sso|identificacion|identification)\b/u',$t))) return ['categorie'=>'plateformes','type'=>'Accès'];

        if ($connect && !$site) return ['categorie'=>'acces','type'=>'Connexion Internet'];
        if ($this->has($t,'vpn')) return ['categorie'=>'acces','type'=>'VPN'];
        if ($this->has($t,'compte')) return ['categorie'=>'acces','type'=>'Compte / mot de passe'];

        if ($this->any($t,$this->lex['logiciels']) && $this->has($t,'licence')) return ['categorie'=>'equipements','type'=>'Problème de licence'];
        if ($this->any($t,$this->lex['logiciels']) || $this->has($t,'install')) return ['categorie'=>'equipements','type'=>'Installation logiciel'];

        if ($this->has($t,'imprim')) return ['categorie'=>'equipements','type'=>'Périphériques'];
        if ($this->has($t,'poste')) return ['categorie'=>'equipements','type'=>'Matériel défectueux'];

        return null;
    }

    protected function devinerCategorieType(string $texte): array
    {
        $t = mb_strtolower($texte);
        if ($hit = $this->devinerParHeuristique($t)) return $hit;

        $cats = [
            'communication' => [
                'syn' => array_merge($this->lex['mail'],$this->lex['stockage'],$this->lex['video'],$this->lex['quota']),
                'types' => [
                    'Messagerie' => array_merge($this->lex['mail'],$this->lex['quota']),
                    'Outils collaboratifs' => array_merge($this->lex['stockage'],$this->lex['video']),
                    'Calendrier' => ['agenda','calendrier','invitation','cita','evento'],
                ],
            ],
            'acces' => [
                'syn' => array_merge($this->lex['wifi'],$this->lex['reseau'],$this->lex['vpn'],$this->lex['compte']),
                'types' => [
                    'Connexion Internet' => array_merge($this->lex['wifi'],$this->lex['reseau'],$this->lex['lenteur'],$this->lex['panne']),
                    'VPN' => $this->lex['vpn'],
                    'Compte / mot de passe' => $this->lex['compte'],
                ],
            ],
            'plateformes' => [
                'syn' => array_merge($this->lex['app'],$this->lex['url'],$this->lex['web_site']),
                'types' => [
                    'Accès' => array_merge(['403','401','login','sso'], $this->lex['compte']),
                    'Erreur' => $this->lex['erreur'],
                    'Formulaires en ligne' => ['formulaire','formulario','soumission','envoi','submit','captcha','validation'],
                    'Sites web' => array_merge($this->lex['web_site'],$this->lex['web_down'],$this->lex['http_codes']),
                ],
            ],
            'equipements' => [
                'syn' => array_merge($this->lex['poste'],$this->lex['imprim'],$this->lex['proj'],$this->lex['camera'],$this->lex['logiciels'],$this->lex['licence'],$this->lex['install']),
                'types' => [
                    'Matériel défectueux' => array_merge($this->lex['panne'],$this->lex['erreur'],$this->lex['poste']),
                    'Périphériques' => array_merge($this->lex['imprim'],$this->lex['camera'],$this->lex['proj']),
                    'Installation logiciel' => array_merge($this->lex['install'],$this->lex['logiciels']),
                    'Problème de licence' => $this->lex['licence'],
                ],
            ],
            'enseignement' => [
                'syn' => array_merge($this->lex['labo'],$this->lex['proj']),
                'types' => [
                    'Équipements de labo' => $this->lex['labo'],
                    'Salles de TP' => ['salle tp','tp','atelier'],
                    'Logiciels de recherche' => ['simulation','calcul','matlab','simulink','octave','spss','stata','python','r','rstudio'],
                    'Accès à bases de données' => array_merge($this->lex['base'], $this->lex['vpn']),
                ],
            ],
            'assistance' => [
                'syn' => ['aide','support','soporte','helpdesk','accompagnement','formation','orientation'],
                'types' => [
                    'Demande d’assistance' => ['aide','support','helpdesk'],
                    'Orientation numérique' => ['accompagnement','formation','onboarding'],
                    'Autres demandes' => ['autre','otro','divers','general'],
                ],
            ],
        ];

        $bestCat = null; $bestType = null; $bestScore = -1;
        foreach ($cats as $cat => $def) {
            $scCat = 0;
            foreach ($def['syn'] as $kw) if (mb_strpos($t, mb_strtolower($kw)) !== false) { $scCat = 1; break; }
            $typeSel = null; $scType = 0;
            foreach ($def['types'] as $type => $kws) {
                foreach ($kws as $kw) if (mb_strpos($t, mb_strtolower($kw)) !== false) { $typeSel = $type; $scType = 1; break; }
                if ($scType) break;
            }
            $score = $scCat + $scType;
            if ($score > $bestScore) { $bestScore = $score; $bestCat = $cat; $bestType = $typeSel; }
        }
        if (!$bestCat && $this->any($t,$this->lex['logiciels'])) { $bestCat = 'equipements'; $bestType = $bestType ?: 'Installation logiciel'; }
        return ['categorie'=>$bestCat,'type'=>$bestType];
    }

    public function detecter(string $texte, $user, DialogueState $state): array
    {
        $orig = $texte;
        $n = $this->norm($texte);

        if (preg_match('/^\/(help|etat|rechercher|csv|notifs|liste|reouvrir|ouvrir|contact)\b(.*)$/u', $n, $m)) {
            $cmd = $m[1]; $arg = trim($m[2] ?? '');
            return match ($cmd) {
                'help'      => ['nom'=>'faq.generale','slots'=>[]],
                'etat'      => $this->parseEtat($arg ?: $n),
                'rechercher'=> ['nom'=>'incident.rechercher','slots'=>['q'=>$arg]],
                'csv'       => ['nom'=>'incident.export.csv','slots'=>[]],
                'notifs'    => ['nom'=>'notifications.liste','slots'=>[]],
                'liste'     => ['nom'=>'incident.liste','slots'=>['etat'=>($this->has($arg,'ouvert')?'ouvert':null)]],
                'reouvrir'  => ['nom'=>'incident.reouvrir.assiste','slots'=>['code'=>$this->extraireCodeIncident($arg)]],
                'ouvrir'    => $this->parseOuvrirAssiste($arg ?: $n, $orig),
                'contact'   => ['nom'=>'contact.info','slots'=>[]],
                default     => ['nom'=>'fallback','slots'=>['texte'=>$orig]],
            };
        }

        if ($this->has($n,'contact')) return ['nom'=>'contact.info','slots'=>[]];
        if ($this->has($n,'help'))    return ['nom'=>'faq.generale','slots'=>[]];
        if ($this->has($n,'csv'))     return ['nom'=>'incident.export.csv','slots'=>[]];
        if ($this->has($n,'notifs'))  return ['nom'=>'notifications.liste','slots'=>[]];
        if ($this->has($n,'liste'))   return ['nom'=>'incident.liste','slots'=>['etat'=>($this->has($n,'ouvert')?'ouvert':null)]];

        if ($this->has($n,'etat'))    return $this->parseEtat($n);

        if ($code = $this->extraireCodeIncident($n)) {
            if ($this->has($n,'fermer'))   return ['nom'=>'incident.clore.assiste','slots'=>['code'=>$code]];
            if ($this->has($n,'reouvrir')) return ['nom'=>'incident.reouvrir.assiste','slots'=>['code'=>$code]];
            return ['nom'=>'incident.etat','slots'=>['code'=>$code]];
        }

        if ($this->has($n,'reouvrir')) return ['nom'=>'incident.reouvrir.assiste','slots'=>['code'=>null]];
        if ($this->has($n,'fermer'))   return ['nom'=>'incident.clore.assiste','slots'=>['code'=>null]];

        if ($this->has($n,'panne') || $this->has($n,'erreur') || $this->has($n,'reseau') || $this->has($n,'wifi') || $this->has($n,'vpn') || $this->has($n,'mail') || $this->has($n,'imprim') || $this->has($n,'compte') || $this->has($n,'poste') || $this->has($n,'app') || $this->has($n,'lenteur') || $this->has($n,'bloquant') || $this->any($n,$this->lex['logiciels']) || $this->has($n,'web_site')) {
            return $this->parseOuvrirAssiste($n, $orig);
        }

        if (preg_match('/\b(rechercher|buscar|trouver|search)\b\s+(.+)$/u', $n, $mm)) {
            return ['nom'=>'incident.rechercher','slots'=>['q'=>trim($mm[2])]];
        }

        return ['nom'=>'fallback','slots'=>['texte'=>$orig]];
    }

    protected function parseEtat(string $arg): array
    {
        $code = $this->extraireCodeIncident($arg);
        if ($code) return ['nom'=>'incident.etat','slots'=>['code'=>$code]];
        $id = $this->extraireIdInterne($arg);
        if ($id) return ['nom'=>'incident.etat','slots'=>['id'=>$id]];
        return ['nom'=>'incident.etat.demande','slots'=>[]];
    }

    protected function parseOuvrirAssiste(string $arg, string $original): array
    {
        $guess = $this->devinerCategorieType($original);
        $titre = mb_substr(trim($original), 0, 120) ?: '';
        return [
            'nom'   => 'incident.ouvrir.assiste',
            'slots' => [
                'categorie'     => $guess['categorie'],
                'type'          => $guess['type'],
                'titre'         => $titre,
                'description'   => $original,
                'types_proposes'=> [],
                'questions'     => [],
            ],
        ];
    }
}
