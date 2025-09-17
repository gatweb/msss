<div class="card page-card links-page-card">
    <div class="row">
        <div class="col s12">
            <h4>Gestion de mes liens</h4>
        </div>
    </div>

    <div class="row">
        <div class="col s12 right-align">
            <a class="btn waves-effect waves-light blue modal-trigger" href="#linkModal" onclick="prepareAddLink()">
                <i class="fas fa-plus left"></i>Ajouter un lien
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <table class="striped highlight responsive-table links-table">
                <thead>
                    <tr>
                        <th>Icône</th>
                        <th>Titre</th>
                        <th>URL</th>
                        <th class="center-align">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($links)): ?>
                        <tr>
                            <td colspan="4" class="center-align">Vous n'avez ajouté aucun lien pour le moment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($links as $link): ?>
                            <tr>
                                <td><i class="<?= htmlspecialchars($link['icon'] ?? 'fas fa-link') ?> link-table-icon"></i></td>
                                <td><?= htmlspecialchars($link['title']) ?></td>
                                <td><a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($link['url']) ?></a></td>
                                <td class="center-align action-buttons">
                                    <a class="btn-icon modal-trigger" href="#linkModal" onclick="editLink(<?= $link['id'] ?>, '<?= htmlspecialchars(addslashes($link['title']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($link['url']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($link['icon'] ?? ''), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($link['description'] ?? ''), ENT_QUOTES) ?>')" title="Éditer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="btn-icon btn-danger" href="#" onclick="deleteLink(<?= $link['id'] ?>)" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal d'ajout/édition de lien -->
    <div id="linkModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Ajouter un lien</h2>
                <button class="close-modal" onclick="closeLinkModal()">&times;</button>
            </div>
            <form id="linkForm" action="/dashboard/links/save" method="POST" class="link-form">
                <input type="hidden" id="linkId" name="id">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                
                <div class="form-group">
                    <label for="title">Titre *</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="url">URL *</label>
                    <input type="url" id="url" name="url" required>
                </div>

                <div class="form-group">
                    <label for="icon">Icône</label>
                    <div class="icon-selector">
                        <select id="icon" name="icon">
                            <option value="fab fa-twitter">Twitter</option>
                            <option value="fab fa-instagram">Instagram</option>
                            <option value="fab fa-youtube">YouTube</option>
                            <option value="fab fa-twitch">Twitch</option>
                            <option value="fab fa-tiktok">TikTok</option>
                            <option value="fab fa-discord">Discord</option>
                            <option value="fas fa-globe">Site web</option>
                            <option value="fas fa-link">Autre</option>
                        </select>
                        <i id="iconPreview" class="fab fa-twitter"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                    <small>Optionnel - Une courte description du lien</small>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeLinkModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let currentLinkId = null;

    function prepareAddLink() {
        currentLinkId = null;
        document.getElementById('modalTitle').innerText = 'Ajouter un lien';
        document.getElementById('linkForm').reset(); 
        document.getElementById('linkId').value = ''; 
        const iconSelect = document.getElementById('icon');
        iconSelect.value = 'fas fa-link'; 
        updateIconPreview(); 
    }

    function editLink(id, title, url, icon, description) {
        currentLinkId = id;
        document.getElementById('modalTitle').innerText = 'Modifier le lien';
        document.getElementById('linkId').value = id;
        document.getElementById('title').value = title;
        document.getElementById('url').value = url;
        document.getElementById('icon').value = icon || 'fas fa-link';
        document.getElementById('description').value = description || '';
        updateIconPreview();
    }

    function closeLinkModal() {
        const modalElement = document.getElementById('linkModal');
        modalElement.style.display = 'none'; 
        prepareAddLink(); 
    }

    function deleteLink(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce lien ?')) {
            const form = document.createElement('form');
            form.method = 'POST'; 
            form.action = `/dashboard/links/delete/${id}`; 
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token'; 
            csrfInput.value = '<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>'; 
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    }

    document.getElementById('icon').addEventListener('change', updateIconPreview);

    function updateIconPreview() {
        const selectedIcon = document.getElementById('icon').value;
        document.getElementById('iconPreview').className = selectedIcon + ' fa-fw'; 
    }

    updateIconPreview();

    </script>
</div>
