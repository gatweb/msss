<?php
/**
 * Vue pour les erreurs génériques
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur | <?= appName() ?></title>
    <link rel="stylesheet" href="<?= assetPath() ?>/assets/css/common.css">
    <link rel="stylesheet" href="<?= assetPath() ?>/assets/css/errors.css">
</head>
<body class="error-page">
    <div class="error-container">
        <div class="error-content">
            <h1>Erreur</h1>
            <h2>Une erreur est survenue</h2>
            <p>Nous sommes désolés, mais une erreur inattendue s'est produite. Veuillez réessayer plus tard.</p>
            <div class="error-actions">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Page précédente
                </a>
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/your-kit-code.js"></script>
</body>
</html>
