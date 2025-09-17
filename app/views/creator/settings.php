<?php
// Titre défini dans le contrôleur ProfileController::settings()
// Layout géré par View::render()
?>

<div class="content-container">
    <h1><?= htmlspecialchars($pageTitle ?? 'Paramètres') ?></h1>
    
    <p>Ici, vous pourrez bientôt gérer les paramètres de votre compte.</p>

    <div class="settings-section">
        <h2>Informations du compte</h2>
        <p>Modification des informations de base (nom, email...).</p>
        <!-- Formulaire informations de base ici -->
    </div>

    <div class="settings-section">
        <h2>Changement de mot de passe</h2>
        <p>Mettez à jour votre mot de passe.</p>
        <!-- Formulaire de changement de mot de passe ici -->
         <p><em>(Logique à déplacer depuis l'ancienne page Profil)</em></p>
    </div>

     <div class="settings-section">
        <h2>Préférences</h2>
        <p>Gérer d'autres préférences.</p>
        <!-- Autres options ici -->
    </div>

</div>

<style>
/* Styles temporaires ou à déplacer dans dashboard.css */
.settings-section {
    background-color: #fff;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    box-shadow: var(--shadow-soft);
}
.settings-section h2 {
    margin-top: 0;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.5rem;
}
</style>
