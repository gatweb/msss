document.addEventListener('DOMContentLoaded', () => {
    // Initialisation des graphiques
    initDonationTypesPieChart();
    initDonationsLineChart();

    // Initialisation des timers actifs
    initializeTimers();

    // Écouteurs d'événements
    setupEventListeners();
});

// Graphique des types de dons
function initDonationTypesPieChart() {
    const ctx = document.getElementById('donationTypesChart').getContext('2d');
    
    fetch('/api/dashboard/donation-types')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        data: Object.values(data),
                        backgroundColor: [
                            '#FF69B4',
                            '#4A90E2',
                            '#50E3C2',
                            '#F5A623'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
}

// Graphique d'évolution des dons
function initDonationsLineChart() {
    const ctx = document.getElementById('donationsChart').getContext('2d');
    let chart = null;

    function updateChart(period) {
        fetch(`/api/dashboard/donations-evolution?period=${period}`)
            .then(response => response.json())
            .then(data => {
                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Montant des dons',
                            data: data.values,
                            borderColor: '#FF69B4',
                            backgroundColor: 'rgba(255, 105, 180, 0.1)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => value + ' €'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });
    }

    // Initialisation avec la période par défaut (semaine)
    updateChart('week');

    // Écouteurs pour les boutons de période
    document.querySelectorAll('.chart-period-selector button').forEach(button => {
        button.addEventListener('click', (e) => {
            document.querySelector('.chart-period-selector button.active')
                .classList.remove('active');
            e.target.classList.add('active');
            updateChart(e.target.dataset.period);
        });
    });
}

// Gestion des timers
function initializeTimers() {
    const timers = document.querySelectorAll('.timer');
    
    timers.forEach(timer => {
        const startTime = new Date(timer.dataset.start).getTime();
        const elapsed = parseInt(timer.dataset.elapsed);
        
        if (timer.closest('.timer-status')) {
            updateTimer(timer, startTime, elapsed);
            setInterval(() => updateTimer(timer, startTime, elapsed), 1000);
        }
    });
}

function updateTimer(timerElement, startTime, initialElapsed) {
    const now = new Date().getTime();
    const elapsed = initialElapsed + Math.floor((now - startTime) / 1000);
    
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = elapsed % 60;
    
    timerElement.textContent = 
        `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

// Configuration des écouteurs d'événements
function setupEventListeners() {
    // Recherche
    const searchInput = document.getElementById('searchDonations');
    searchInput?.addEventListener('input', debounce((e) => {
        filterDonations(e.target.value);
    }, 300));

    // Tri
    const sortSelect = document.getElementById('sortDonations');
    sortSelect?.addEventListener('change', (e) => {
        sortDonations(e.target.value);
    });

    // Export
    const exportBtn = document.getElementById('exportDonations');
    exportBtn?.addEventListener('click', exportDonations);

    // Actions sur les dons
    document.querySelectorAll('.stop-timer').forEach(btn => {
        btn.addEventListener('click', handleStopTimer);
    });

    document.querySelectorAll('.view-donation').forEach(btn => {
        btn.addEventListener('click', handleViewDonation);
    });

    document.querySelectorAll('.delete-donation').forEach(btn => {
        btn.addEventListener('click', handleDeleteDonation);
    });

    // Filtres de période
    document.querySelectorAll('.dropdown-menu a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            filterByPeriod(e.target.dataset.period);
        });
    });
}

// Fonctions de gestion des dons
function filterDonations(query) {
    const rows = document.querySelectorAll('.donations-table tbody tr');
    
    rows.forEach(row => {
        const donorName = row.querySelector('.donor-name').textContent.toLowerCase();
        row.style.display = donorName.includes(query.toLowerCase()) ? '' : 'none';
    });
}

function sortDonations(criteria) {
    const tbody = document.querySelector('.donations-table tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        switch(criteria) {
            case 'date_desc':
            case 'date_asc':
                const dateA = new Date(a.querySelector('.donation-date').textContent);
                const dateB = new Date(b.querySelector('.donation-date').textContent);
                return criteria === 'date_desc' ? dateB - dateA : dateA - dateB;
                
            case 'amount_desc':
            case 'amount_asc':
                const amountA = parseFloat(a.querySelector('.donation-amount').textContent);
                const amountB = parseFloat(b.querySelector('.donation-amount').textContent);
                return criteria === 'amount_desc' ? amountB - amountA : amountA - amountB;
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

function exportDonations() {
    const period = document.querySelector('.dropdown-menu a.active')?.dataset.period || 'all';
    window.location.href = `/api/dashboard/export-donations?period=${period}`;
}

async function handleStopTimer(e) {
    const row = e.target.closest('tr');
    const donationId = row.dataset.id;
    
    try {
        const response = await fetch(`/api/donations/${donationId}/stop-timer`, {
            method: 'POST'
        });
        
        const result = await response.json();
        
        if (result.success) {
            const timerStatus = row.querySelector('.timer-status');
            timerStatus.innerHTML = `
                <span class="donation-status completed">
                    <i class="fas fa-check"></i> Terminé
                </span>
            `;
            e.target.remove();
        } else {
            showNotification('Erreur lors de l\'arrêt du timer', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

async function handleViewDonation(e) {
    const donationId = e.target.closest('tr').dataset.id;
    
    try {
        const response = await fetch(`/api/donations/${donationId}`);
        const donation = await response.json();
        
        const modalBody = document.querySelector('#donationDetailsModal .donation-details');
        modalBody.innerHTML = `
            <div class="detail-row">
                <strong>Donateur:</strong> ${donation.donor_name}
            </div>
            <div class="detail-row">
                <strong>Montant:</strong> ${donation.amount} €
            </div>
            <div class="detail-row">
                <strong>Date:</strong> ${new Date(donation.donation_timestamp).toLocaleString()}
            </div>
            <div class="detail-row">
                <strong>Type:</strong> ${donation.donation_type}
            </div>
            ${donation.comment ? `
                <div class="detail-row">
                    <strong>Commentaire:</strong>
                    <p class="comment">${donation.comment}</p>
                </div>
            ` : ''}
        `;
        
        openModal('donationDetailsModal');
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement des détails', 'error');
    }
}

async function handleDeleteDonation(e) {
    if (!confirm('Voulez-vous vraiment supprimer ce don ?')) return;
    
    const row = e.target.closest('tr');
    const donationId = row.dataset.id;
    
    try {
        const response = await fetch(`/api/donations/${donationId}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            row.remove();
            showNotification('Don supprimé', 'success');
        } else {
            showNotification('Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

function filterByPeriod(period) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('period', period);
    window.location.href = currentUrl.toString();
}

// Utilitaires
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
