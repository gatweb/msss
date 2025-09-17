<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Tableau de Bord</title>
    <link rel="stylesheet" href="/assets/css/common.css">
<link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/packs.css">
    <link rel="stylesheet" href="/assets/css/pack-forms.css">
    <!-- material_creatrice.css removed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- TEST HEAD DASHBOARD.PHP -->
</head>
<body>
    <div class="dashboard-layout">
    <?php include __DIR__ . '/header_dashboard.php'; ?>
    <div class="dashboard-content">
        <main class="dashboard-main">
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
                <?= isset($content) ? $content : '' ?>
            </div>
        </main>
        <?php include __DIR__ . '/footer_dashboard.php'; ?>
    </div>
</div>
</body>
</html>
