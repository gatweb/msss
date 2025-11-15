<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
?>
<header class="site-header user-header">
    <div class="site-header__inner">
        <a href="/profile" class="site-logo">
            <img src="/assets/img/logo.png" alt="Msss">
            <span>Espace donateur</span>
        </a>

        <nav id="user-nav" class="site-nav">
            <ul class="site-nav__list">
                <li>
                    <a href="/dashboard" class="site-nav__link<?= str_starts_with($currentPath, '/dashboard') ? ' is-active' : '' ?>">
                        <i class="fas fa-chart-line"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="/profile/packs" class="site-nav__link<?= str_starts_with($currentPath, '/profile/packs') ? ' is-active' : '' ?>">
                        <i class="fas fa-gift"></i>
                        Mes Packs
                    </a>
                </li>
                <li>
                    <a href="/profile/links" class="site-nav__link<?= str_starts_with($currentPath, '/profile/links') ? ' is-active' : '' ?>">
                        <i class="fas fa-link"></i>
                        Mes liens
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
