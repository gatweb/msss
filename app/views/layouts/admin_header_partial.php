<?php
$headerClasses = 'admin-header';
$logoHref = '/profile/admin/creators';
$logoLabel = 'Administration';
$logoIcon = 'fas fa-crown';
$logoImage = null;
$navLinks = [
    [
        'label' => 'Créatrices',
        'href' => '/profile/admin/creators',
        'icon' => 'fas fa-users',
        'match' => '/profile/admin/creators',
    ],
    [
        'label' => 'Retour au site',
        'href' => '/',
        'icon' => 'fas fa-home',
        'match' => '/',
    ],
];

$actionButtons = [
    [
        'label' => 'Déconnexion',
        'href' => '/logout',
        'icon' => 'fas fa-sign-out-alt',
        'variant' => 'primary',
    ],
];

require __DIR__ . '/header_partial.php';
