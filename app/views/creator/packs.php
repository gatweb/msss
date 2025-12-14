<?php ob_start(); ?>
<!-- Début du contenu principal de la page Packs -->
<header class="page-section-header">
    <div class="page-section-heading">
        <p class="page-section-label">Offres</p>
        <h2 class="page-section-title">Mes packs de dons</h2>
    </div>
    <div class="page-section-actions">
        <a href="/profile/packs/create" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Ajouter un pack</span>
        </a>
    </div>
</header>

<div class="row">

    <?php if (empty($packs)): ?>
        <div class="alert alert-info">Aucun pack pour le moment.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($packs as $pack): ?>
                <div class="col s12 m6 l4">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title"><?= htmlspecialchars($pack['name']) ?></span>
                            <p>Prix : <?= number_format($pack['price'], 2) ?> €</p>
                            <p><?= nl2br(htmlspecialchars($pack['description'])) ?></p>
                            <p>Abonnés : <?= isset($pack['subscriber_count']) ? $pack['subscriber_count'] : 0 ?></p>
                            <p>Revenu mensuel : <?= isset($pack['monthly_revenue']) ? number_format($pack['monthly_revenue'], 2) : '0.00' ?> €</p>
                            <p>
                                <?php if ($pack['is_active']): ?>
                                    <span class="badge blue">Actif</span>
                                <?php else: ?>
                                    <span class="badge red">Inactif</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="card-action">
                            <a href="/profile/packs/edit/<?= $pack['id'] ?>" class="btn btn-sm btn-primary me-1"><i class="fas fa-edit left"></i>&nbsp;&nbsp;Éditer</a>
                            <a href="/profile/packs/delete/<?= $pack['id'] ?>" class="btn red me-1" onclick="return confirm('Supprimer ce pack ?');"><i class="fas fa-trash left"></i>&nbsp;&nbsp;Supprimer</a>
                            <a href="/profile/packs/toggle/<?= $pack['id'] ?>" class="btn btn-sm btn-warning">
                                <?php if ($pack['is_active']): ?>
                                    <i class="fas fa-pause left"></i> Pause
                                <?php else: ?>
                                    <i class="fas fa-play left"></i> Activer
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal pour créer/éditer un pack -->
    <div id="packModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nouveau pack</h2>
                <button type="button" class="modal-close" onclick="closePackModal()">&times;</button>
            </div>
            <form id="packForm" onsubmit="savePack(event)">
                <div class="card-action">
                    <input type="hidden" id="packId" name="id">
                    <div class="form-group">
                        <label for="packName">Nom du pack</label>
                        <input type="text" id="packName" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="packPrice">Prix mensuel (€)</label>
                        <input type="number" id="packPrice" name="price" class="form-control" step="0.01" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="packDescription">Description</label>
                        <textarea id="packDescription" name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Avantages</label>
                        <div id="perksContainer"></div>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="addPerk()">
                            <i class="fas fa-plus left"></i> Ajouter un avantage
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closePackModal()">Annuler</button>
                    <button type="submit" class="btn waves-effect waves-light blue">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let currentPackId = null;

    function addPerk(value = '') {
        const container = document.getElementById('perksContainer');
        const div = document.createElement('div');
        div.className = 'perk-input';
        div.innerHTML = `
            <input type="text" name="perks[]" value="${value}" placeholder="Décrivez un avantage">
            <button type="button" class="btn btn-icon" onclick="removePerk(this)">
                <i class="fas fa-minus"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function removePerk(button) {
        button.closest('.perk-input').remove();
    }

    function savePack(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch('/api/packs' + (currentPackId ? `/${currentPackId}` : ''), {
            method: currentPackId ? 'PUT' : 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert('Erreur lors de la sauvegarde du pack');
            }
        });
    }

    function togglePackStatus(packId) {
        fetch(`/api/packs/${packId}/toggle`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            }
        });
    }

    function openPackModal(pack = null) {
        currentPackId = pack ? pack.id : null;
        const modal = document.getElementById('packModal');
        const form = document.getElementById('packForm');
        const title = document.getElementById('modalTitle');

        // Reset form
        form.reset();
        document.getElementById('perksContainer').innerHTML = '';

        if (pack) {
            title.textContent = 'Éditer le pack';
            document.getElementById('packId').value = pack.id;
            document.getElementById('packName').value = pack.name;
            document.getElementById('packPrice').value = pack.price;
            document.getElementById('packDescription').value = pack.description;
            if (pack.perks) {
                pack.perks.forEach(perk => addPerk(perk));
            }
        } else {
            title.textContent = 'Nouveau pack';
            addPerk(); // Add one empty perk by default
        }

        modal.style.display = 'block';
    }

    function closePackModal() {
        const modal = document.getElementById('packModal');
        modal.style.display = 'none';
    }
    </script>

