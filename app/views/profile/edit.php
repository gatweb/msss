<?php
// Titre de la page
$this->setTitle('Modifier mon profil');
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Modifier mon profil</h1>
        <a href="/profile" class="btn-view-public">
            <i class="fas fa-eye"></i> Voir mon profil
        </a>
    </div>

    <form class="form-grid" action="/profile/update" method="POST" enctype="multipart/form-data">
        <!-- Section principale -->
        <div class="form-section">
            <h2>Informations générales</h2>
            
            <div class="profile-images">
                <div class="profile-pic">
                    <img src="<?= htmlspecialchars($creator['profile_pic_url'] ?? '/assets/img/default-avatar.png') ?>" 
                         alt="Photo de profil">
                    <label for="avatar" class="btn-upload">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                </div>
                
                <div class="cover-pic">
                    <img src="<?= htmlspecialchars($creator['cover_pic_url'] ?? '/assets/img/default-cover.jpg') ?>" 
                         alt="Image de couverture">
                    <label for="banner" class="btn-upload">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="banner" name="banner" accept="image/*" style="display: none;">
                </div>
            </div>
            
            <div class="form-group">
                <label for="name">Nom *</label>
                <input type="text" id="name" name="name" required 
                       value="<?= htmlspecialchars($creator['name']) ?>" 
                       maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="tagline">Tagline</label>
                <input type="text" id="tagline" name="tagline" 
                       value="<?= htmlspecialchars($creator['tagline'] ?? '') ?>" 
                       maxlength="200">
                <small>Une courte description qui apparaît sous votre nom (200 caractères max)</small>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="6"><?= htmlspecialchars($creator['description'] ?? '') ?></textarea>
                <small>Présentez-vous à vos supporters</small>
            </div>
        </div>

        <!-- Section latérale -->
        <div class="form-section">
            <h2>Paramètres de dons</h2>
            
            <div class="form-group">
                <label for="donation_goal">Objectif de dons (€)</label>
                <div class="input-group">
                    <input type="number" id="donation_goal" name="donation_goal" 
                           value="<?= htmlspecialchars($creator['donation_goal'] ?? '') ?>" 
                           min="0" step="1">
                    <span class="input-group-text">€</span>
                </div>
                <small>Laissez vide si vous ne souhaitez pas afficher d'objectif</small>
            </div>

            <h2 class="mt-4">Sécurité</h2>
            
            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password">
            </div>
            
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password">
                <small>Laissez vide pour ne pas changer</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
        </div>
    </form>
</div>

<script>
// Prévisualisation des images
document.getElementById('avatar')?.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = this.parentElement.querySelector('img');
            if (preview) preview.src = e.target.result;
        }.bind(this);
        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById('banner')?.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = this.parentElement.querySelector('img');
            if (preview) preview.src = e.target.result;
        }.bind(this);
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
