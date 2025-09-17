<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard Créateur', ENT_QUOTES, 'UTF-8') ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/style.css"> 
    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column; /* Header en haut, puis le reste */
        }

        #main-wrapper {
            display: flex;
            flex-grow: 1; /* Prend la hauteur restante */
            overflow: hidden; /* Empêche le débordement global */
        }

        .creator-sidebar {
            width: 260px; /* Largeur fixe de la sidebar */
            flex-shrink: 0; /* Empêche la sidebar de rétrécir */
            overflow-y: auto; /* Ajoute le défilement si le contenu dépasse */
        }

        .creator-content {
            flex-grow: 1; /* Prend la largeur restante */
            padding: 2rem; /* Espacement intérieur */
            overflow-y: auto; /* Permet le défilement du contenu principal */
        }

        /* Style des liens de la sidebar */
        .creator-sidebar .nav-link {
            color: #495057; /* Couleur de texte plus douce */
            padding: 0.75rem 1rem;
            transition: background-color 0.2s ease-in-out;
        }

        .creator-sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #0d6efd;
        }

        .creator-sidebar .nav-link.active {
            font-weight: 600;
            color: #0d6efd; /* Bleu Bootstrap */
            background-color: #e0eaf7;
            border-left: 3px solid #0d6efd;
            padding-left: calc(1rem - 3px);
        }

        .creator-sidebar .nav-link i {
            width: 24px; /* Alignement des icônes */
            text-align: center;
            margin-right: 0.5rem;
            vertical-align: middle; /* Meilleur alignement vertical */
        }

        /* Pour pousser Déconnexion en bas */
        .creator-sidebar .sidebar-sticky {
            min-height: calc(100vh - 56px); /* Hauteur min = viewport - hauteur header */
        }

        .creator-sidebar .mb-auto {
             margin-bottom: auto !important; /* Force la marge basse */
        }
    </style>
</head>
<body>
    <?php require APP_PATH . '/views/layouts/creator_header.php'; ?>

    <div id="main-wrapper">
        <?php include __DIR__ . '/_creator_sidebar.php'; // Inclusion de la nouvelle sidebar ?>

        <main class="creator-content">
            <!-- Affichage du titre de la page spécifique (si défini) -->
            <?php if (isset($pageTitle) && !empty($pageTitle)): ?>
                <h1 class="h3 mb-4"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
            <?php endif; ?>

            <!-- Affichage du conseil du jour ici -->
            <?php if (isset($dailyTip) && !empty($dailyTip)): ?>
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-lightbulb-fill me-2"></i>
                    <strong>Conseil du jour :</strong> <?= htmlspecialchars($dailyTip, ENT_QUOTES, 'UTF-8') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Affichage des messages Flash ici -->
            <?php
            // Récupérer les messages flash
            $flashMessages = flash();

            // Vérifier s'il y a des messages
            if (!empty($flashMessages)) :
                // Boucler et afficher chaque message
                foreach ($flashMessages as $flashMessage) :
                    $message = htmlspecialchars($flashMessage['message'] ?? 'Message vide.', ENT_QUOTES, 'UTF-8');
                    $type = htmlspecialchars($flashMessage['type'] ?? 'info', ENT_QUOTES, 'UTF-8');
                    // Mapper les types aux classes d'alerte Bootstrap
                    $alertClass = match (strtolower($type)) {
                        'error', 'danger' => 'alert-danger',
                        'warning' => 'alert-warning',
                        'success' => 'alert-success',
                        default => 'alert-info', // Défaut à 'info'
                    };
            ?>
                    <div class="alert <?= $alertClass ?> alert-dismissible fade show mb-3" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
            <?php
                endforeach;

                // Effacer les messages de la session après affichage
                unset($_SESSION['flash']);
            endif;
            ?>

            <?= isset($content) ? $content : '' ?>
        </main>
    </div> <!-- Fin #main-wrapper -->

    <!-- Scripts JS (Bootstrap etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/dashboard.js"></script>
</body>
</html>
