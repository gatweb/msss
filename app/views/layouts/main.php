<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= $pageTitle ?? 'Plateforme de Dons' ?></title>
    <link rel="stylesheet" href="/assets/css/common.css">
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <a href="/" class="logo">Plateforme de Dons</a>
                <div class="nav-links">
                    <?php if (isset($_SESSION['user_type'])): ?>
                        <?php if ($_SESSION['user_type'] === 'creator'): ?>
                            <a href="/dashboard">Dashboard</a>
                            <a href="/profile">Mon Profil</a>
                        <?php elseif ($_SESSION['user_type'] === 'admin'): ?>
                            <a href="/profile/admin">Administration</a>
                        <?php endif; ?>
                        <a href="/logout">Déconnexion</a>
                    <?php else: ?>
                        <a href="/login">Connexion</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Plateforme de Dons. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/app.js"></script>
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>
