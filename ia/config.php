<?php

use Dotenv\Dotenv;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$autoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

if (class_exists(Dotenv::class) && file_exists(BASE_PATH . '/.env')) {
    Dotenv::createImmutable(BASE_PATH)->safeLoad();
}

$env = static function (string $key, $default = null) {
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }

    $value = getenv($key);
    return $value !== false ? $value : $default;
};

$provider = $env('AI_API_PROVIDER', defined('AI_API_PROVIDER') ? AI_API_PROVIDER : 'mistral');
$apiKey = $env('AI_API_KEY', defined('AI_API_KEY') ? AI_API_KEY : '');
$endpoint = $env('AI_API_ENDPOINT', defined('AI_API_ENDPOINT') ? AI_API_ENDPOINT : 'https://api.mistral.ai/v1/chat/completions');
$model = $env('AI_MODEL', defined('AI_MODEL') ? AI_MODEL : 'mistral-small-latest');

if (!defined('AI_API_PROVIDER')) {
    define('AI_API_PROVIDER', $provider);
}
if (!defined('AI_API_KEY')) {
    define('AI_API_KEY', $apiKey);
}
if (!defined('AI_API_ENDPOINT')) {
    define('AI_API_ENDPOINT', $endpoint);
}
if (!defined('AI_MODEL')) {
    define('AI_MODEL', $model);
}

if (!defined('API_TIMEOUT_SECONDS')) {
    define('API_TIMEOUT_SECONDS', 10);
}

if (!defined('LOG_FILE')) {
    define('LOG_FILE', BASE_PATH . '/storage/logs/api_errors.log');
}

if (!function_exists('log_api_error')) {
    function log_api_error(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] - {$message}\n";
        $logDir = dirname(LOG_FILE);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        error_log($logMessage, 3, LOG_FILE);
    }
}

if (empty(AI_API_KEY)) {
    log_api_error('ERREUR CRITIQUE: Clé API (AI_API_KEY) non configurée.');
}
