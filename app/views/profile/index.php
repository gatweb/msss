
<div class="profile-container">
    <div class="profile-header">
        <h1>Mon profil</h1>
        <a href="/profile/<?= htmlspecialchars($creator['username']) ?>" class="btn-view-public">
            <i class="fas fa-eye"></i> Voir mon profil public
        </a>
    </div>

    <form method="POST" action="/profile/update" class="profile-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

        <div class="form-grid">
            <div class="form-section main-info">
                <h2>Informations générales</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="form-section main-info">
            <h2>Informations générales</h2>
                
                <div class="profile-images">
                    <div class="profile-pic">
                        <img src="<?= htmlspecialchars($creator['profile_pic_url'] ?? '/assets/img/default-avatar.png') ?>" 
                             alt="Photo de profil" id="profilePicPreview">
                        <label for="profilePic" class="btn-upload">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="profilePic" name="profile_pic" accept="image/*" hidden>
                    </div>

                    <div class="cover-pic">
                        <img src="<?= htmlspecialchars($creator['cover_pic_url'] ?? '/assets/img/default-cover.jpg') ?>" 
                             alt="Photo de couverture" id="coverPicPreview">
                        <label for="coverPic" class="btn-upload">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="coverPic" name="cover_pic" accept="image/*" hidden>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($creator['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($creator['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="tagline">Tagline</label>
                    <input type="text" id="tagline" name="tagline" value="<?= htmlspecialchars($creator['tagline'] ?? '') ?>">
                    <small>Une courte description qui apparaît sous votre nom</small>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Décrivez-vous et votre travail..."><?= htmlspecialchars($creator['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="donation_goal">Objectif de dons</label>
                    <div class="input-group">
                        <input type="number" id="donation_goal" name="donation_goal" 
                               value="<?= htmlspecialchars($creator['donation_goal'] ?? '') ?>" min="0" step="1">
                        <span class="input-group-text">€</span>
                    </div>
                </div>

                <h3>Liens de soutien</h3>
                <p class="help-text">Ajoutez vos liens pour recevoir des dons de vos supporters.</p>

                <div class="form-group">
                    <label for="paypal_link">Lien PayPal</label>
                    <input type="url" id="paypal_link" name="paypal_link" value="<?= htmlspecialchars($creator['paypal_link'] ?? '') ?>" 
                           placeholder="https://paypal.me/votre-nom">
                </div>

                <div class="form-group">
                    <label for="amazon_link">Liste de souhaits Amazon</label>
                    <input type="url" id="amazon_link" name="amazon_link" value="<?= htmlspecialchars($creator['amazon_link'] ?? '') ?>"
                           placeholder="https://amazon.fr/liste-de-souhaits/votre-liste">
                </div>

                <div class="form-group">
                    <label for="throne_link">Lien Throne</label>
                    <input type="url" id="throne_link" name="throne_link" value="<?= htmlspecialchars($creator['throne_link'] ?? '') ?>"
                           placeholder="https://throne.com/votre-profil">
                </div>

                <div class="form-group">
                    <label for="other_links">Autres liens</label>
                    <textarea id="other_links" name="other_links" rows="2" 
                              placeholder="Ajoutez d'autres liens de soutien (un par ligne)"><?= htmlspecialchars($creator['other_links'] ?? '') ?></textarea>
                    <small class="help-text">Séparez les liens par des retours à la ligne</small>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>

            <div class="form-section password-section">
                <h2>Changer le mot de passe</h2>
                
                <form action="/profile/password" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="form-group">
                        <label for="current_password">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Changer le mot de passe
                    </button>
                </form>
            </div>

            <div class="form-section links-section">
                <h2>Liens sociaux</h2>
                
                <div class="links-list">
                    <?php if (empty($links)): ?>
                        <p class="empty-links">Vous n'avez pas encore ajouté de liens sociaux</p>
                    <?php else: ?>
                        <?php foreach ($links as $link): ?>
                            <div class="link-item">
                                <i class="<?= htmlspecialchars($link['icon'] ?? 'fas fa-link') ?>"></i>
                                <span class="link-title"><?= htmlspecialchars($link['title']) ?></span>
                                <a href="<?= htmlspecialchars($link['url']) ?>" class="link-url" target="_blank">
                                    <?= htmlspecialchars($link['url']) ?>
                                </a>
                                <div class="link-actions">
                                    <button class="btn btn-edit" onclick="editLink(<?= (int)$link['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="/profile/links/<?= (int)$link['id'] ?>/delete" method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce lien ?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <form action="/profile/links" method="POST" class="add-link-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="form-group">
                        <label for="link_title">Titre du lien</label>
                        <input type="text" id="link_title" name="link_title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="link_url">URL</label>
                        <input type="url" id="link_url" name="link_url" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="link_icon">Icône</label>
                        <input type="text" id="link_icon" name="link_icon" placeholder="fas fa-link">
                        <small>Classe Font Awesome (ex: fas fa-twitter)</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un lien
                    </button>
                </form>
            </div>
        </div>
    </form>
</div>

<style>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.profile-header {
    position: relative;
    margin-bottom: 2rem;
}

.banner-upload {
    position: relative;
    height: 200px;
    overflow: hidden;
    border-radius: 10px;
}

.banner-upload img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-upload {
    position: absolute;
    bottom: -50px;
    left: 50px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.avatar-upload img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.3s ease;
}

.upload-btn:hover {
    background: rgba(0, 0, 0, 0.7);
}

input[type="file"] {
    display: none;
}

.profile-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.profile-section {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.profile-section h2 {
    margin-bottom: 1.5rem;
    color: #2c3e50;
    font-size: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #34495e;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #3498db;
    outline: none;
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
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #3498db;
    color: #fff;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-edit {
    background: #f39c12;
    color: #fff;
}

.btn-edit:hover {
    background: #d68910;
}

.btn-delete {
    background: #e74c3c;
    color: #fff;
}

.btn-delete:hover {
    background: #c0392b;
}

.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 5px;
}

.alert-success {
    background: #2ecc71;
    color: #fff;
}

.alert-error {
    background: #e74c3c;
    color: #fff;
}

.links-list {
    margin-top: 1.5rem;
}

.link-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 5px;
    margin-bottom: 0.5rem;
}

.link-item i {
    font-size: 1.2rem;
    color: #3498db;
}

.link-title {
    font-weight: 500;
    color: #2c3e50;
}

.link-url {
    color: #7f8c8d;
    text-decoration: none;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.link-actions {
    display: flex;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .profile-content {
        grid-template-columns: 1fr;
    }
    
    .avatar-upload {
        left: 50%;
        transform: translateX(-50%);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prévisualisation des images
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Profile pic preview
    document.getElementById('profilePic').addEventListener('change', function() {
        previewImage(this, 'profilePicPreview');
    });

    // Cover pic preview
    document.getElementById('coverPic').addEventListener('change', function() {
        previewImage(this, 'coverPicPreview');
    });
});

function editLink(linkId) {
    // À implémenter : ouvrir une modal pour éditer le lien
    alert('Fonctionnalité à venir');
}
</script>


