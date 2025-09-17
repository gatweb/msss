<?php ob_start(); ?>
<div class="stats-dashboard">
    <div class="section-title-bar">
        <h1>Statistiques détaillées</h1>
        <div class="section-title-bar">
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

    <!-- Métriques principales -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="section-title-bar">
                <h3>Revenus totaux</h3>
                <select onchange="updateMetricView('revenue', this.value)">
                    <option value="daily">Par jour</option>
                    <option value="weekly">Par semaine</option>
                    <option value="monthly" selected>Par mois</option>
                </select>
            </div>
            <div class="metric-chart">
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="metric-footer">
                <div class="metric-total">
                    <span class="label">Total période</span>
                    <span class="value"><?= number_format($stats['total_revenue'], 2) ?> €</span>
                </div>
                <div class="metric-trend <?= $stats['revenue_trend'] >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= $stats['revenue_trend'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= abs($stats['revenue_trend']) ?>%
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="section-title-bar">
                <h3>Nouveaux donateurs</h3>
                <select onchange="updateMetricView('donors', this.value)">
                    <option value="daily">Par jour</option>
                    <option value="weekly">Par semaine</option>
                    <option value="monthly" selected>Par mois</option>
                </select>
            </div>
            <div class="metric-chart">
                <canvas id="donorsChart"></canvas>
            </div>
            <div class="metric-footer">
                <div class="metric-total">
                    <span class="label">Total période</span>
                    <span class="value"><?= $stats['new_donors'] ?></span>
                </div>
                <div class="metric-trend <?= $stats['donors_trend'] >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= $stats['donors_trend'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= abs($stats['donors_trend']) ?>%
                </div>
            </div>
        </div>
    </div>

    <!-- Analyses détaillées -->
    <div class="analysis-grid">
        <!-- Distribution des dons -->
        <div class="analysis-card">
            <h3>Distribution des dons</h3>
            <div class="chart-container">
                <canvas id="donationDistributionChart"></canvas>
            </div>
            <div class="analysis-insights">
                <div class="insight">
                    <i class="fas fa-bullseye"></i>
                    <div class="insight-content">
                        <h4>Don médian</h4>
                        <p><?= number_format($stats['median_donation'], 2) ?> €</p>
                    </div>
                </div>
                <div class="insight">
                    <i class="fas fa-chart-line"></i>
                    <div class="insight-content">
                        <h4>Don moyen</h4>
                        <p><?= number_format($stats['average_donation'], 2) ?> €</p>
                    </div>
                </div>
                <div class="insight">
                    <i class="fas fa-trophy"></i>
                    <div class="insight-content">
                        <h4>Don le plus élevé</h4>
                        <p><?= number_format($stats['highest_donation'], 2) ?> €</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rétention des donateurs -->
        <div class="analysis-card">
            <h3>Rétention des donateurs</h3>
            <div class="chart-container">
                <canvas id="retentionChart"></canvas>
            </div>
            <div class="analysis-insights">
                <div class="insight">
                    <i class="fas fa-users"></i>
                    <div class="insight-content">
                        <h4>Taux de rétention</h4>
                        <p><?= number_format($stats['retention_rate'], 1) ?>%</p>
                    </div>
                </div>
                <div class="insight">
                    <i class="fas fa-sync"></i>
                    <div class="insight-content">
                        <h4>Donateurs réguliers</h4>
                        <p><?= $stats['regular_donors'] ?></p>
                    </div>
                </div>
                <div class="insight">
                    <i class="fas fa-history"></i>
                    <div class="insight-content">
                        <h4>Durée moyenne</h4>
                        <p><?= $stats['average_donor_lifetime'] ?> mois</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance des packs -->
        <div class="analysis-card full-width">
            <h3>Performance des packs</h3>
            <div class="packs-table-container">
                <table class="packs-table">
                    <thead>
                        <tr>
                            <th>Pack</th>
                            <th>Prix</th>
                            <th>Abonnés</th>
                            <th>Revenu mensuel</th>
                            <th>Taux de rétention</th>
                            <th>Croissance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['packs_performance'] as $pack): ?>
                            <tr>
                                <td>
                                    <div class="pack-name">
                                        <?= htmlspecialchars($pack['name']) ?>
                                        <?php if ($pack['is_best_performer']): ?>
                                            <span class="badge success">Meilleure performance</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= number_format($pack['price'], 2) ?> €</td>
                                <td><?= $pack['subscribers'] ?></td>
                                <td><?= number_format($pack['monthly_revenue'], 2) ?> €</td>
                                <td>
                                    <div class="retention-bar" style="--retention: <?= $pack['retention_rate'] ?>%">
                                        <?= number_format($pack['retention_rate'], 1) ?>%
                                    </div>
                                </td>
                                <td>
                                    <span class="trend <?= $pack['growth'] >= 0 ? 'positive' : 'negative' ?>">
                                        <i class="fas fa-<?= $pack['growth'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                                        <?= abs($pack['growth']) ?>%
                                    </span>
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

.section-title-bar {
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

/* Métriques principales */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin: 2rem 0;
}

.metric-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-title-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title-bar h3 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--text-color);
}

.section-title-bar select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    font-size: 0.9rem;
}

.metric-chart {
    height: 300px;
    margin-bottom: 1.5rem;
}

.metric-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.metric-total {
    display: flex;
    flex-direction: column;
}

.metric-total .label {
    font-size: 0.9rem;
    color: #666;
}

.metric-total .value {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-color);
}

.metric-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.metric-trend.positive {
    color: var(--success-color);
}

.metric-trend.negative {
    color: var(--danger-color);
}

/* Analyses détaillées */
.analysis-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.analysis-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.analysis-card.full-width {
    grid-column: 1 / -1;
}

.analysis-card h3 {
    margin: 0 0 1.5rem;
    font-size: 1.1rem;
    color: var(--text-color);
}

.chart-container {
    height: 250px;
    margin-bottom: 1.5rem;
}

.analysis-insights {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.insight {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.insight i {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.insight-content h4 {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
}

.insight-content p {
    margin: 0.25rem 0 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-color);
}

/* Table des packs */
.packs-table-container {
    overflow-x: auto;
}

.packs-table {
    width: 100%;
    border-collapse: collapse;
}

.packs-table th,
.packs-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.packs-table th {
    font-weight: 500;
    color: #666;
    white-space: nowrap;
}

.pack-name {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

.badge.success {
    background: #e6fff2;
    color: var(--success-color);
}

.retention-bar {
    position: relative;
    width: 100%;
    height: 24px;
    background: #f5f6fa;
    border-radius: 12px;
    overflow: hidden;
}

.retention-bar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: var(--retention);
    background: var(--primary-color);
    border-radius: 12px;
}

.retention-bar::after {
    content: attr(data-value);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--text-color);
    font-weight: 500;
    mix-blend-mode: difference;
}

/* Responsive */
@media (max-width: 1024px) {
    .metrics-grid,
    .analysis-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .section-title-bar {
        flex-direction: column;
    }

    .date-range {
        width: 100%;
        justify-content: space-between;
    }

    .analysis-insights {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Configuration des graphiques
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
};

// Graphique des revenus
const revenueData = <?= json_encode($stats['revenue_data']) ?>;
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: revenueData.labels,
        datasets: [{
            label: 'Revenus',
            data: revenueData.values,
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

// Graphique des nouveaux donateurs
const donorsData = <?= json_encode($stats['donors_data']) ?>;
new Chart(document.getElementById('donorsChart'), {
    type: 'bar',
    data: {
        labels: donorsData.labels,
        datasets: [{
            label: 'Nouveaux donateurs',
            data: donorsData.values,
            backgroundColor: '#a29bfe'
        }]
    },
    options: chartOptions
});

// Distribution des dons
const distributionData = <?= json_encode($stats['distribution_data']) ?>;
new Chart(document.getElementById('donationDistributionChart'), {
    type: 'bar',
    data: {
        labels: distributionData.ranges,
        datasets: [{
            label: 'Nombre de dons',
            data: distributionData.counts,
            backgroundColor: '#74b9ff'
        }]
    },
    options: {
        ...chartOptions,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Montant du don (€)'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Nombre de dons'
                }
            }
        }
    }
});

// Rétention des donateurs
const retentionData = <?= json_encode($stats['retention_data']) ?>;
new Chart(document.getElementById('retentionChart'), {
    type: 'line',
    data: {
        labels: retentionData.months,
        datasets: [{
            label: 'Taux de rétention',
            data: retentionData.rates,
            borderColor: '#00b894',
            backgroundColor: 'rgba(0, 184, 148, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        ...chartOptions,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: value => value + '%'
                }
            }
        }
    }
});

// Mise à jour des vues
function updateDateRange(range) {
    document.querySelectorAll('.date-range .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Recharger les données
    fetch(`/api/creator/stats?range=${range}`)
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les graphiques et statistiques
            updateCharts(data);
            updateMetrics(data);
        });
}

function updateMetricView(metric, view) {
    fetch(`/api/creator/stats/${metric}?view=${view}`)
        .then(response => response.json())
        .then(data => {
            // Mettre à jour le graphique spécifique
            updateChart(metric, data);
        });
}

function exportStats() {
    const range = document.querySelector('.date-range .btn.active').textContent;
    window.location.href = `/api/creator/stats/export?range=${range}`;
}
</script>

