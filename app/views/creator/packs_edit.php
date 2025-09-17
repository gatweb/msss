<?php $hideSidebar = true; ?>
<div class="container mt-4">
    <h1 class="mb-4">Modifier le pack</h1>
    <form action="/profile/packs/edit/<?= $pack['id'] ?>" method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:600px;margin:auto;">
        <div class="mb-3">
            <label for="name" class="form-label">Nom du pack</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($pack['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Prix mensuel (€)</label>
            <input type="number" class="form-control" id="price" name="price" min="1" step="0.01" value="<?= htmlspecialchars($pack['price']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($pack['description']) ?></textarea>
        </div>
        <div class="mb-4">
            <label for="perks" class="form-label">Avantages (optionnel, un par ligne)</label>
            <textarea class="form-control" id="perks" name="perks" rows="3"><?php
                if (!empty($pack['perks']) && is_array($pack['perks'])) {
                    echo htmlspecialchars(implode("\n", $pack['perks']));
                }
            ?></textarea>
        </div>
        <div class="d-flex justify-content-end">
            <a href="/profile/packs" class="btn btn-secondary me-2">Annuler</a>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
<style>
.form-label { font-weight: 500; }
</style>

