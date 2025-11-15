<?php /** Layout pour les pages d'authentification */ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?></title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-oZL4YZ6oJQWxqUoh8fDGe0XEt3G5UpiRaY1oCbcnZ6+QcmGeXgnz9K/Y/xFdVtOfvtTDHkJ/xZBwbNG0Ax7y4g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php if (!empty($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="site-shell">
    <?php include __DIR__ . '/header_partial.php'; ?>

    <main class="site-main">
        <div class="site-main__inner">
            <?php include __DIR__ . '/flash_messages.php'; ?>
            <section class="auth-layout">
                <div class="auth-card">
                    <?= $content ?>
                </div>
            </section>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
