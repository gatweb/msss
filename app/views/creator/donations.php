
<?php ob_start(); ?>
<?php ob_start(); ?>
<?php
$hideNavLinks = true;
$donations = $donations ?? [];
$stats = $stats ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
?>
<div class="dashboard-container">
    <!-- En-tête -->
    <div class="section-title-bar">
        <h1>Gestion des Dons</h1>
        <div class="header-actions">
            <button class="btn btn-outline" id="exportDonations">
                <i class="fas fa-download"></i> Exporter
            </button>
            <div class="dropdown">
                <button class="btn btn-outline dropdown-toggle">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <div class="dropdown-menu">
                    <a href="#" data-period="today">Aujourd'hui</a>
                    <a href="#" data-period="week">Cette semaine</a>
                    <a href="#" data-period="month">Ce mois</a>
                    <a href="#" data-period="year">Cette année</a>
                    <a href="#" data-period="all">Tout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des dons -->
    <div class="donations-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-content">
                <h3>Total des Dons</h3>
                <p class="stat-value"><?= number_format($stats['total_amount'] ?? 0, 2) ?> €</p>
                <?php if (isset($stats['trend'])): ?>
                <p class="stat-trend <?= $stats['trend'] >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= $stats['trend'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= abs($stats['trend']) ?>% vs mois dernier
                </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>Donateurs Uniques</h3>
                <p class="stat-value"><?= $stats['unique_donors'] ?? 0 ?></p>
                <p class="stat-detail">Don moyen : <?= number_format($stats['average_donation'] ?? 0, 2) ?> €</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="stat-content">
                <h3>Types de Dons</h3>
                <canvas id="donationTypesChart" width="100" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Graphique des dons -->
    <div class="donations-chart-container">
        <div class="section-title-bar">
            <h2>Évolution des Dons</h2>
            <div class="chart-period-selector">
                <button class="btn btn-sm active" data-period="week">7J</button>
                <button class="btn btn-sm" data-period="month">30J</button>
                <button class="btn btn-sm" data-period="year">1A</button>
            </div>
        </div>
        <canvas id="donationsChart"></canvas>
    </div>

    <!-- Liste des dons -->
    <div class="donations-list-container">
        <div class="section-title-bar">
            <h2>Historique des Dons</h2>
            <div class="list-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchDonations" placeholder="Rechercher un donateur...">
                </div>
                <select id="sortDonations">
                    <option value="date_desc">Plus récents</option>
                    <option value="date_asc">Plus anciens</option>
                    <option value="amount_desc">Montant (décroissant)</option>
                    <option value="amount_asc">Montant (croissant)</option>
                </select>
            </div>
        </div>

        <div class="donations-table-container">
            <table class="donations-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Donateur</th>
                        <th>Montant</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $donation): ?>
                        <tr data-id="<?= $donation['id'] ?>">
                            <td>
                                <div class="donation-date">
                                    <?= date('d/m/Y', strtotime($donation['donation_timestamp'])) ?>
                                    <span class="donation-time">
                                        <?= date('H:i', strtotime($donation['donation_timestamp'])) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="donor-info">
                                    <span class="donor-name">
                                        <?= htmlspecialchars($donation['donor_name']) ?>
                                    </span>
                                    <?php if ($donation['is_recurring']): ?>
                                        <span class="badge badge-recurring" title="Donateur régulier">
                                            <i class="fas fa-sync-alt"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="donation-amount">
                                    <?= number_format($donation['amount'], 2) ?> €
                                </div>
                            </td>
                            <td>
                                <span class="donation-type <?= strtolower($donation['donation_type']) ?>">
                                    <?= $donation['donation_type'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($donation['timer_status'] === 'running'): ?>
                                    <div class="timer-status">
                                        <i class="fas fa-clock"></i>
                                        <span class="timer" data-start="<?= $donation['timer_start_time'] ?>"
                                              data-elapsed="<?= $donation['timer_elapsed_seconds'] ?>">
                                            00:00:00
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="donation-status completed">
                                        <i class="fas fa-check"></i> Terminé
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="donation-actions">
                                    <?php if ($donation['timer_status'] === 'running'): ?>
                                        <button class="btn btn-icon stop-timer" title="Arrêter le timer">
                                            <i class="fas fa-stop"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-icon view-donation" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-icon delete-donation" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="btn btn-outline">&laquo; Précédent</a>
            <?php endif; ?>
            
            <span class="page-info">Page <?= $currentPage ?> sur <?= $totalPages ?></span>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="btn btn-outline">Suivant &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Détails du Don -->
<div class="modal" id="donationDetailsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Don</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="donation-details">
                    <!-- Les détails seront chargés dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/donations.js"></script>

<?php require_once APP_PATH . '/views/layouts/dashboard_footer.php'; ?>

<!-- Modal Détails du Don -->
<div class="modal" id="donationDetailsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Don</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="donation-details">
                    <!-- Les détails seront chargés dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/donations.js"></script>

<?php require_once APP_PATH . '/views/layouts/dashboard_footer.php'; ?>
