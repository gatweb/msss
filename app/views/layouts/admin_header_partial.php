<!-- START ADMIN HEADER -->
<header class="admin-header">
    <div class="header-content">
        <div class="logo">
            <a href="/profile/admin/creators">
                <i class="fas fa-crown"></i>
            </a>
        </div>
        <nav class="user-header">
            <ul class="nav-list">
                <li><a href="/profile/admin/creators" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/profile/admin/creators') === 0 ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Créatrices</a></li>
                <li><a href="/" class="nav-item">
                    <i class="fas fa-home"></i> Retour au site</a></li>
                <li><a href="/logout" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>
    </div>
</header>
<!-- END ADMIN HEADER -->
