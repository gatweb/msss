<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Administration</title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-oZL4YZ6oJQWxqUoh8fDGe0XEt3G5UpiRaY1oCbcnZ6+QcmGeXgnz9K/Y/xFdVtOfvtTDHkJ/xZBwbNG0Ax7y4g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="site-shell admin-page">
    <?php require APP_PATH . '/views/layouts/admin_header_partial.php'; ?>

    <main class="site-main">
        <div class="site-main__inner">
            <?php include __DIR__ . '/flash_messages.php'; ?>
            <div class="surface">
                <?= $content ?>
            </div>
        </div>
    </main>

    <?php require APP_PATH . '/views/layouts/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggles = document.querySelectorAll('[data-nav-toggle]');
            toggles.forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const target = document.querySelector(toggle.dataset.target);
                    if (target) {
                        target.classList.toggle('is-open');
                    }
                });
            });
        });
    </script>
</body>
</html>
