<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Accès interdit | <?= appName() ?></title>
    <link rel="stylesheet" href="<?= assetPath() ?>/assets/css/common.css">
    <link rel="stylesheet" href="<?= assetPath() ?>/assets/css/errors.css">
</head>
<body class="error-page">
    <div class="error-container">
        <div class="error-content">
            <h1>403</h1>
            <h2>Accès interdit</h2>
            <p>Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
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
</div>

<style>
.error-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.error-content {
    text-align: center;
    background: #ffffff;
    padding: 3rem;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.error-content h1 {
    font-size: 6rem;
    color: #e74c3c;
    margin: 0;
    line-height: 1;
}

.error-content h2 {
    font-size: 2rem;
    color: #2c3e50;
    margin: 1rem 0;
}

.error-content p {
    color: #7f8c8d;
    margin-bottom: 2rem;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #3498db;
    color: #ffffff;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-secondary {
    background: #95a5a6;
    color: #ffffff;
}

.btn-secondary:hover {
    background: #7f8c8d;
}
</style>

