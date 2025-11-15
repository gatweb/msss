<?php
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';

$links = [
    ['/dashboard', 'Tableau de bord', 'bi bi-grid-1x2-fill'],
    ['/dashboard/stats', 'Statistiques', 'bi bi-graph-up'],
    ['/dashboard/links', 'Mes liens', 'bi bi-link-45deg'],
    ['/dashboard/ai-tools', 'Outils IA', 'bi bi-stars'],
    ['/dashboard/settings', 'Paramètres', 'bi bi-gear-fill'],
];

$currentCreator = $creator ?? [];
$publicUsername = $currentCreator['username'] ?? ($_SESSION['creator_username'] ?? null);
$publicUrl = $publicUsername ? '/creator/' . rawurlencode($publicUsername) : '/';
?>
<nav class="creator-sidebar">
    <h5>Navigation</h5>
    <ul class="nav-list">
        <?php foreach ($links as [$href, $label, $icon]): ?>
            <?php $isActive = str_starts_with($currentUri, $href); ?>
            <li>
                <a class="nav-item<?= $isActive ? ' active' : '' ?>" href="<?= $href ?>">
                    <i class="<?= $icon ?>"></i>
                    <?= htmlspecialchars($label) ?>
                </a>
            </li>
        <?php endforeach; ?>
        <li>
            <a class="nav-item" href="<?= htmlspecialchars($publicUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                <i class="bi bi-eye-fill"></i>
                Voir page publique
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a class="nav-item" href="/logout">
            <i class="bi bi-box-arrow-right"></i>
            Déconnexion
        </a>
    </div>
</nav>
