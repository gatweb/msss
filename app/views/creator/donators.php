<?php ob_start(); ?>
<div class="donators-dashboard">
    <div class="section-title-bar">
        <h1>Mes donateurs</h1>
        <div class="header-actions">
            <button class="btn btn-outline" onclick="exportDonators()">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>
    </div>

    <div class="donators-grid">
        <!-- Statistiques des donateurs -->
        <div class="stats-cards">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-content">
                    <h3>Total donateurs</h3>
                    <p class="stat-value"><?php echo number_format($stats['total_donators']); ?></p>
                </div>
            </div>

            <div class="stat-card">
                <i class="fas fa-redo"></i>
                <div class="stat-content">
                    <h3>Donateurs réguliers</h3>
                    <p class="stat-value"><?php echo number_format($stats['recurring_donators']); ?></p>
                </div>
            </div>

            <div class="stat-card">
                <i class="fas fa-euro-sign"></i>
                <div class="stat-content">
                    <h3>Don moyen</h3>
                    <p class="stat-value"><?php echo formatAmount($stats['average_donation']); ?></p>
                </div>
            </div>
        </div>

        <!-- Liste des donateurs -->
        <div class="donators-list">
            <div class="list-header">
                <div class="search-box">
                    <input type="text" id="searchDonators" placeholder="Rechercher un donateur..." onkeyup="filterDonators()">
                    <i class="fas fa-search"></i>
                </div>
                <div class="filters">
                    <select id="sortDonators" onchange="sortDonators()">
                        <option value="recent">Plus récent</option>
                        <option value="amount">Montant total</option>
                        <option value="frequency">Fréquence</option>
                    </select>
                </div>
            </div>

            <div class="donators-table">
                <table>
                    <thead>
                        <tr class="donator-row">
                            <th>Statut</th>
                            <th>Dernier don</th>
                            <th>Total des dons</th>
                            <th>Fréquence</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donators as $donator): ?>
                        <tr class="donator-row" onclick="window.location.href='/dashboard/donators/profile?email=<?php echo urlencode($donator['donor_email']); ?>'" style="cursor:pointer;">
                            <td class="led-col">
                                <?php
                                $ledColor = '#95a5a6'; // gris par défaut
                                $status = $donator['crm_status'] ?? 'prospect';
                                $statusLabels = [
                                    'client' => 'Client',
                                    'indesirable' => 'Indésirable',
                                    'attente' => 'En attente',
                                    'prospect' => 'Prospect',
                                    'ancien' => 'Ancien',
                                ];
                                switch ($status) {
                                    case 'client': $ledColor = '#27ae60'; break;
                                    case 'indesirable': $ledColor = '#e74c3c'; break;
                                    case 'attente': $ledColor = '#f39c12'; break;
                                    case 'prospect': $ledColor = '#3498db'; break;
                                    case 'ancien': $ledColor = '#95a5a6'; break;
                                }
                                $label = $statusLabels[$status] ?? ucfirst($status);
                                ?>
                                <span class="led-status" style="background: <?= $ledColor ?>;" title="<?= htmlspecialchars($label) ?>"></span>
<?php
// Affichage étoile + timer si fan fidèle actif
if (!empty($donator['timer_end']) && strtotime($donator['timer_end']) > time()) {
    $diff = strtotime($donator['timer_end']) - time();
    $days = floor($diff / 86400);
    $hours = floor(($diff % 86400) / 3600);
    echo ' <span title="Fan fidèle actif" style="color:#FFD700;font-size:1.2em;vertical-align:middle;">★</span>';
    if ($days > 0 || $hours > 0) {
        echo " <span style='font-size:0.95em;color:#b58900;'>";
        if ($days > 0) echo $days . 'j ';
        if ($hours > 0) echo $hours . 'h';
        echo '</span>';
    }
}
?>
                            </td>
                            <td class="donator-info">
                                <div>
                                    <strong><?php echo htmlspecialchars($donator['donor_name']); ?></strong>
                                    <span><?php echo htmlspecialchars($donator['donor_email']); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="last-donation">
                                    <span><?php echo date('d/m/Y H:i', strtotime($donator['last_donation'])); ?></span>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo number_format($donator['total_amount'], 2, ',', ' '); ?></strong>
                                <span>(<?php echo $donator['donation_count']; ?> dons)</span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">Ponctuel</span>
                            </td>
                            <td>
                                <span class="text-muted">-</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo ($current_page - 1); ?>" class="btn btn-outline">
                        <i class="fas fa-chevron-left"></i> Précédent
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="btn btn-outline <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo ($current_page + 1); ?>" class="btn btn-outline">
                        Suivant <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.donators-dashboard {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.section-title-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-card i {
    font-size: 2rem;
    color: var(--primary-color);
}

.search-box {
    position: relative;
    margin-bottom: 1rem;
}

.search-box input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.donators-table {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.donator-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.led-col {
    text-align: center;
    vertical-align: middle;
    width: 44px;
}
.led-status {
    display: inline-block;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    box-shadow: 0 0 4px rgba(0,0,0,0.13);
    position: relative;
    cursor: pointer;
}
.led-status[title]:hover::after {
    content: attr(title);
    position: absolute;
    left: 50%;
    top: 120%;
    transform: translateX(-50%);
    background: #222;
    color: #fff;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: 0.85em;
    white-space: nowrap;
    z-index: 9999;
    opacity: 0.98;
    pointer-events: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.17);
}

.donator-info img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.donator-info div {
    display: flex;
    flex-direction: column;
}

.donator-info span {
    font-size: 0.9rem;
    color: #666;
}

.last-donation {
    display: flex;
    flex-direction: column;
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge-success {
    background: #e1f7e1;
    color: #2ecc71;
}

.badge-secondary {
    background: #f5f6f7;
    color: #95a5a6;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .donators-dashboard {
        padding: 1rem;
    }

    .section-title-bar {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .stats-cards {
        grid-template-columns: 1fr;
    }

    .donators-table {
        font-size: 0.9rem;
    }

    th, td {
        padding: 0.75rem;
    }
}
</style>

<script>
function filterDonators() {
    // À implémenter : filtrage des donateurs
    const searchTerm = document.getElementById('searchDonators').value;
    console.log('Filtering donators:', searchTerm);
}

function sortDonators() {
    // À implémenter : tri des donateurs
    const sortBy = document.getElementById('sortDonators').value;
    console.log('Sorting donators by:', sortBy);
}

function exportDonators() {
    // À implémenter : export des données des donateurs
    console.log('Exporting donators data');
}

function sendMessage(donatorId) {
    // À implémenter : envoi de message à un donateur
    console.log('Sending message to donator:', donatorId);
}
</script>
