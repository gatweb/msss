document.addEventListener('DOMContentLoaded', () => {
    // Formulaire de profil
    const profileForm = document.getElementById('profileForm');
    profileForm?.addEventListener('submit', handleProfileUpdate);

    // Formulaires d'upload
    const avatarForm = document.getElementById('avatarForm');
    const bannerForm = document.getElementById('bannerForm');
    avatarForm?.addEventListener('submit', handleAvatarUpload);
    bannerForm?.addEventListener('submit', handleBannerUpload);

    // Gestion des liens
    setupLinkHandlers();

    // Gestion des packs
    setupPackHandlers();
});

// Mise à jour du profil
async function handleProfileUpdate(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(e.target);
        const response = await fetch('/dashboard/profile/update', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Profil mis à jour avec succès', 'success');
        } else {
            showNotification('Erreur lors de la mise à jour du profil', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

// Upload d'avatar
async function handleAvatarUpload(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(e.target);
        const response = await fetch('/dashboard/profile/update-avatar', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.querySelector('.profile-avatar img').src = result.url;
            closeModal('avatarModal');
            showNotification('Photo de profil mise à jour', 'success');
        } else {
            showNotification('Erreur lors de l\'upload', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

// Upload de bannière
async function handleBannerUpload(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(e.target);
        const response = await fetch('/dashboard/profile/update-banner', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.querySelector('.profile-banner').style.backgroundImage = `url('${result.url}')`;
            closeModal('bannerModal');
            showNotification('Bannière mise à jour', 'success');
        } else {
            showNotification('Erreur lors de l\'upload', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

// Gestion des liens
function setupLinkHandlers() {
    // Ajouter un lien
    const addLinkBtn = document.querySelector('[data-target="#addLinkModal"]');
    addLinkBtn?.addEventListener('click', () => {
        document.getElementById('link_id').value = '';
        document.getElementById('linkForm').reset();
        openModal('linkModal');
    });

    // Modifier un lien
    document.querySelectorAll('.edit-link').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const linkId = e.target.closest('[data-id]').dataset.id;
            const response = await fetch(`/dashboard/links/${linkId}`);
            const link = await response.json();
            
            document.getElementById('link_id').value = link.id;
            document.getElementById('link_title').value = link.title;
            document.getElementById('link_url').value = link.url;
            
            openModal('linkModal');
        });
    });

    // Supprimer un lien
    document.querySelectorAll('.delete-link').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce lien ?')) return;
            
            const linkId = e.target.closest('[data-id]').dataset.id;
            try {
                const response = await fetch(`/dashboard/links/${linkId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    e.target.closest('.link-card').remove();
                    showNotification('Lien supprimé', 'success');
                } else {
                    showNotification('Erreur lors de la suppression', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'error');
            }
        });
    });

    // Soumettre le formulaire de lien
    const linkForm = document.getElementById('linkForm');
    linkForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const linkId = formData.get('link_id');
        const method = linkId ? 'PUT' : 'POST';
        const url = linkId ? `/dashboard/links/${linkId}` : '/dashboard/links';
        
        try {
            const response = await fetch(url, {
                method,
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                location.reload(); // Recharger pour voir les changements
            } else {
                showNotification('Erreur lors de la sauvegarde', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showNotification('Une erreur est survenue', 'error');
        }
    });
}

// Gestion des packs
function setupPackHandlers() {
    // Ajouter un pack
    const addPackBtn = document.querySelector('[data-target="#addPackModal"]');
    addPackBtn?.addEventListener('click', () => {
        document.getElementById('pack_id').value = '';
        document.getElementById('packForm').reset();
        openModal('packModal');
    });

    // Modifier un pack
    document.querySelectorAll('.edit-pack').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const packId = e.target.closest('[data-id]').dataset.id;
            const response = await fetch(`/dashboard/packs/${packId}`);
            const pack = await response.json();
            
            document.getElementById('pack_id').value = pack.id;
            document.getElementById('pack_name').value = pack.name;
            document.getElementById('pack_description').value = pack.description;
            document.getElementById('pack_price').value = pack.price;
            document.getElementById('pack_active').checked = pack.is_active;
            
            openModal('packModal');
        });
    });

    // Supprimer un pack
    document.querySelectorAll('.delete-pack').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce pack ?')) return;
            
            const packId = e.target.closest('[data-id]').dataset.id;
            try {
                const response = await fetch(`/dashboard/packs/${packId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    e.target.closest('.pack-card').remove();
                    showNotification('Pack supprimé', 'success');
                } else {
                    showNotification('Erreur lors de la suppression', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'error');
            }
        });
    });

    // Toggle status d'un pack
    document.querySelectorAll('.pack-status-toggle').forEach(toggle => {
        toggle.addEventListener('change', async (e) => {
            const packId = e.target.dataset.id;
            const isActive = e.target.checked;
            
            try {
                const response = await fetch(`/dashboard/packs/${packId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ is_active: isActive })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const statusText = e.target.closest('.pack-status')
                        .querySelector('.status-text');
                    statusText.textContent = isActive ? 'Actif' : 'Inactif';
                    showNotification('Statut mis à jour', 'success');
                } else {
                    e.target.checked = !isActive; // Remettre dans l'état précédent
                    showNotification('Erreur lors de la mise à jour', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                e.target.checked = !isActive; // Remettre dans l'état précédent
                showNotification('Une erreur est survenue', 'error');
            }
        });
    });

    // Soumettre le formulaire de pack
    const packForm = document.getElementById('packForm');
    packForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const packId = formData.get('pack_id');
        const method = packId ? 'PUT' : 'POST';
        const url = packId ? `/dashboard/packs/${packId}` : '/dashboard/packs';
        
        try {
            const response = await fetch(url, {
                method,
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                location.reload(); // Recharger pour voir les changements
            } else {
                showNotification('Erreur lors de la sauvegarde', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showNotification('Une erreur est survenue', 'error');
        }
    });
}

// Utilitaires
function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('show');
    modal.style.display = 'block';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('show');
    modal.style.display = 'none';
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
