<!-- header partiel nettoyé : contenu du dashboard créateur retiré --> 

<main class="admin-main">
    <?php
    // Récupérer les messages flash (sans les définir si aucun n'est passé)
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
                default => 'alert-info',
            };
    ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show m-3" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php
        endforeach;

        // Effacer les messages de la session après affichage
        unset($_SESSION['flash']);
    endif;
    ?>
