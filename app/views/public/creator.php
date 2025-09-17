<?php
$pageTitle = htmlspecialchars($creator['name']) . ' - Profil';
ob_start();
?>

<div class="creator-profile">
    <div class="creator-header" style="background-image: url('<?= htmlspecialchars($creator['banner_url'] ?? '/assets/img/default-banner.jpg') ?>')">
        <div class="container">
            <div class="creator-header-content">
                <div class="creator-avatar">
                    <img src="<?= htmlspecialchars($creator['profile_pic_url'] ?? '/assets/img/default-profile.jpg') ?>" 
                         alt="<?= htmlspecialchars($creator['name']) ?>">
                </div>
                <div class="creator-title">
                    <h1><?= htmlspecialchars($creator['name']) ?></h1>
                    <?php if (!empty($creator['tagline'])): ?>
                        <p class="creator-tagline"><?= htmlspecialchars($creator['tagline']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="creator-content">
            <div class="creator-main">
                <div class="creator-stats-card">
                    <div class="stat">
                        <i class="fas fa-gift"></i>
                        <span class="stat-value"><?= number_format($creator['total_donations'], 2) ?>€</span>
                        <span class="stat-label">Total des dons</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-users"></i>
                        <span class="stat-value"><?= $creator['donor_count'] ?></span>
                        <span class="stat-label">Donateurs</span>
                    </div>
                    <?php if (isset($creator['donation_goal'])): ?>
                        <div class="progress-section">
                            <div class="progress-info">
                                <span>Objectif : <?= number_format($creator['donation_goal'], 2) ?>€</span>
                                <span><?= number_format(($creator['total_donations'] / $creator['donation_goal']) * 100, 1) ?>%</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?= min(100, ($creator['total_donations'] / $creator['donation_goal']) * 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($creator['description'])): ?>
                    <div class="creator-about">
                        <h2>À propos</h2>
                        <div class="content">
                            <?= nl2br(htmlspecialchars($creator['description'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="donation-section">
                    <h2>Faire un don</h2>
                    <form action="/donations/add" method="POST" class="donation-form">
                        <input type="hidden" name="creator_id" value="<?= $creator['id'] ?>">
                        
                        <div class="form-group">
                            <label for="amount">Montant (€)</label>
                            <input type="number" id="amount" name="amount" min="1" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="donation_type">Type de don</label>
                            <select id="donation_type" name="donation_type" required>
                                <option value="PayPal">PayPal</option>
                                <option value="Photo">Photo</option>
                                <option value="Cadeau">Cadeau</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-heart"></i> Faire un don
                        </button>
                    </form>
                </div>
            </div>

            <div class="creator-sidebar">
                <?php if (!empty($links)): ?>
                    <div class="creator-links">
                        <h3>Liens utiles</h3>
                        <ul>
                            <?php foreach ($links as $link): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-link"></i>
                                        <?= htmlspecialchars($link['title']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($creator['packs'])): ?>
                    <div class="creator-packs">
                        <h3>Packs disponibles</h3>
                        <div class="packs-grid">
                            <?php foreach ($creator['packs'] as $pack): ?>
                                <div class="pack-card">
                                    <?php if (!empty($pack['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($pack['image_url']) ?>" alt="<?= htmlspecialchars($pack['name']) ?>">
                                    <?php endif; ?>
                                    <div class="pack-info">
                                        <h4><?= htmlspecialchars($pack['name']) ?></h4>
                                        <p><?= htmlspecialchars($pack['description']) ?></p>
                                        <div class="pack-price"><?= number_format($pack['price'], 2) ?>€</div>
                                        <button class="button button-outline">
                                            <i class="fas fa-shopping-cart"></i> Acheter
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$extraScripts = '<script src="/assets/js/creator-profile.js"></script>';
require APP_PATH . '/views/layouts/main.php';
?>
