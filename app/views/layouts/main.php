<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Plateforme de Dons' ?></title>
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/public.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-oZL4YZ6oJQWxqUoh8fDGe0XEt3G5UpiRaY1oCbcnZ6+QcmGeXgnz9K/Y/xFdVtOfvtTDHkJ/xZBwbNG0Ax7y4g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="site-shell">
    <?php include __DIR__ . '/header_partial.php'; ?>

    <main class="site-main">
        <div class="site-main__inner">
            <?php include __DIR__ . '/flash_messages.php'; ?>

            <?php if (!empty($error_message)): ?>
                <div class="flash-stack">
                    <div class="flash-message flash-message--error"><?= htmlspecialchars($error_message) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="flash-stack">
                    <div class="flash-message flash-message--success"><?= htmlspecialchars($success_message) ?></div>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.site-nav__list').forEach((list) => {
                list.addEventListener('wheel', (event) => {
                    if (Math.abs(event.deltaY) < Math.abs(event.deltaX)) {
                        return;
                    }
                    event.preventDefault();
                    list.scrollLeft += event.deltaY;
                });
            });
        });
    </script>
</body>
</html>
