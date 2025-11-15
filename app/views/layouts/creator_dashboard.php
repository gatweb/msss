<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard Créatrice', ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-oZL4YZ6oJQWxqUoh8fDGe0XEt3G5UpiRaY1oCbcnZ6+QcmGeXgnz9K/Y/xFdVtOfvtTDHkJ/xZBwbNG0Ax7y4g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body class="site-shell creator-dashboard">
    <?php require APP_PATH . '/views/layouts/creator_header.php'; ?>

    <div id="main-wrapper">
        <?php include __DIR__ . '/_creator_sidebar.php'; ?>

        <main class="creator-content">
            <?php include __DIR__ . '/flash_messages.php'; ?>
            <?php if (!empty($pageTitle)): ?>
                <h1 class="dashboard-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
            <?php endif; ?>

            <?php if (!empty($dailyTip)): ?>
                <div class="dashboard-tip">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Conseil du jour :</strong> <?= htmlspecialchars($dailyTip, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>
    </div>

    <?php include __DIR__ . '/creator_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/dashboard.js"></script>
</body>
</html>
