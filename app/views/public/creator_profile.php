<div class="creator-profile">
    <!-- En-tête du profil -->
    <div class="profile-header">
        <div class="profile-cover" style="background-image: url('<?php echo e($creator['cover_url'] ?? '/assets/images/default-cover.jpg'); ?>');">
            <div class="profile-avatar">
                <img src="<?php echo e($creator['avatar_url'] ?? '/assets/img/default-avatar.png'); ?>" alt="<?php echo e($creator['name']); ?>">
            </div>
        </div>
        <div class="profile-info">
            <h1><?php echo e($creator['name']); ?></h1>
            <p class="bio"><?php echo e($creator['bio']); ?></p>
            
            <div class="profile-stats">
                <div class="stat">
                    <span class="stat-value"><?php echo number_format($creator['total_supporters']); ?></span>
                    <span class="stat-label">Supporters</span>
                </div>
                <div class="stat">
                    <span class="stat-value"><?php echo number_format($creator['total_donations']); ?></span>
                    <span class="stat-label">Dons reçus</span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="#donation-form" class="btn btn-primary">
                    <i class="fas fa-heart"></i> Faire un don
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-outline" onclick="followCreator(<?php echo $creator['id']; ?>)">
                        <i class="fas fa-user-plus"></i> Suivre
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="profile-content">
        <!-- Packs de dons -->
        <section class="donation-packs">
            <h2>Packs de dons</h2>
            <div class="packs-grid">
                <?php foreach ($packs as $pack): ?>
                    <div class="pack-card">
                        <div class="pack-image">
                            <img src="<?php echo e($pack['image_url'] ?? '/assets/img/default-pack.jpg'); ?>" alt="<?php echo e($pack['name']); ?>">
                        </div>
                        <div class="pack-content">
                            <h3><?php echo e($pack['name']); ?></h3>
                            <p class="pack-price"><?php echo formatAmount($pack['price']); ?></p>
                            <p class="pack-description"><?php echo e($pack['description']); ?></p>
                            <a href="/donate/<?php echo $creator['id']; ?>/pack/<?php echo $pack['id']; ?>" class="btn btn-primary btn-block">
                                Choisir ce pack
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="profile-grid">
            <!-- Liens utiles -->
            <section class="useful-links">
                <h2>Liens utiles</h2>
                <div class="links-grid">
                    <?php foreach ($creator['links'] as $link): ?>
                        <a href="<?php echo e($link['url']); ?>" class="link-card" target="_blank" rel="noopener noreferrer">
                            <i class="<?php echo e($link['icon']); ?>"></i>
                            <span><?php echo e($link['title']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Derniers donateurs -->
            <section class="recent-supporters">
                <h2>Derniers supporters</h2>
                <div class="supporters-list">
                    <?php foreach ($recent_supporters as $supporter): ?>
                        <div class="supporter-item">
                            <img src="<?php echo e($supporter['avatar_url'] ?? '/assets/img/default-avatar.png'); ?>" alt="<?php echo e($supporter['name']); ?>">
                            <div class="supporter-info">
                                <strong><?php echo e($supporter['name']); ?></strong>
                                <span><?php echo formatDate($supporter['donation_date']); ?></span>
                            </div>
                            <div class="donation-amount">
                                <?php echo formatAmount($supporter['amount']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <!-- Commentaires -->
        <section class="comments-section">
            <h2>Commentaires</h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <form class="comment-form" action="/creator/<?php echo $creator['id']; ?>/comment" method="POST">
                    <textarea name="content" placeholder="Laissez un commentaire..." required></textarea>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Envoyer
                    </button>
                </form>
            <?php else: ?>
                <div class="login-prompt">
                    <p>Connectez-vous pour laisser un commentaire</p>
                    <a href="/login" class="btn btn-outline">Se connecter</a>
                </div>
            <?php endif; ?>

            <div class="comments-list">
                <?php foreach ($approved_comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <img src="<?php echo e($comment['user_avatar'] ?? '/assets/img/default-avatar.png'); ?>" alt="<?php echo e($comment['user_name']); ?>">
                            <div class="comment-meta">
                                <strong><?php echo e($comment['user_name']); ?></strong>
                                <span><?php echo formatDate($comment['created_at']); ?></span>
                            </div>
                        </div>
                        <p class="comment-content"><?php echo e($comment['content']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<style>
.creator-profile {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.profile-header {
    margin-bottom: 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.profile-cover {
    height: 300px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.profile-avatar {
    position: absolute;
    bottom: -60px;
    left: 2rem;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    padding: 4rem 2rem 2rem;
}

.profile-info h1 {
    margin: 0 0 0.5rem;
    color: #2c3e50;
}

.bio {
    color: #666;
    margin-bottom: 1.5rem;
}

.profile-stats {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.stat {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.profile-actions {
    display: flex;
    gap: 1rem;
}

.profile-content {
    margin-bottom: 3rem;
}

.donation-packs {
    margin-bottom: 3rem;
}

.packs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.profile-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.useful-links, .recent-supporters {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.link-card {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 5px;
    color: #2c3e50;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.link-card:hover {
    background: #e9ecef;
}

.supporters-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.supporter-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.supporter-item:hover {
    background: #f8f9fa;
}

.supporter-item img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.supporter-info {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.supporter-info span {
    font-size: 0.9rem;
    color: #666;
}

.donation-amount {
    font-weight: bold;
    color: #2c3e50;
}

.comments-section {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.comment-form {
    margin: 1.5rem 0;
}

.comment-form textarea {
    width: 100%;
    height: 100px;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    resize: vertical;
    margin-bottom: 1rem;
}

.login-prompt {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 5px;
    margin: 1.5rem 0;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.comment-item {
    padding: 1rem;
    border-radius: 5px;
    background: #f8f9fa;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.comment-header img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.comment-meta {
    display: flex;
    flex-direction: column;
}

.comment-meta span {
    font-size: 0.9rem;
    color: #666;
}

.comment-content {
    margin: 0;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }

    .profile-header {
        margin: -1rem -1rem 2rem;
        border-radius: 0;
    }

    .profile-cover {
        height: 200px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        bottom: -50px;
    }

    .profile-info {
        padding: 3rem 1.5rem 1.5rem;
    }

    .profile-actions {
        flex-direction: column;
    }

    .profile-actions .btn {
        width: 100%;
    }
}
</style>

<script>
function followCreator(creatorId) {
    // À implémenter : suivre/ne plus suivre une créatrice
    console.log('Toggle follow for creator:', creatorId);
}
</script>
