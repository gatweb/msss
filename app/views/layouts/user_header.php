<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Msss</title>
    <!-- Styles du dashboard utilisés partout -->
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">

    <link rel="stylesheet" href="/assets/css/packs.css">
    <link rel="stylesheet" href="/assets/css/pack-forms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Scripts -->
</head>
<body>
    <div class="dashboard-layout">
        <header class="dashboard-header">
            <div class="header-content">
                <div class="logo">
                    <a href="/profile"><img src="/assets/img/logo.png" alt="Msss"></a>
                </div>
                <nav class="user-header">
                    <ul class="nav-list">
                        <li><a href="/profile" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/profile' ? ' active' : '' ?>">
                            <i class="fas fa-user"></i> Profil</a></li>
                        <li><a href="/dashboard" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/dashboard' ? ' active' : '' ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="/profile/packs" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/profile/packs' ? ' active' : '' ?>">
                            <i class="fas fa-gift"></i> Packs</a></li>
                        <li><a href="/profile/messages" class="nav-item<?= $_SERVER['REQUEST_URI'] === '/profile/messages' ? ' active' : '' ?>">
                            <i class="fas fa-envelope"></i> Messages</a></li>
                        <li><a href="/logout" class="nav-item">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main class="dashboard-main">
        <?= $content ?>
            <div class="content-container">

                <?php if (isset($_SESSION['flash_messages'])): ?>
                    <?php foreach ($_SESSION['flash_messages'] as $type => $messages): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="alert alert-<?= $type ?>">
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['flash_messages']); ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
