<?php $hideSidebar = true; ?>
<div class="container mt-4">
    <header class="page-section-header mb-4">
        <div class="page-section-heading">
            <p class="page-section-label">Offres</p>
            <h2 class="page-section-title">Créer un nouveau pack</h2>
        </div>
    </header>
    <form action="/profile/packs/create" method="post" class="pack-form-container">
        <div class="mb-3">
            <label for="name" class="form-label">Nom du pack</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Prix mensuel (€)</label>
            <input type="number" class="form-control" id="price" name="price" min="1" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>
        <div class="mb-4">
            <label for="perks" class="form-label">Avantages (optionnel, un par ligne)</label>
            <textarea class="form-control" id="perks" name="perks" rows="3" placeholder="Ex : Accès à un chat privé\nBadge spécial...\n"></textarea>
        </div>
        <div class="d-flex justify-content-end">
            <a href="/profile/packs" class="btn btn-secondary me-2">Annuler</a>
            <button type="submit" class="btn btn-success">Créer</button>
        </div>
    </form>
</div>
<style>
.form-label { font-weight: 500; }
</style>
