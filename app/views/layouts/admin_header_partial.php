<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
?>
<header class="site-header admin-header">
    <div class="site-header__inner">
        <a class="site-logo" href="/profile/admin/creators">
            <i class="fas fa-crown"></i>
            <span>Administration</span>
        </a>

        <nav id="admin-nav" class="site-nav">
            <ul class="site-nav__list">
                <li>
                    <a href="/profile/admin/creators" class="site-nav__link<?= str_starts_with($currentPath, '/profile/admin/creators') ? ' is-active' : '' ?>">
                        <i class="fas fa-users"></i>
                        Créatrices
                    </a>
                </li>
                <li>
                    <a href="/" class="site-nav__link">
                        <i class="fas fa-home"></i>
                        Retour au site
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
