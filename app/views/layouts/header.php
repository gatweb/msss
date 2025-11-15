<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Msss');
}

$GLOBALS['LEGACY_LAYOUT'] = true;

$pageTitle = $pageTitle ?? APP_NAME;
$extraStyles = [];

if (!empty($styles) && is_array($styles)) {
    $extraStyles = $styles;
}

$stylesheets = array_unique(array_merge([
    '/assets/css/common.css',
    '/assets/css/public.css',
    '/assets/css/auth.css',
], $extraStyles));

$extraScripts = [];
if (!empty($scripts) && is_array($scripts)) {
    $extraScripts = $scripts;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars(APP_NAME) ?></title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <?php foreach ($stylesheets as $stylesheet): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($stylesheet) ?>">
    <?php endforeach; ?>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-oZL4YZ6oJQWxqUoh8fDGe0XEt3G5UpiRaY1oCbcnZ6+QcmGeXgnz9K/Y/xFdVtOfvtTDHkJ/xZBwbNG0Ax7y4g=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    <?php foreach ($extraScripts as $script): ?>
        <script src="<?= htmlspecialchars($script) ?>" defer></script>
    <?php endforeach; ?>
</head>
<body class="site-shell legacy-page">
    <?php require APP_PATH . '/views/layouts/header_partial.php'; ?>

    <main class="site-main legacy-page__main">
        <div class="site-main__inner legacy-page__inner">
            <?php include APP_PATH . '/views/layouts/flash_messages.php'; ?>
