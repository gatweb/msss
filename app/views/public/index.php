<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<div class="hero-section">
    <div class="container">
        <h1>Bienvenue sur notre Plateforme de Dons</h1>
        <p>Découvrez et soutenez nos créatrices talentueuses</p>
    </div>
</div>

<div class="container">
    <div class="creators-grid">
        <?php foreach ($creators as $creator): ?>
            <div class="creator-card">
                <div class="creator-image">
                    <img src="<?= htmlspecialchars($creator['profile_pic_url'] ?? '/assets/img/default-profile.jpg') ?>" 
                         alt="<?= htmlspecialchars($creator['name']) ?>">
                </div>
                <div class="creator-info">
                    <h2><?= htmlspecialchars($creator['name']) ?></h2>
                    <?php if (!empty($creator['tagline'])): ?>
                        <p class="creator-tagline"><?= htmlspecialchars($creator['tagline']) ?></p>
                    <?php endif; ?>
                    <div class="creator-stats">
                        <div class="stat">
                            <i class="fas fa-gift"></i>
                            <span><?= number_format($creator['total_donations'], 2) ?>€</span>
                            <small>reçus</small>
                        </div>
                        <div class="stat">
                            <i class="fas fa-users"></i>
                            <span><?= $creator['donor_count'] ?></span>
                            <small>donateurs</small>
                        </div>
                    </div>
                    <a href="/creator/<?= $creator['id'] ?>" class="button button-outline">
                        <i class="fas fa-heart"></i> Découvrir
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if (empty($creators)): ?>
    <div class="container">
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h2>Aucune créatrice pour le moment</h2>
            <p>Revenez bientôt pour découvrir nos talentueuses créatrices !</p>
        </div>
    </div>
<?php endif; ?>
