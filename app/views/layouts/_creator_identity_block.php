<div class="identity-container">
  <div class="creator-info">
    <h2>
      <?= htmlspecialchars($creator['name'] ?? 'Mss Stéphanie') ?>
      <span class="page-title"><?= isset($pageTitle) ? ' - ' . htmlspecialchars($pageTitle) : '' ?></span>
    </h2>
    <?php /* Commenter le sous-menu pour éviter la duplication
    <nav class="simple-sub-menu">
      <?php $currentUri = $_SERVER['REQUEST_URI']; ?>
      <a href="/creator/public" class="<?= $currentUri == '/creator/public' ? 'active' : '' ?>">Voir page publique</a>
      <a href="/dashboard/stats" class="<?= strpos($currentUri, '/dashboard') === 0 ? 'active' : '' ?>">Stats</a>
      <a href="/dashboard/links" class="<?= strpos($currentUri, '/dashboard/links') === 0 ? 'active' : '' ?>">Mes liens</a>
      <a href="/profile/settings" class="<?= $currentUri == '/profile/settings' ? 'active' : '' ?>">Paramètres</a>
      <a href="/dashboard/ai-tools" class="<?= strpos($currentUri, '/dashboard/ai-tools') === 0 ? 'active' : '' ?>">Outils IA</a>
      <a href="/logout">Déconnexion</a>
    </nav>
    */ ?>

    <?php if (isset($dailyTip) && !empty($dailyTip)): ?>
      <div class="daily-ai-tip">
        <p><i class="fas fa-lightbulb"></i> <strong>Conseil du jour :</strong> <?= htmlspecialchars($dailyTip) ?></p>
      </div>
    <?php endif; ?>

  </div>
</div>
