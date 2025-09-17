<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-8 mx-auto text-center">
            <h1 class="display-4 mb-4">Bienvenue sur Msss</h1>
            <p class="lead">Découvrez nos créateurs et leurs packs exclusifs.</p>
            <?php if (!$this->auth->isLoggedIn()): ?>
                <div class="mt-4">
                    <a href="/register" class="btn btn-primary btn-lg me-3">Devenir créateur</a>
                    <a href="/login" class="btn btn-outline-primary btn-lg">Se connecter</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($creators)): ?>
        <section class="mb-5">
            <h2 class="text-center mb-4">Nos créateurs</h2>
            <div class="row g-4">
                <?php foreach ($creators as $creator): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <?php if ($creator['profile_pic_url']): ?>
                                <img src="<?= e($creator['profile_pic_url']) ?>" class="card-img-top" alt="<?= e($creator['name']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= e($creator['name']) ?></h5>
                                <?php if ($creator['tagline']): ?>
                                    <p class="card-text"><?= e($creator['tagline']) ?></p>
                                <?php endif; ?>
                                <a href="/creator/<?= e($creator['username']) ?>" class="btn btn-outline-primary">Voir le profil</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($popularPacks)): ?>
        <section>
            <h2 class="text-center mb-4">Packs populaires</h2>
            <div class="row g-4">
                <?php foreach ($popularPacks as $pack): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <?php if ($pack['image_url']): ?>
                                <img src="<?= e($pack['image_url']) ?>" class="card-img-top" alt="<?= e($pack['name']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= e($pack['name']) ?></h5>
                                <p class="card-text">
                                    <?= e($pack['description']) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0"><?= formatAmount($pack['price']) ?></span>
                                    <a href="/pack/<?= e($pack['id']) ?>" class="btn btn-primary">Voir le pack</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
