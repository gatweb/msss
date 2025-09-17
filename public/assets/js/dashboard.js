// Fonction pour formater les montants
function formatAmount(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

// Fonction pour formater les dates
function formatDate(dateString) {
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(dateString));
}

// Mise à jour en temps réel des statistiques
function updateStats() {
    fetch('/api/dashboard/stats')
        .then(response => response.json())
        .then(stats => {
            // Mise à jour des statistiques
            document.querySelector('.total-donations').textContent = formatAmount(stats.total_donations);
            document.querySelector('.donor-count').textContent = stats.donor_count;
            
            // Mise à jour de la barre de progression
            const progressBar = document.querySelector('.progress');
            const progressPercentage = Math.min(100, (stats.total_donations / stats.donation_goal) * 100);
            progressBar.style.width = `${progressPercentage}%`;
            
            // Mise à jour du texte de progression
            document.querySelector('.progress-text').textContent = 
                `${formatAmount(stats.total_donations)} / ${formatAmount(stats.donation_goal)}`;
        })
        .catch(error => console.error('Erreur lors de la mise à jour des stats:', error));
}

// Mise à jour des dons récents
function updateRecentDonations() {
    fetch('/api/dashboard/recent-donations')
        .then(response => response.json())
        .then(donations => {
            const container = document.querySelector('.recent-donations');
            
            // Si pas de dons
            if (donations.length === 0) {
                container.innerHTML = '<p class="empty-state">Aucun don récent</p>';
                return;
            }
            
            // Mise à jour des dons
            const donationsHtml = donations.map(donation => `
                <div class="donation-card">
                    <div class="donor-info">
                        <span class="donor-name">${donation.donor_name}</span>
                        <span class="donation-time">${formatDate(donation.donation_timestamp)}</span>
                    </div>
                    <div class="donation-amount">${formatAmount(donation.amount)}</div>
                    ${donation.comment ? `
                        <div class="donation-comment">${donation.comment}</div>
                    ` : ''}
                </div>
            `).join('');
            
            container.innerHTML = donationsHtml;
        })
        .catch(error => console.error('Erreur lors de la mise à jour des dons:', error));
}

// Fonction pour envoyer une relance
function sendReminder(donorName) {
    fetch('/api/dashboard/send-reminder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ donorName })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(`Relance envoyée à ${donorName}`);
        } else {
            alert('Erreur lors de l\'envoi de la relance');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi de la relance');
    });
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    // Temporairement désactivé en attendant l'implémentation des API
    // updateStats();
    // updateRecentDonations();
    // setInterval(updateStats, 60000);
    // setInterval(updateRecentDonations, 30000);

    // Toggle sidebar on mobile
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.dashboard-sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // Close sidebar when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = sidebarToggle.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        }
    });

    // User menu dropdown
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(event) {
            event.stopPropagation();
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.style.display = 'none';
        });

        dropdownMenu.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});
