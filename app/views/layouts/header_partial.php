<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$navLinks = [
    [
        'label' => 'Accueil',
        'href' => '/',
        'isActive' => $currentPath === '/',
    ],
    [
        'label' => 'Découvrir',
        'href' => '/#creators',
        'isActive' => str_starts_with($currentPath, '/creator'),
    ],
];

$isLogged = session('creator_id');
$displayName = session('creator_name') ?? session('username') ?? null;
?>
<header class="site-header">
    <div class="site-header__inner">
        <a href="/" class="site-logo">
            <img src="/assets/img/logo.png" alt="Msss">
            <span>Msss</span>
        </a>

        <button class="site-nav__toggle" type="button" data-nav-toggle data-target="#site-nav" aria-label="Basculer la navigation">
            <i class="fas fa-bars"></i>
        </button>

        <nav id="site-nav" class="site-nav">
            <ul class="site-nav__list">
                <?php foreach ($navLinks as $link): ?>
                    <li>
                        <a href="<?= $link['href'] ?>" class="site-nav__link<?= $link['isActive'] ? ' is-active' : '' ?>">
                            <?= htmlspecialchars($link['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="site-actions">
            <?php if ($isLogged): ?>
                <a class="btn-ghost" href="/dashboard">
                    <i class="fas fa-chart-line"></i>
                    <?= $displayName ? htmlspecialchars($displayName) : 'Dashboard' ?>
                </a>
                <a class="btn" href="/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </a>
            <?php else: ?>
                <a class="btn-ghost" href="/login">
                    Connexion
                </a>
                <a class="btn" href="/register">
                    Devenir créatrice
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
