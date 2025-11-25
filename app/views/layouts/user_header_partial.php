<?php
$headerClasses = 'user-header';
$logoHref = '/profile';
$logoSubLabel = 'Espace donateur';
$navLinks = [
    [
        'label' => 'Dashboard',
        'href' => '/dashboard',
        'match' => '/dashboard',
        'icon' => 'fas fa-chart-line',
    ],
    [
        'label' => 'Mes packs',
        'href' => '/profile/packs',
        'match' => '/profile/packs',
        'icon' => 'fas fa-gift',
    ],
    [
        'label' => 'Mes liens',
        'href' => '/profile/links',
        'match' => '/profile/links',
        'icon' => 'fas fa-link',
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
