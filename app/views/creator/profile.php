<?php ob_start(); ?>
<div class="row">
    <div class="col s12 m12">
        <div class="card">
            <div class="card-image" style="background: #e3e3e3;">
                <img src="<?= htmlspecialchars($creator['banner_url'] ?? '/assets/img/default-banner.jpg') ?>" alt="Bannière" style="height:200px; object-fit:cover; width:100%;">
                <a class="btn-floating halfway-fab waves-effect waves-light blue modal-trigger" href="#bannerModal"><i class="fas fa-camera"></i></a>
            </div>
            <div class="card-content center-align">
                <img src="<?= htmlspecialchars($creator['profile_pic_url'] ?? '/assets/img/default-avatar.png') ?>" alt="Avatar" class="circle responsive-img z-depth-2" style="width:120px; margin-top:-70px; border:4px solid #fff;">
                <a class="btn-floating btn-small waves-effect waves-light blue modal-trigger" href="#avatarModal" style="position:relative; top:-45px; left:40px;"><i class="fas fa-camera"></i></a>
                <h5><?= htmlspecialchars($creator['name']) ?></h5>
                <span class="grey-text">@<?= htmlspecialchars($creator['tagline']) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col s12 m6">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Informations de base</span>
                <form id="profileForm">
                    <div class="input-field">
                        <input id="name" name="name" type="text" value="<?= htmlspecialchars($creator['name']) ?>">
                        <label for="name" class="active">Nom d'affichage</label>
                    </div>
                    <div class="input-field">
                        <input id="tagline" name="tagline" type="text" value="<?= htmlspecialchars($creator['tagline']) ?>">
                        <label for="tagline" class="active">Tagline</label>
                    </div>
                    <div class="input-field">
                        <textarea id="description" name="description" class="materialize-textarea" rows="4"><?= htmlspecialchars($creator['description']) ?></textarea>
                        <label for="description" class="active">Description</label>
                    </div>
                    <div class="input-field">
                        <input id="donation_goal" name="donation_goal" type="number" step="0.01" min="0" value="<?= htmlspecialchars($creator['donation_goal']) ?>">
                        <label for="donation_goal" class="active">Objectif de dons (€)</label>
                    </div>
                    <button type="submit" class="btn blue waves-effect waves-light">
                        <i class="fas fa-save left"></i>Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col s12 m6">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Mes Liens</span>
                <a class="btn-floating btn-small waves-effect waves-light green right modal-trigger" href="#addLinkModal"><i class="fas fa-plus"></i></a>
                <ul class="collection">
                    <?php if (!empty($links)): ?>
                        <?php foreach ($links as $link): ?>
                            <li class="collection-item avatar">
                                <i class="fas fa-link circle blue"></i>
                                <span class="title"><?= htmlspecialchars($link['title']) ?></span>
                                <p><a href="<?= htmlspecialchars($link['url']) ?>" target="_blank">Lien externe</a></p>
                                <a href="#editLinkModal" class="secondary-content modal-trigger"><i class="fas fa-edit"></i></a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="collection-item">Aucun lien enregistré.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Mes liens -->
<div class="card hoverable" style="margin-top:2rem;">
    <div class="card-content">
        <span class="card-title"><i class="fas fa-link left"></i>Mes liens</span>
        <?php if (!empty($links)): ?>
            <ul class="collection">
                <?php foreach ($links as $link): ?>
                    <li class="collection-item">
                        <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank">
                            <?= htmlspecialchars($link['title']) ?>
                        </a>
                        <a href="#editLinkModal" class="secondary-content modal-trigger" onclick="editLink(<?= $link['id'] ?>)"><i class="fas fa-edit"></i></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="grey-text">Aucun lien enregistré.</p>
        <?php endif; ?>
        <a href="#addLinkModal" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3 modal-trigger" style="margin-top:1rem;"><i class="material-icons left">add</i>Ajouter un lien</a>
    </div>
</div>

<!-- Modals pour ajout/édition de lien -->
<div id="addLinkModal" class="modal">
    <div class="modal-content">
        <h5>Ajouter un lien</h5>
        <form id="addLinkForm">
            <div class="input-field">
                <input id="title" name="title" type="text" required>
                <label for="title">Titre</label>
            </div>
            <div class="input-field">
                <input id="url" name="url" type="url" required>
                <label for="url">URL</label>
            </div>
            <button type="submit" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">Ajouter</button>
        </form>
    </div>
</div>
<div id="editLinkModal" class="modal">
    <div class="modal-content">
        <h5>Modifier le lien</h5>
        <form id="editLinkForm">
            <input type="hidden" name="id" id="edit-link-id">
            <div class="input-field">
                <input id="edit-title" name="title" type="text" required>
                <label for="edit-title">Titre</label>
            </div>
            <div class="input-field">
                <input id="edit-url" name="url" type="url" required>
                <label for="edit-url">URL</label>
            </div>
            <button type="submit" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">Enregistrer</button>
        </form>
    </div>
</div>

<!-- Modals -->
<!-- Modal Avatar -->
<div class="modal fade" id="avatarModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la photo de profil</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="avatarForm">
                    <div class="form-group">
                        <label for="profile_pic">Choisir une image</label>
                        <input type="file" id="profile_pic" name="profile_pic" 
                               class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bannière -->
<div class="modal fade" id="bannerModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la bannière</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="bannerForm">
                    <div class="form-group">
                        <label for="banner">Choisir une image</label>
                        <input type="file" id="banner" name="banner" 
                               class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Lien -->
<div class="modal fade" id="linkModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gérer un lien</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="linkForm">
                    <input type="hidden" id="link_id" name="link_id">
                    <div class="form-group">
                        <label for="link_title">Titre</label>
                        <input type="text" id="link_title" name="title" 
                               class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="link_url">URL</label>
                        <input type="url" id="link_url" name="url" 
                               class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Pack -->
<div class="modal fade" id="packModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gérer un pack</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="packForm">
                    <input type="hidden" id="pack_id" name="pack_id">
                    <div class="form-group">
                        <label for="pack_name">Nom</label>
                        <input type="text" id="pack_name" name="name" 
                               class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="pack_description">Description</label>
                        <textarea id="pack_description" name="description" 
                                class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="pack_price">Prix (€)</label>
                        <input type="number" id="pack_price" name="price" 
                               class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="pack_image">Image</label>
                        <input type="file" id="pack_image" name="image" 
                               class="form-control" accept="image/*">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" id="pack_active" name="is_active" 
                               class="form-check-input" checked>
                        <label class="form-check-label" for="pack_active">Pack actif</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/profile.js"></script>
<?php $content = ob_get_clean(); ?>
