<?php

// Configuration de l'application
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Msss');
}
if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}
if (!defined('APP_ENV')) {
    define('APP_ENV', 'development');
}
if (!defined('APP_URL')) {
    define('APP_URL', 'http://msss.local/');
}
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', true);
}

// Chemins de l'application
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/app');
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', ROOT_PATH . '/storage');
}
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', STORAGE_PATH . '/uploads');
}
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', STORAGE_PATH . '/cache');
}
if (!defined('LOG_PATH')) {
    define('LOG_PATH', STORAGE_PATH . '/logs');
}
if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', APP_PATH . '/views');
}

// Configuration des uploads
if (!defined('UPLOAD_MAX_SIZE')) {
    define('UPLOAD_MAX_SIZE', 5242880); // 5MB
}
if (!defined('ALLOWED_EXTENSIONS')) {
    define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
}

// Configuration de la base de données
if (!defined('DB_CONNECTION')) {
    define('DB_CONNECTION', 'sqlite');
}
if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', STORAGE_PATH . '/database.sqlite');
}

// Configuration des sessions
if (!defined('SESSION_LIFETIME')) {
    define('SESSION_LIFETIME', 120);
}
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', 'msss_session');
}
if (!defined('SESSION_PATH')) {
    define('SESSION_PATH', '/');
}
if (!defined('SESSION_DOMAIN')) {
    define('SESSION_DOMAIN', null);
}
if (!defined('SESSION_SECURE')) {
    define('SESSION_SECURE', false);
}
if (!defined('SESSION_HTTP_ONLY')) {
    define('SESSION_HTTP_ONLY', true);
}

// Configuration du hachage
if (!defined('PASSWORD_HASH_ALGO')) {
    define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);
}
if (!defined('PASSWORD_HASH_COST')) {
    define('PASSWORD_HASH_COST', 10);
}
// Configuration du hachage
if (!defined('PASSWORD_HASH_ALGO')) {
    define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);
}

if (!defined('PASSWORD_HASH_COST')) {
    define('PASSWORD_HASH_COST', 10);
}

// Configuration des tokens
define('TOKEN_LENGTH', 32);
define('TOKEN_EXPIRY', 60 * 60); // 1 heure

// Configuration des cookies
define('COOKIE_LIFETIME', 2592000); // 30 jours
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');
define('COOKIE_SECURE', false);
define('COOKIE_HTTPONLY', true);

// Configuration des vues
define('LAYOUT_PATH', APP_PATH . '/views/layouts');
define('PARTIAL_PATH', APP_PATH . '/views/partials');

// Configuration des logs
if (!defined('LOG_PATH')) {
    define('LOG_PATH', STORAGE_PATH . '/logs');
}
define('LOG_LEVEL', 'debug');

// Configuration du cache
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', STORAGE_PATH . '/cache');
}
define('CACHE_LIFETIME', 3600);

// Configuration des assets
define('ASSET_PATH', '/assets');
define('CSS_PATH', '/assets/css');
define('JS_PATH', '/assets/js');
define('IMG_PATH', '/assets/img');

// Configuration des dates
define('DATE_FORMAT', 'd/m/Y');
define('TIME_FORMAT', 'H:i');
define('DATETIME_FORMAT', 'd/m/Y H:i');
define('TIMEZONE', 'Europe/Paris');

// Configuration de la sécurité
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LENGTH', 32);
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_MAX_LENGTH', 72);

// Configuration des médias
define('MEDIA_MAX_SIZE', 10485760); // 10MB
define('MEDIA_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'video/mp4']);
define('MEDIA_PATH', ROOT_PATH . '/public/media');

// Configuration des notifications
define('NOTIFICATION_LIFETIME', 604800); // 7 jours

// --- Configuration IA (Mistral) ---
define('AI_API_KEY', $_ENV['AI_API_KEY'] ?? '');
define('AI_API_ENDPOINT', $_ENV['AI_API_ENDPOINT'] ?? 'https://api.mistral.ai/v1/chat/completions');
define('AI_MODEL', $_ENV['AI_MODEL'] ?? 'mistral-small-latest');
// Log values for debugging
error_log('DEBUG AI Config (using $_ENV): Key=' . (defined('AI_API_KEY') ? (empty(AI_API_KEY) ? '[EMPTY_FROM_ENV]' : AI_API_KEY) : '[NOT DEFINED]') . ', Endpoint=' . (defined('AI_API_ENDPOINT') ? (empty(AI_API_ENDPOINT) ? '[EMPTY_FROM_ENV]' : AI_API_ENDPOINT) : '[NOT DEFINED]') . ', Model=' . (defined('AI_MODEL') ? (empty(AI_MODEL) ? '[EMPTY_FROM_ENV]' : AI_MODEL) : '[NOT DEFINED]'));
// ---------------------------------

// Configuration des erreurs
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT_PATH . '/logs/error.log');
