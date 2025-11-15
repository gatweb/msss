<?php
// Démarrage de la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Administration') ?> - Msss</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/common.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Scripts -->
</head>
<body>
    <header class="admin-header" style="background:#fff;box-shadow:0 4px 24px #0001;padding-bottom:0.5em;">
        <div class="header-content" style="display:flex;flex-direction:column;align-items:center;">
            <div class="logo" style="display:flex;justify-content:center;align-items:center;margin:2em auto 1em auto;border:6px solid red;background:yellow;">
                <a href="/profile/admin" style="display:inline-block;">
                    <img src="/assets/img/logo.png" alt="Msss Logo" style="max-width:320px;height:auto;display:block;border:6px solid red !important;">
                </a>
            </div>
            
            <?php if (isset($_SESSION['creator_id'])): ?>
            <nav class="user-header">
                <ul class="nav-list">
                    <li><a href="/profile" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/profile' ? ' active' : '' ?>">
                        <i class="fas fa-user"></i> Profil</a></li>
                    <li><a href="/dashboard" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/dashboard' ? ' active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="/profile/packs" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/profile/packs' ? ' active' : '' ?>">
                        <i class="fas fa-gift"></i> Packs</a></li>
                    <li><a href="/profile/messages" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/messages' ? ' active' : '' ?>">
                        <i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="/logout" class="nav-item">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
            <i class="fas fa-user"></i> Profil</a></li>
        <li><a href="/dashboard" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/dashboard' ? ' active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="/profile/packs" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/profile/packs' ? ' active' : '' ?>">
            <i class="fas fa-gift"></i> Packs</a></li>
        <li><a href="/profile/messages" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/messages' ? ' active' : '' ?>">
            <i class="fas fa-envelope"></i> Messages</a></li>
        <li><a href="/logout" class="nav-item">
            <i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
    </ul>
</nav>
            
            
            <?php endif; ?>
        </div>
    </header>
    
    <main class="admin-main">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
