<?php require_once APP_PATH . '/views/layouts/donor_header.php'; ?>

<div class="donor-dashboard">
    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-content">
                <h3>Créatrices soutenues</h3>
                <p class="stat-value"><?= $stats['supported_creators'] ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-content">
                <h3>Total des dons</h3>
                <p class="stat-value"><?= number_format($stats['total_donations'], 2) ?> €</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-sync"></i>
            </div>
            <div class="stat-content">
                <h3>Dons mensuels</h3>
                <p class="stat-value"><?= number_format($stats['monthly_donations'], 2) ?> €/mois</p>
            </div>
        </div>
    </div>

    <!-- Créatrices suivies -->
    <div class="creators-grid">
        <?php foreach ($followedCreators as $creator): ?>
            <div class="creator-card">
                <div class="creator-header">
                    <img src="<?= htmlspecialchars($creator['profile_pic_url']) ?>" 
                         alt="<?= htmlspecialchars($creator['name']) ?>" 
                         class="creator-avatar">
                    <div class="creator-info">
                        <h3><?= htmlspecialchars($creator['name']) ?></h3>
                        <p class="creator-tagline"><?= htmlspecialchars($creator['tagline']) ?></p>
                    </div>
                </div>
                
                <div class="creator-stats">
                    <div class="stat">
                        <span class="stat-label">Dernier don</span>
                        <span class="stat-value"><?= date('d/m/Y', strtotime($creator['last_donation'])) ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">Total donné</span>
                        <span class="stat-value"><?= number_format($creator['total_donated'], 2) ?> €</span>
                    </div>
                </div>

                <?php if (!empty($creator['active_pack'])): ?>
                    <div class="active-pack">
                        <i class="fas fa-star"></i>
                        <span>Pack <?= htmlspecialchars($creator['active_pack']) ?></span>
                    </div>
                <?php endif; ?>

                <div class="creator-actions">
                    <a href="/creator/<?= $creator['id'] ?>" class="btn btn-outline">
                        <i class="fas fa-external-link-alt"></i>
                        Voir la page
                    </a>
                    <a href="/donor/donations/<?= $creator['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-gift"></i>
                        Faire un don
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.creators-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.creator-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.creator-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.creator-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.creator-info h3 {
    margin: 0 0 0.25rem;
    color: var(--text-color);
}

.creator-tagline {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.creator-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    padding: 1rem 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    margin-bottom: 1rem;
}

.stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.stat-label {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 0.25rem;
}

.stat-value {
    font-weight: 600;
    color: var(--text-color);
}

.active-pack {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #fff8e1;
    color: #f39c12;
    border-radius: var(--border-radius);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.creator-actions {
    display: flex;
    gap: 1rem;
}

.creator-actions .btn {
    flex: 1;
    justify-content: center;
}

.btn-outline {
    border: 1px solid #ddd;
    background: none;
    color: var(--text-color);
}

.btn-outline:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--secondary-color);
}
</style>

<?php require_once APP_PATH . '/views/layouts/donor_footer.php'; ?>
