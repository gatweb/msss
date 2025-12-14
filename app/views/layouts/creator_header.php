<?php
$headerClasses = 'creator-header';
$logoSubLabel = 'Espace créatrice';
$navLinks = [
    [
        'label' => 'Profil',
        'href' => '/profile',
        'match' => '/profile',
        'exact' => true,
        'icon' => 'fas fa-user',
    ],
    [
        'label' => 'Dashboard',
        'href' => '/dashboard',
        'match' => '/dashboard',
        'icon' => 'fas fa-gauge-high',
    ],
    [
        'label' => 'Packs',
        'href' => '/profile/packs',
        'match' => '/profile/packs',
        'icon' => 'fas fa-box',
    ],
    [
        'label' => 'Messages',
        'href' => '/profile/messages',
        'match' => '/profile/messages',
        'icon' => 'fas fa-envelope',
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
