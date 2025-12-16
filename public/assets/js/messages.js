
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
            detail.innerHTML = "`"
                + "<div class=\"message-content\">"
                    + "<div class=\"message-donor\">"
                        + "<img src=\"" + (message.donor_avatar || '/assets/img/default-avatar.png') + "" alt=\"" + message.donor_name + "">
                        + "<div class=\"donor-info\">
                            <h2>" + message.donor_name + "</h2>
                            <p class=\"donor-meta\">
                                " + formatDate(message.created_at) + "
                                " + (message.donation_amount ? `• Don de ${formatAmount(message.donation_amount)}` : '') + "
                            </p>
                        </div>
                    </div>
                    <div class=\"message-body\">
                        " + formatMessage(message.content) + "
                    </div>
                    <div class=\"message-actions\">
                        <button class=\"btn btn-primary\" onclick=\"replyToMessage(" + messageId + ")\">
                            <i class=\"fas fa-reply\"></i>
                            Répondre
                        </button>
                        <button class=\"btn btn-outline\" onclick=\"archiveMessage(" + messageId + ")\">
                            <i class=\"fas fa-archive\"></i>
                            Archiver
                        </button>
                    </div>
                </div>
            ";
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
    const filterDropdown = document.querySelector('.filter-dropdown');
    if (filterDropdown && !filterDropdown.contains(e.target)) {
        const menu = document.querySelector('.filter-menu');
        if(menu) {
            menu.style.display = 'none';
        }
    }
});
