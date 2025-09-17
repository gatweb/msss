<?php $pageTitle = 'Tableau de bord'; ?>
<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Tableau de bord administrateur</h1>
        <div class="date-range">
            <button class="btn btn-outline" onclick="updateDateRange('7d')">7 jours</button>
            <button class="btn btn-outline" onclick="updateDateRange('30d')">30 jours</button>
            <button class="btn btn-outline" onclick="updateDateRange('1y')">1 an</button>
        </div>
    </div>

    <!-- Cartes des statistiques -->
<a href="/admin/stats" class="btn btn-link" style="float:right;margin-bottom:1rem;">
    <i class="fas fa-chart-bar"></i> Voir les statistiques détaillées
</a>
    <div class="stats-cards">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div class="stat-content">
                <h3>Créateurs actifs</h3>
                <p class="stat-value"><?php echo number_format($stats['total_creators']); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <i class="fas fa-hand-holding-heart"></i>
            <div class="stat-content">
                <h3>Total des dons</h3>
                <p class="stat-value"><?php echo number_format($stats['total_donations']); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <i class="fas fa-euro-sign"></i>
            <div class="stat-content">
                <h3>Montant total</h3>
                <p class="stat-value"><?php echo formatAmount($stats['total_amount']); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <i class="fas fa-box-open"></i>
            <div class="stat-content">
                <h3>Packs actifs</h3>
                <p class="stat-value"><?php echo number_format($stats['total_packs']); ?></p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Graphique de l'évolution des dons -->
        <div class="dashboard-card donations-chart">
            <h2>Évolution des dons</h2>
            <canvas id="donationsChart"></canvas>
        </div>

        <!-- Top créateurs -->
        <div class="dashboard-card top-creators">
            <h2>Top créateurs</h2>
            <div class="creators-list">
                <?php foreach ($stats['top_creators'] as $creator): ?>
                    <div class="creator-item">
                        <img src="<?php echo e($creator['profile_pic_url'] ?? '/assets/img/default-avatar.png'); ?>" alt="<?php echo e($creator['name']); ?>">
                        <div class="creator-info">
                            <h3><?php echo e($creator['name']); ?></h3>
                            <p><?php echo formatAmount($creator['total_amount']); ?> (<?php echo $creator['donation_count']; ?> dons)</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Types de dons -->
        <div class="dashboard-card donation-types">
            <h2>Types de dons</h2>
            <canvas id="donationTypesChart"></canvas>
        </div>

        <!-- Derniers dons -->
        <div class="dashboard-card recent-donations">
            <h2>Derniers dons</h2>
            <div class="donations-list">
                <?php foreach ($stats['recent_donations'] as $donation): ?>
                    <div class="donation-item">
                        <div class="donation-info">
                            <p class="donation-amount"><?php echo formatAmount($donation['amount']); ?></p>
                            <p class="donation-creator">pour <?php echo e($donation['creator_name']); ?></p>
                        </div>
                        <p class="donation-date"><?php echo formatDate($donation['created_at']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    margin: 0;
    color: #2c3e50;
}

.date-range {
    display: flex;
    gap: 1rem;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-card i {
    font-size: 2rem;
    color: #3498db;
}

.stat-content h3 {
    margin: 0;
    font-size: 0.9rem;
    color: #7f8c8d;
}

.stat-value {
    margin: 0;
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.dashboard-card {
    background: #fff;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dashboard-card h2 {
    margin: 0 0 1.5rem;
    color: #2c3e50;
    font-size: 1.25rem;
}

.donations-chart {
    grid-column: 1 / -1;
}

.creators-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.creator-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.creator-item:hover {
    background-color: #f8f9fa;
}

.creator-item img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.creator-info h3 {
    margin: 0;
    font-size: 1rem;
    color: #2c3e50;
}

.creator-info p {
    margin: 0;
    font-size: 0.9rem;
    color: #7f8c8d;
}

.donations-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.donation-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.donation-item:hover {
    background-color: #f8f9fa;
}

.donation-amount {
    margin: 0;
    font-weight: bold;
    color: #2c3e50;
}

.donation-creator {
    margin: 0;
    font-size: 0.9rem;
    color: #7f8c8d;
}

.donation-date {
    margin: 0;
    font-size: 0.8rem;
    color: #95a5a6;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .stats-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique de l'évolution des dons
const monthlyStats = <?php echo json_encode($stats['monthly_stats']); ?>;
const months = monthlyStats.map(stat => stat.month);
const amounts = monthlyStats.map(stat => parseFloat(stat.total_amount));
const counts = monthlyStats.map(stat => parseInt(stat.donation_count));

new Chart(document.getElementById('donationsChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Montant total (€)',
                data: amounts,
                borderColor: '#3498db',
                tension: 0.4,
                yAxisID: 'y'
            },
            {
                label: 'Nombre de dons',
                data: counts,
                borderColor: '#2ecc71',
                tension: 0.4,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

// Graphique des types de dons
const donationTypes = <?php echo json_encode($stats['donation_types']); ?>;
const types = donationTypes.map(type => type.donation_type);
const typeAmounts = donationTypes.map(type => parseFloat(type.total_amount));

new Chart(document.getElementById('donationTypesChart'), {
    type: 'doughnut',
    data: {
        labels: types,
        datasets: [{
            data: typeAmounts,
            backgroundColor: [
                '#3498db',
                '#2ecc71',
                '#e74c3c',
                '#f1c40f',
                '#9b59b6'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Mise à jour de la plage de dates
function updateDateRange(range) {
    // À implémenter : appel AJAX pour mettre à jour les données
    console.log('Updating date range:', range);
}
</script>
