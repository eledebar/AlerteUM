<?php

return [

   
    'notify_admin_on_creation'   => false,
    'notify_admin_on_assignment' => false,
    'notify_admin_on_status'     => false,
    'notify_admin_on_escalation' => true,
    'notify_admin_on_sla_breach' => true,

    'admin_emails' => env('ITIL_ADMIN_EMAILS', ''),

    
    'sla_hours' => [
        'low'      => 72,
        'medium'   => 48,
        'high'     => 24,
        'critical' => 4,
    ],

  
    'priority_order' => ['critical', 'high', 'medium', 'low'],

   
    'labels' => [
        'priority' => [
            'low'      => 'Faible',
            'medium'   => 'Moyenne',
            'high'     => 'Haute',
            'critical' => 'Critique',
        ],
        'status' => [
            'nouveau'   => 'Nouveau',
            'en_cours'  => 'En cours',
            'en-cours'  => 'En cours',   
            'résolu'    => 'Résolu',
            'resolu'    => 'Résolu',     
            'fermé'     => 'Fermé',
            'ferme'     => 'Fermé',     
        ],
    ],

];
