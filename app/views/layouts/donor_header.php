<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Espace Donateur</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/common.css">
<link rel="stylesheet" href="/assets/css/donor.css">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <!-- Sidebar removed -->
        <div class="sidebar-header">
            <img src="<?= htmlspecialchars($user['avatar_url'] ?? '/assets/img/default-avatar.png') ?>" 
                 alt="Photo de profil" 
                 class="profile-pic">
            <h2><?= htmlspecialchars($user['name'] ?? 'Mon Compte') ?></h2>
        </div>
        
        <nav class="sidebar-nav">
            <!-- Menu Donateur -->
            <div class="nav-section">
                <h3 class="nav-section-title">Mes Créatrices</h3>
                <a href="/donor" class="nav-item <?= $pageTitle === 'Mes Créatrices' ? 'active' : '' ?>">
                    <i class="fas fa-heart"></i>
                    <span>Créatrices suivies</span>
                </a>
                <a href="/donor/discover" class="nav-item <?= $pageTitle === 'Découvrir' ? 'active' : '' ?>">
                    <i class="fas fa-compass"></i>
                    <span>Découvrir</span>
                </a>
            </div>

            <div class="nav-separator"></div>

            <div class="nav-section">
                <h3 class="nav-section-title">Mes Dons</h3>
                <a href="/donor/donations" class="nav-item <?= $pageTitle === 'Mes Dons' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i>
                    <span>Historique des dons</span>
                </a>
                <a href="/donor/subscriptions" class="nav-item <?= $pageTitle === 'Mes Abonnements' ? 'active' : '' ?>">
                    <i class="fas fa-sync"></i>
                    <span>Dons récurrents</span>
                </a>
            </div>

            <div class="nav-separator"></div>

            <div class="nav-section">
                <h3 class="nav-section-title">Mon Compte</h3>
                <a href="/donor/profile" class="nav-item <?= $pageTitle === 'Mon Profil' ? 'active' : '' ?>">
                    <i class="fas fa-user"></i>
                    <span>Mon profil</span>
                </a>
                <a href="/donor/notifications" class="nav-item <?= $pageTitle === 'Notifications' ? 'active' : '' ?>">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                    <?php if (!empty($unreadNotifications)): ?>
                        <span class="notification-badge"><?= count($unreadNotifications) ?></span>
                    <?php endif; ?>
                </a>
                <a href="/donor/settings" class="nav-item <?= $pageTitle === 'Paramètres' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </div>
        </nav>
        
        <div class="sidebar-footer">
            <a href="/logout" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="donor-main">
        <!-- Topbar -->
        <!-- Header removed -->
            <div class="topbar-left">

            </div>

        </header>

        <!-- Content Container -->
        <div class="content-container">
