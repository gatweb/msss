<?php
// Définir le titre de la page pour l'inclure dans le layout
$pageTitle = 'Outils IA';

// Inclure le layout principal du tableau de bord créateur
// Assumant que le layout est dans app/views/layouts/creator_dashboard.php
// et qu'il gère l'inclusion du header, footer, et du bloc identité.

// Le contenu spécifique de cette page va ici, à l'intérieur du layout.
// Pour l'instant, juste un message placeholder.
?>

<div class="content-container">
    <header class="page-section-header">
        <div class="page-section-heading">
            <p class="page-section-label">Assistants</p>
            <h2 class="page-section-title"><?= htmlspecialchars($pageTitle ?? 'Outils IA') ?></h2>
        </div>
    </header>
    <p>Cette section est en cours de construction. Revenez bientôt pour découvrir nos outils IA !</p>
    
    <?php
    // Vous pourriez ajouter ici des appels à des composants ou des includes spécifiques si nécessaire
    // Exemple: include __DIR__ . '/../partials/_some_component.php';
    ?>
</div>

<?php
// Note : Ce fichier ne contient que le contenu spécifique de la page.
// Le layout `creator_dashboard.php` doit être appelé par le contrôleur
// pour envelopper ce contenu.
// Exemple dans un contrôleur hypothétique :
// App\Core\View::render('creator/ia_tools', ['pageTitle' => 'Outils IA', 'creator' => $creatorData]);
// où 'creator/ia_tools' est le chemin vers ce fichier de vue (sans .php)
// et le layout est spécifié dans la méthode View::render ou dans le BaseController.
?>
