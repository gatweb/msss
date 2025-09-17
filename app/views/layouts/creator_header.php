<!-- START CREATOR HEADER -->
<header class="creator-header">
    <div class="header-content">
        <div class="logo">
            <a href="/">
                <img src="/assets/img/logo.png" alt="Msss Logo">
            </a>
        </div>
        <nav class="user-header">
            <ul class="nav-list">
                <li><a href="/profile" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/profile') === 0 ? 'active' : '' ?>">
                    <i class="fas fa-user"></i> Profil</a></li>
                <li><a href="/dashboard" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') === 0 ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="/profile/packs" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/profile/packs') === 0 ? 'active' : '' ?>">
                    <i class="fas fa-box-open"></i> Packs</a></li>
                <li><a href="/profile/messages" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/profile/messages') === 0 ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="/logout" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>
    </div>
</header>
<!-- END CREATOR HEADER -->

        <!-- Content Container -->
