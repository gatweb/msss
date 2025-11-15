<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
?>
<header class="site-header creator-header">
    <div class="site-header__inner">
        <a href="/" class="site-logo">
            <?php $brandName = defined('APP_NAME') ? APP_NAME : 'Msss'; ?>
            <img src="/assets/img/logo.png" alt="<?= htmlspecialchars($brandName) ?>">
            <span><?= htmlspecialchars($brandName) ?> — Espace créatrice</span>
        </a>

        <nav id="creator-nav" class="site-nav">
            <ul class="site-nav__list">
                <li>
                    <a href="/profile" class="site-nav__link<?= str_starts_with($currentPath, '/profile') && !str_contains($currentPath, '/profile/packs') && !str_contains($currentPath, '/profile/messages') ? ' is-active' : '' ?>">
                        <i class="fas fa-user"></i>
                        Profil
                    </a>
                </li>
                <li>
                    <a href="/dashboard" class="site-nav__link<?= str_starts_with($currentPath, '/dashboard') ? ' is-active' : '' ?>">
                        <i class="fas fa-gauge-high"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="/profile/packs" class="site-nav__link<?= str_starts_with($currentPath, '/profile/packs') ? ' is-active' : '' ?>">
                        <i class="fas fa-box"></i>
                        Packs
                    </a>
                </li>
                <li>
                    <a href="/profile/messages" class="site-nav__link<?= str_starts_with($currentPath, '/profile/messages') ? ' is-active' : '' ?>">
                        <i class="fas fa-envelope"></i>
                        Messages
                    </a>
                </li>
            </ul>
        </nav>

        <div class="site-actions">
            <a class="btn" href="/logout">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </div>
</header>
