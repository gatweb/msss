<div class="profile-page">
    <div class="profile-header">
        <div class="profile-cover" style="background-image: url('<?= htmlspecialchars($creator['cover_pic_url'] ?? '/assets/images/default-cover.jpg') ?>')">
            <div class="profile-avatar">
                <img src="<?= htmlspecialchars($creator['profile_pic_url'] ?? '/assets/img/default-avatar.png') ?>" 
                     alt="<?= htmlspecialchars($creator['name']) ?>">
            </div>
        </div>
        
        <div class="profile-info">
            <h1><?= htmlspecialchars($creator['name']) ?></h1>
            
            <?php if ($creator['tagline']): ?>
                <p class="tagline"><?= htmlspecialchars($creator['tagline']) ?></p>
            <?php endif; ?>
            
            <div class="profile-stats">
                <div class="stat">
                    <span class="stat-value"><?= number_format($creator['total_donations'] ?? 0) ?></span>
                    <span class="stat-label">Dons reçus</span>
                </div>
                <div class="stat">
                    <span class="stat-value"><?= number_format($creator['total_supporters'] ?? 0) ?></span>
                    <span class="stat-label">Supporters</span>
                </div>
            </div>
            
            <div class="profile-actions">
                <?php if ($isCurrentUser ?? false): ?>
                    <a href="/profile/edit" class="btn-edit">
                        <i class="fas fa-edit"></i> Modifier mon profil
                    </a>
                <?php else: ?>
                    <a href="/donation/links/<?= $creator['id'] ?>" class="btn-support">
                        <i class="fas fa-heart"></i> Soutenir
                    </a>
                    <a href="mailto:<?= htmlspecialchars($creator['email']) ?>" class="btn-contact">
                        <i class="fas fa-envelope"></i> Contact
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="profile-content">
        <?php if ($creator['description']): ?>
            <div class="profile-section">
                <h2>À propos</h2>
                <div class="profile-description">
                    <?= nl2br(htmlspecialchars($creator['description'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($media)): ?>
            <div class="profile-section">
                <h2>Médias récents</h2>
                <div class="media-grid">
                    <?php foreach ($media as $item): ?>
                        <div class="media-item">
                            <?php if ($item['type'] === 'image'): ?>
                                <img src="/uploads/<?= htmlspecialchars($item['filename']) ?>" 
                                     alt="<?= htmlspecialchars($item['title'] ?? '') ?>">
                            <?php else: ?>
                                <div class="video-preview">
                                    <?php if ($item['thumbnail']): ?>
                                        <img src="/uploads/<?= htmlspecialchars($item['thumbnail']) ?>" 
                                             alt="Aperçu vidéo">
                                    <?php endif; ?>
                                    <i class="fas fa-play"></i>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($item['title']): ?>
                                <div class="media-caption">
                                    <?= htmlspecialchars($item['title']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-footer">
                    <a href="/media/gallery/<?= $creator['id'] ?>" class="btn-view-all">
                        Voir tous les médias
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($creator['donation_goal']): ?>
            <div class="profile-section">
                <h2>Objectif de dons</h2>
                <div class="donation-goal">
                    <div class="goal-progress">
                        <?php 
                        $progress = min(100, ($creator['total_donations'] / $creator['donation_goal']) * 100);
                        ?>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?= $progress ?>%"></div>
                        </div>
                        <div class="goal-stats">
                            <span class="current"><?= number_format($creator['total_donations']) ?>€</span>
                            <span class="target"><?= number_format($creator['donation_goal']) ?>€</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


