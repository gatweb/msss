<?php
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$currentPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';

$brandName = defined('APP_NAME') ? APP_NAME : 'Msss';
$headerClasses = $headerClasses ?? 'site-header';
if (!str_contains($headerClasses, 'site-header')) {
    $headerClasses = trim('site-header ' . $headerClasses);
}

$logoHref = $logoHref ?? '/';
$logoLabel = $logoLabel ?? $brandName;
$logoSubLabel = $logoSubLabel ?? null;
$logoIcon = $logoIcon ?? null;
$logoImage = array_key_exists('logoImage', get_defined_vars()) ? $logoImage : '/assets/img/logo.png';

if (!isset($navLinks)) {
    $navLinks = [
        [
            'label' => 'Accueil',
            'href' => '/',
            'match' => '/',
        ],
        [
            'label' => 'Découvrir',
            'href' => '/#creators',
            'match' => '/creator',
        ],
    ];
}

foreach ($navLinks as &$link) {
    $match = $link['match'] ?? $link['href'];
    $isExact = $link['exact'] ?? false;
    if ($isExact) {
        $link['isActive'] = $link['isActive'] ?? $currentPath === $match;
        continue;
    }

    $link['isActive'] = $link['isActive'] ?? (
        $match === '/' ? $currentPath === '/' : str_starts_with($currentPath, $match)
    );
}
unset($link);

$isCreator = session('creator_id');
$isUser = session('user_id');
$isLogged = $isCreator || $isUser;
$displayName = session('creator_name') ?? session('username') ?? null;

if (!isset($actionButtons)) {
    if ($isLogged) {
        $actionButtons = [
            [
                'label' => $displayName ?: 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'fas fa-chart-line',
                'variant' => 'ghost',
            ],
            [
                'label' => 'Déconnexion',
                'href' => '/logout',
                'icon' => 'fas fa-sign-out-alt',
                'variant' => 'primary',
            ],
        ];
    } else {
        $actionButtons = [
            [
                'label' => 'Connexion',
                'href' => '/login',
                'variant' => 'ghost',
            ],
            [
                'label' => 'Devenir créatrice',
                'href' => '/register',
                'variant' => 'primary',
            ],
        ];
    }
}
?>
<header class="<?= htmlspecialchars($headerClasses) ?>">
    <div class="site-header__inner">
        <a href="<?= htmlspecialchars($logoHref) ?>" class="site-logo">
            <?php if ($logoImage): ?>
                <img src="<?= htmlspecialchars($logoImage) ?>" alt="<?= htmlspecialchars($logoLabel) ?>">
            <?php endif; ?>
            <?php if ($logoIcon): ?>
                <i class="<?= htmlspecialchars($logoIcon) ?>" aria-hidden="true"></i>
            <?php endif; ?>
            <span>
                <?= htmlspecialchars($logoLabel) ?>
                <?php if ($logoSubLabel): ?>
                    <small><?= htmlspecialchars($logoSubLabel) ?></small>
                <?php endif; ?>
            </span>
        </a>

        <nav id="site-nav" class="site-nav">
            <ul class="site-nav__list">
                <?php foreach ($navLinks as $link): ?>
                    <li>
                        <a href="<?= htmlspecialchars($link['href']) ?>"
                           class="site-nav__link<?= !empty($link['isActive']) ? ' is-active' : '' ?>">
                            <?php if (!empty($link['icon'])): ?>
                                <i class="<?= htmlspecialchars($link['icon']) ?>" aria-hidden="true"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($link['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <?php if (!empty($actionButtons)): ?>
            <div class="site-actions">
                <?php foreach ($actionButtons as $button): ?>
                    <?php
                    $variant = $button['variant'] ?? 'ghost';
                    $class = $variant === 'primary' ? 'btn' : 'btn-ghost';
                    ?>
                    <a class="<?= $class ?>"
                       href="<?= htmlspecialchars($button['href']) ?>"
                       <?= !empty($button['target']) ? 'target="' . htmlspecialchars($button['target']) . '"' : '' ?>>
                        <?php if (!empty($button['icon'])): ?>
                            <i class="<?= htmlspecialchars($button['icon']) ?>" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?= htmlspecialchars($button['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</header>
