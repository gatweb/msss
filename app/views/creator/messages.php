<div class="card messages-page-card">
    <header class="page-section-header">
        <div class="page-section-heading">
            <p class="page-section-label">Messagerie</p>
            <h2 class="page-section-title">
                <i class="fas fa-envelope"></i>
                Messages des donateurs
            </h2>
        </div>
    </header>

    <div class="messages-actions">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" placeholder="Rechercher..." onkeyup="filterMessages(this.value)">
        </div>
        <div class="filter-dropdown">
            <button class="btn btn-outline" onclick="toggleFilterMenu()">
                <i class="fas fa-filter"></i>
                Filtrer
            </button>
            <div class="filter-menu" style="display: none;">
                <label class="filter-option">
                    <input type="checkbox" checked onchange="toggleFilter('unread')">
                    Non lus
                </label>
                <label class="filter-option">
                    <input type="checkbox" checked onchange="toggleFilter('read')">
                    Lus
                </label>
                <label class="filter-option">
                    <input type="checkbox" checked onchange="toggleFilter('with_donation')">
                    Avec don
                </label>
            </div>
        </div>
    </div>

    <div class="messages-layout">
        <div class="messages-list-container">
            <!-- Liste des messages ici -->
            <?php foreach ($messages as $message): ?>
                <div class="message-item <?= $message->read ? '' : 'unread' ?>" 
                     data-id="<?= $message->id ?>" 
                     onclick="selectMessage(<?= $message->id ?>, <?= $message->sender_id ?>)"">
                    <div class="message-header">
                        <span class="donor-name"><?= htmlspecialchars($message->sender_name ?? 'Utilisateur inconnu') ?></span>
                        <span class="message-time"><?= date('d/m/y', strtotime($message->created_at)) /* TODO: Use better relative time */ ?></span>
                    </div>
                    <p class="message-snippet"><?= htmlspecialchars(substr($message->content, 0, 50)) . (strlen($message->content) > 50 ? '...' : '') ?></p>
                    <?php if (!empty($message->donation_amount)): ?>
                        <span class="donation-badge">Don: <?= number_format($message->donation_amount, 2, ',', ' ') ?> €</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
                <div class="empty-state-list">
                    <p>Aucun message pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="message-detail-container card" id="messageDetail">
            <!-- Le contenu détaillé du message s'affichera ici -->
            <div class="empty-state-detail">
                <p><i class="fas fa-arrow-left"></i> Sélectionnez un message pour le lire</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFilterMenu() {
    const menu = document.querySelector('.filter-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function filterMessages(query) {
    const items = document.querySelectorAll('.message-item');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(query.toLowerCase()) ? 'block' : 'none';
    });
}

function toggleFilter(type) {
    // Implémenter le filtrage des messages selon le type
    console.log('Toggle filter:', type);
}

function selectMessage(messageId) {
    // Marquer comme sélectionné dans la liste
    document.querySelectorAll('.message-item').forEach(item => {
        item.classList.remove('selected');
    });
    document.querySelector(`[data-id="${messageId}"]`).classList.add('selected');

    // Charger le contenu du message
    fetch(`/api/messages/${messageId}`)
        .then(response => response.json())
        .then(message => {
            const detail = document.getElementById('messageDetail');
            detail.innerHTML = `
                <div class="message-content">
                    <div class="message-donor">
                        <img src="${message.donor_avatar || '/assets/img/default-avatar.png'}" 
                             alt="${message.donor_name}">
                        <div class="donor-info">
                            <h2>${message.donor_name}</h2>
                            <p class="donor-meta">
                                ${formatDate(message.created_at)}
                                ${message.donation_amount ? `• Don de ${formatAmount(message.donation_amount)}` : ''}
                            </p>
                        </div>
                    </div>
                    <div class="message-body">
                        ${formatMessage(message.content)}
                    </div>
                    <div class="message-actions">
                        <button class="btn btn-primary" onclick="replyToMessage(${messageId})">
                            <i class="fas fa-reply"></i>
                            Répondre
                        </button>
                        <button class="btn btn-outline" onclick="archiveMessage(${messageId})">
                            <i class="fas fa-archive"></i>
                            Archiver
                        </button>
                    </div>
                </div>
            `;
            detail.classList.add('active');

            // Marquer comme lu
            if (!message.read) {
                markAsRead(messageId);
            }
        });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatAmount(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

function formatMessage(content) {
    return content.replace(/\n/g, '<br>');
}

function markAsRead(messageId) {
    fetch(`/api/messages/${messageId}/read`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.querySelector(`[data-id="${messageId}"]`).classList.remove('unread');
        }
    });
}

function replyToMessage(messageId) {
    // Implémenter la réponse au message
    console.log('Reply to message:', messageId);
}

function archiveMessage(messageId) {
    if (confirm('Voulez-vous vraiment archiver ce message ?')) {
        fetch(`/api/messages/${messageId}/archive`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            }
        });
    }
}

// Fermer le menu des filtres en cliquant ailleurs
document.addEventListener('click', (e) => {
    if (!e.target.closest('.filter-dropdown')) {
        document.querySelector('.filter-menu').style.display = 'none';
    }
});
</script>

<?php  ?>
