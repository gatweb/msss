<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', $basePath);
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', $basePath);
}

if (!defined('APP_PATH')) {
    define('APP_PATH', $basePath . '/app');
}

if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', $basePath . '/app/storage');
}

if (!defined('LOG_PATH')) {
    define('LOG_PATH', STORAGE_PATH . '/logs');
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', true);
}

require BASE_PATH . '/vendor/autoload.php';
