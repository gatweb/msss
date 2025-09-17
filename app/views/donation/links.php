
<div class="donation-links">
    <div class="creator-info">
        <img src="<?= htmlspecialchars($creator['profile_pic_url'] ?? '/assets/img/default-avatar.png') ?>" 
             alt="<?= htmlspecialchars($creator['name']) ?>" class="creator-avatar">
        <h1>Soutenir <?= htmlspecialchars($creator['name']) ?></h1>
        <?php if ($creator['tagline']): ?>
            <p class="tagline"><?= htmlspecialchars($creator['tagline']) ?></p>
        <?php endif; ?>
    </div>

    <div class="support-options">
        <?php if ($creator['paypal_link']): ?>
            <a href="<?= htmlspecialchars($creator['paypal_link']) ?>" target="_blank" class="support-link paypal">
                <i class="fab fa-paypal"></i>
                <span>Faire un don via PayPal</span>
            </a>
        <?php endif; ?>

        <?php if ($creator['throne_link']): ?>
            <a href="<?= htmlspecialchars($creator['throne_link']) ?>" target="_blank" class="support-link throne">
                <i class="fas fa-crown"></i>
                <span>Liste de souhaits Throne</span>
            </a>
        <?php endif; ?>

        <?php if ($creator['amazon_link']): ?>
            <a href="<?= htmlspecialchars($creator['amazon_link']) ?>" target="_blank" class="support-link amazon">
                <i class="fab fa-amazon"></i>
                <span>Liste de souhaits Amazon</span>
            </a>
        <?php endif; ?>

        <?php if ($creator['other_links']): ?>
            <div class="other-links">
                <h3>Autres moyens de soutien</h3>
                <?php foreach (explode("\n", $creator['other_links']) as $link): ?>
                    <?php if (trim($link)): ?>
                        <a href="<?= htmlspecialchars(trim($link)) ?>" target="_blank" class="support-link other">
                            <i class="fas fa-link"></i>
                            <span>Lien personnalisé</span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($creator['description']): ?>
        <div class="creator-description">
            <h3>À propos de <?= htmlspecialchars($creator['name']) ?></h3>
            <p><?= nl2br(htmlspecialchars($creator['description'])) ?></p>
        </div>
    <?php endif; ?>
</div>

<style>
.donation-links {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.creator-info {
    text-align: center;
    margin-bottom: 2rem;
}

.creator-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin-bottom: 1rem;
    object-fit: cover;
}

.tagline {
    color: #666;
    font-style: italic;
    margin-top: 0.5rem;
}

.support-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.support-link {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 8px;
    text-decoration: none;
    color: white;
    transition: transform 0.2s;
}

.support-link:hover {
    transform: translateY(-2px);
}

.support-link i {
    font-size: 1.5rem;
    margin-right: 1rem;
}

.paypal {
    background: #003087;
}

.throne {
    background: #6441a5;
}

.amazon {
    background: #ff9900;
}

.other {
    background: #4a4a4a;
}

.other-links {
    margin-top: 1rem;
}

.other-links h3 {
    margin-bottom: 1rem;
    color: #333;
}

.creator-description {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 2rem;
}

.creator-description h3 {
    color: #333;
    margin-bottom: 1rem;
}

@media (max-width: 480px) {
    .support-link {
        flex-direction: column;
        text-align: center;
        padding: 1.5rem;
    }

    .support-link i {
        margin: 0 0 0.5rem 0;
        font-size: 2rem;
    }
}
</style>

