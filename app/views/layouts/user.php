<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset(
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/user.css">
    <!-- material_creatrice.css removed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="user-layout">
        <?php require APP_PATH . '/views/layouts/user_header_partial.php'; ?>
        <main class="user-main">
            <?= $content ?>
        </main>
        <?php require_once APP_PATH . '/views/layouts/footer_user.php'; ?>
    </div>
</body>
</html>
