<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="media-gallery">
    <div class="gallery-header">
        <h1>Mes médias</h1>
        <div class="gallery-actions">
            <div class="filters">
                <a href="?type=image" class="filter-btn <?= $type === 'image' ? 'active' : '' ?>">
                    <i class="fas fa-image"></i> Images
                </a>
                <a href="?type=video" class="filter-btn <?= $type === 'video' ? 'active' : '' ?>">
                    <i class="fas fa-video"></i> Vidéos
                </a>
                <a href="?" class="filter-btn <?= !$type ? 'active' : '' ?>">
                    <i class="fas fa-border-all"></i> Tout
                </a>
            </div>
            
            <a href="/media/upload" class="btn-upload">
                <i class="fas fa-plus"></i> Ajouter un média
            </a>
        </div>
    </div>

    <?php if (empty($media)): ?>
        <div class="empty-state">
            <i class="fas fa-photo-video"></i>
            <h2>Aucun média</h2>
            <p>Commencez à ajouter des images ou des vidéos pour les partager avec vos supporters.</p>
            <a href="/media/upload" class="btn-upload">
                <i class="fas fa-plus"></i> Ajouter un média
            </a>
        </div>
    <?php else: ?>
        <div class="media-grid">
            <?php foreach ($media as $item): ?>
                <div class="media-card" data-id="<?= $item['id'] ?>">
                    <div class="media-preview">
                        <?php if ($item['type'] === 'image'): ?>
                            <img src="/uploads/<?= htmlspecialchars($item['filename']) ?>" 
                                 alt="<?= htmlspecialchars($item['title'] ?? $item['original_filename']) ?>">
                        <?php else: ?>
                            <div class="video-preview">
                                <?php if ($item['thumbnail']): ?>
                                    <img src="/uploads/<?= htmlspecialchars($item['thumbnail']) ?>" 
                                         alt="Aperçu vidéo">
                                <?php endif; ?>
                                <i class="fas fa-play"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="media-info">
                        <h3><?= htmlspecialchars($item['title'] ?? $item['original_filename']) ?></h3>
                        
                        <div class="media-meta">
                            <span class="size">
                                <?= $this->formatFileSize($item['size']) ?>
                            </span>
                            <span class="date">
                                <?= $this->formatDate($item['created_at']) ?>
                            </span>
                        </div>
                        
                        <div class="media-actions">
                            <button class="btn-edit" data-id="<?= $item['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-delete" data-id="<?= $item['id'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($currentPage > 1 || count($media) === 20): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?><?= $type ? '&type=' . $type : '' ?>" 
                       class="page-link">
                        <i class="fas fa-chevron-left"></i> Précédent
                    </a>
                <?php endif; ?>
                
                <?php if (count($media) === 20): ?>
                    <a href="?page=<?= $currentPage + 1 ?><?= $type ? '&type=' . $type : '' ?>" 
                       class="page-link">
                        Suivant <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Modal d'édition -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Modifier le média</h2>
            <button class="close-modal">&times;</button>
        </div>
        
        <form id="editForm" class="modal-body">
            <input type="hidden" name="media_id" id="mediaId">
            
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel">Annuler</button>
                <button type="submit" class="btn-save">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<style>
.media-gallery {
    padding: 2rem;
}

.gallery-header {
    margin-bottom: 2rem;
}

.gallery-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.filters {
    display: flex;
    gap: 1rem;
}

.filter-btn {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    color: #666;
    background: #f0f0f0;
}

.filter-btn.active {
    background: #4CAF50;
    color: white;
}

.btn-upload {
    padding: 0.5rem 1rem;
    background: #4CAF50;
    color: white;
    border-radius: 4px;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.empty-state i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.media-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.media-preview {
    position: relative;
    padding-top: 75%;
    background: #f0f0f0;
}

.media-preview img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-preview {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.video-preview i {
    font-size: 2rem;
    color: white;
    background: rgba(0,0,0,0.5);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.media-info {
    padding: 1rem;
}

.media-info h3 {
    margin: 0;
    font-size: 1rem;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.media-meta {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: #666;
}

.media-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.media-actions button {
    padding: 0.5rem;
    border: none;
    background: none;
    cursor: pointer;
    color: #666;
}

.media-actions button:hover {
    color: #333;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
}

.page-link {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #4CAF50;
    border: 1px solid #4CAF50;
    border-radius: 4px;
}

.page-link:hover {
    background: #4CAF50;
    color: white;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background: white;
    width: 90%;
    max-width: 500px;
    margin: 2rem auto;
    border-radius: 8px;
}

.modal-header {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

.modal-body {
    padding: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1rem;
}

.btn-cancel {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
}

.btn-save {
    padding: 0.5rem 1rem;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

@media (max-width: 768px) {
    .gallery-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .filters {
        width: 100%;
        justify-content: space-between;
    }
    
    .btn-upload {
        width: 100%;
        text-align: center;
    }
    
    .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const mediaCards = document.querySelectorAll('.media-card');
    
    // Ouvrir le modal d'édition
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const mediaId = this.dataset.id;
            // TODO: Charger les données du média
            document.getElementById('mediaId').value = mediaId;
            modal.style.display = 'block';
        });
    });
    
    // Fermer le modal
    document.querySelector('.close-modal').addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    document.querySelector('.btn-cancel').addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    // Clic en dehors du modal
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Soumission du formulaire d'édition
    editForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const mediaId = document.getElementById('mediaId').value;
        const formData = new FormData(this);
        
        try {
            const response = await fetch(`/media/update/${mediaId}`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                // Recharger la page pour voir les modifications
                window.location.reload();
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        }
    });
    
    // Suppression d'un média
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
                return;
            }
            
            const mediaId = this.dataset.id;
            
            try {
                const response = await fetch(`/media/delete/${mediaId}`, {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Supprimer la carte du média
                    this.closest('.media-card').remove();
                    
                    // Si plus de médias, afficher l'état vide
                    if (document.querySelectorAll('.media-card').length === 0) {
                        window.location.reload();
                    }
                } else {
                    alert(data.message || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            }
        });
    });
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
