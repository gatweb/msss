<?php $pageTitle = 'Statistiques avancées'; ?>

<a href="/admin" class="btn btn-link" style="float:right;margin-bottom:1rem;">
    <i class="fas fa-tachometer-alt"></i> Retour au tableau de bord
</a>
<div class="stats-dashboard">
    <div class="page-header">
        <h1>Statistiques Globales</h1>
        <div class="header-actions">
            <div class="date-range">
                <button class="btn btn-outline" onclick="updateDateRange('7d')">7 jours</button>
                <button class="btn btn-outline active" onclick="updateDateRange('30d')">30 jours</button>
                <button class="btn btn-outline" onclick="updateDateRange('90d')">90 jours</button>
                <button class="btn btn-outline" onclick="updateDateRange('1y')">1 an</button>
            </div>
            <button class="btn btn-outline" onclick="exportStats()">
                <i class="fas fa-download"></i>
                Exporter
            </button>
        </div>
    </div>

    <!-- Vue d'ensemble -->
    <div class="overview-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>Créatrices Actives</h3>
                <p class="stat-value"><?= $stats['active_creators'] ?></p>
                <p class="stat-trend <?= $stats['creators_trend'] >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= $stats['creators_trend'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= abs($stats['creators_trend']) ?>% ce mois
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-content">
                <h3>Total des Dons</h3>
                <p class="stat-value"><?= number_format($stats['total_donations'], 2) ?> €</p>
                <p class="stat-trend <?= $stats['donations_trend'] >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= $stats['donations_trend'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= abs($stats['donations_trend']) ?>% ce mois
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-gift"></i>
            </div>
            <div class="stat-content">
                <h3>Packs Actifs</h3>
                <p class="stat-value"><?= $stats['active_packs'] ?></p>
                <p class="stat-subtitle"><?= $stats['total_packs'] ?> packs au total</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>Don Moyen</h3>
                <p class="stat-value"><?= number_format($stats['average_donation'], 2) ?> €</p>
                <p class="stat-subtitle">sur <?= $stats['total_transactions'] ?> transactions</p>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="charts-grid">
        <!-- Évolution des dons -->
        <div class="chart-card">
            <h3>Évolution des Dons</h3>
            <div class="chart-container">
                <canvas id="donationsChart"></canvas>
            </div>
        </div>

        <!-- Nouvelles créatrices -->
        <div class="chart-card">
            <h3>Nouvelles Créatrices</h3>
            <div class="chart-container">
                <canvas id="creatorsChart"></canvas>
            </div>
        </div>

        <!-- Top créatrices -->
        <div class="chart-card full-width">
            <h3>Top 10 Créatrices</h3>
            <div class="ranking-table-container">
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>Créatrice</th>
                            <th>Total Dons</th>
                            <th>Donateurs</th>
                            <th>Don Moyen</th>
                            <th>Croissance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['top_creators'] as $creator): ?>
                            <tr>
                                <td>
                                    <div class="creator-info">
                                        <img src="<?= $creator['avatar'] ?? '/assets/img/default-avatar.png' ?>" 
                                             alt="<?= htmlspecialchars($creator['name']) ?>"
                                             class="creator-avatar">
                                        <div>
                                            <strong><?= htmlspecialchars($creator['name']) ?></strong>
                                            <span class="creator-since">Depuis <?= formatDate($creator['created_at']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?= number_format($creator['total_donations'], 2) ?> €</td>
                                <td><?= $creator['total_donors'] ?></td>
                                <td><?= number_format($creator['average_donation'], 2) ?> €</td>
                                <td>
                                    <span class="trend <?= $creator['growth'] >= 0 ? 'positive' : 'negative' ?>">
                                        <i class="fas fa-<?= $creator['growth'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                                        <?= abs($creator['growth']) ?>%
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn btn-icon" onclick="viewCreator(<?= $creator['id'] ?>)" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-icon" onclick="contactCreator(<?= $creator['id'] ?>)" title="Contacter">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.stats-dashboard {
    padding: 2rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.date-range {
    display: flex;
    gap: 0.5rem;
}

.date-range .btn-outline.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* Vue d'ensemble */
.overview-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin: 2rem 0;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--primary-light);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-content {
    flex: 1;
}

.stat-content h3 {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
}

.stat-value {
    margin: 0.5rem 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-color);
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.stat-trend.positive {
    color: var(--success-color);
}

.stat-trend.negative {
    color: var(--danger-color);
}

.stat-subtitle {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
}

/* Graphiques */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-top: 2rem;
}

.chart-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chart-card.full-width {
    grid-column: 1 / -1;
}

.chart-card h3 {
    margin: 0 0 1.5rem;
    font-size: 1.1rem;
    color: var(--text-color);
}

.chart-container {
    height: 300px;
}

/* Table de classement */
.ranking-table-container {
    overflow-x: auto;
}

.ranking-table {
    width: 100%;
    border-collapse: collapse;
}

.ranking-table th,
.ranking-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.ranking-table th {
    font-weight: 500;
    color: #666;
    white-space: nowrap;
}

.creator-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.creator-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.creator-since {
    display: block;
    font-size: 0.85rem;
    color: #666;
}

.table-actions {
    display: flex;
    gap: 0.5rem;
}

/* Responsive */
@media (max-width: 1200px) {
    .overview-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .overview-grid {
        grid-template-columns: 1fr;
    }

    .charts-grid {
        grid-template-columns: 1fr;
    }

    .header-actions {
        flex-direction: column;
    }

    .date-range {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<script>
// Configuration des graphiques
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
};

// Graphique des dons
const donationsData = <?= json_encode($stats['donations_data']) ?>;
new Chart(document.getElementById('donationsChart'), {
    type: 'line',
    data: {
        labels: donationsData.labels,
        datasets: [{
            label: 'Total des dons',
            data: donationsData.values,
            borderColor: '#6c5ce7',
            backgroundColor: 'rgba(108, 92, 231, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        ...chartOptions,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => value + ' €'
                }
            }
        }
    }
});

// Graphique des créatrices
const creatorsData = <?= json_encode($stats['creators_data']) ?>;
new Chart(document.getElementById('creatorsChart'), {
    type: 'bar',
    data: {
        labels: creatorsData.labels,
        datasets: [{
            label: 'Nouvelles créatrices',
            data: creatorsData.values,
            backgroundColor: '#a29bfe'
        }]
    },
    options: chartOptions
});

// Fonctions d'interaction
function updateDateRange(range) {
    document.querySelectorAll('.date-range .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Recharger les données
    fetch(`/api/admin/stats?range=${range}`)
        .then(response => response.json())
        .then(data => {
            updateCharts(data);
            updateStats(data);
        });
}

function viewCreator(id) {
    window.location.href = `/admin/creators/${id}`;
}

function contactCreator(id) {
    // Implémenter la fonctionnalité de contact
    console.log('Contacting creator:', id);
}

function exportStats() {
    const range = document.querySelector('.date-range .btn.active').textContent;
    window.location.href = `/api/admin/stats/export?range=${range}`;
}

// Mise à jour des données
function updateCharts(data) {
    // Mettre à jour les graphiques avec les nouvelles données
    donationsChart.data = data.donations_chart;
    creatorsChart.data = data.creators_chart;
    donationsChart.update();
    creatorsChart.update();
}

function updateStats(data) {
    // Mettre à jour les statistiques globales
    document.querySelectorAll('.stat-value').forEach(el => {
        const key = el.dataset.stat;
        if (data[key]) {
            el.textContent = data[key];
        }
    });
}
</script>

