<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Tableau de bord</title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-oZL4YZ6oJQWxqUoh8fDGe0XEt3G5UpiRaY1oCbcnZ6+QcmGeXgnz9K/Y/xFdVtOfvtTDHkJ/xZBwbNG0Ax7y4g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="site-shell creator-dashboard">
    <?php require APP_PATH . '/views/layouts/creator_header.php'; ?>

    <div id="main-wrapper">
        <?php include __DIR__ . '/_creator_sidebar.php'; ?>

        <main class="creator-content">
            <?php include __DIR__ . '/flash_messages.php'; ?>
            <?php if (isset($pageTitle)): ?>
                <h1 class="dashboard-title"><?= htmlspecialchars($pageTitle) ?></h1>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>
    </div>

    <?php include __DIR__ . '/creator_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-nav-toggle]').forEach((toggle) => {
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
