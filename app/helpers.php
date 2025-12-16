<?php

/**
 * Échappement des caractères spéciaux HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Génère un token CSRF
 */
function generateCsrfToken() {
    return \App\Core\App::make(\App\Core\Csrf::class)->generateToken();
}

/**
 * Vérifie un token CSRF
 */
function verifyCsrfToken($token) {
    return \App\Core\App::make(\App\Core\Csrf::class)->verifyToken($token);
}

/**
 * Formatage d'une date
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Formatage d'un nombre
 */
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals, ',', ' ');
}

/**
 * Affiche un temps "il y a X" à partir d'une date SQL
 */
function formatTimeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) {
        return 'À l’instant';
    } elseif ($diff < 3600) {
        return intval($diff / 60) . ' min';
    } elseif ($diff < 86400) {
        return intval($diff / 3600) . ' h';
    } elseif ($diff < 2592000) {
        return intval($diff / 86400) . ' j';
    } else {
        return date('d/m/Y', $timestamp);
    }
}

/**
 * Formatage d'un prix
 */
function formatPrice($price) {
    return formatNumber($price, 2) . ' €';
}

/**
 * Formatage d'un montant monétaire
 */
function formatAmount($amount) {
    if ($amount === null) return '0,00 €';
    return formatNumber($amount, 2) . ' €';
}

/**
 * Troncature d'une chaîne
 */
function truncate($string, $length = 100, $append = '...') {
    if (strlen($string) > $length) {
        $string = substr($string, 0, $length) . $append;
    }
    return $string;
}

/**
 * Génère une URL absolue
 */
if (!function_exists('url')) {
    function url($path = '') {
        $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $scheme = $isSecure ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';

        return rtrim($scheme . $host, '/') . '/' . ltrim($path, '/');
    }
}

/**
 * Génère une URL d'asset
 */
if (!function_exists('asset')) {
    function asset($path) {
        return url('assets/' . ltrim($path, '/'));
    }
}

/**
 * Redirection vers une URL
 */
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

/**
 * Retourne une réponse JSON
 */
function json($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Retourne une erreur 404
 */
function notFound() {
    header('HTTP/1.0 404 Not Found');
    $view = new \App\Core\View();
    $view->render('errors/404.html.twig');
    exit;
}

/**
 * Retourne une erreur 403
 */
function forbidden() {
    header('HTTP/1.0 403 Forbidden');
    $view = new \App\Core\View();
    $view->render('errors/403.html.twig');
    exit;
}

/**
 * Vérifie si la requête est en AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Vérifie si la requête est en POST
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Retourne une valeur de $_POST
 */
function post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * Retourne une valeur de $_GET
 */
function get($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * Retourne une valeur de $_FILES
 */
function files($key) {
    return $_FILES[$key] ?? null;
}

/**
 * Retourne une valeur de $_SESSION
 */
if (!function_exists('session')) {
    function session($key = null, $default = null) {
        $session = \App\Core\App::make(\App\Core\Session::class);
        if (is_null($key)) {
            return $session->all();
        }

        return $session->get($key, $default);
    }
}

/**
 * Définit une valeur dans $_SESSION
 */
function setSession($key, $value) {
    \App\Core\App::make(\App\Core\Session::class)->set($key, $value);
}

/**
 * Supprime une valeur de $_SESSION
 */
function unsetSession($key) {
    \App\Core\App::make(\App\Core\Session::class)->remove($key);
}

/**
 * Retourne un message flash
 */
if (!function_exists('flash')) {
    function flash($message = null, $type = 'info') {
        $session = \App\Core\App::make(\App\Core\Session::class);
        
        // Si pas de message, on retourne tous les messages
        if (is_null($message)) {
            return $session->get('flash', []);
        }

        // Sinon on ajoute un message
        $flashes = $session->get('flash');
        if (!is_array($flashes)) {
            $flashes = [];
        }

        $flashes[] = [
            'message' => $message,
            'type' => $type
        ];

        $session->set('flash', $flashes);
        return $flashes;
    }
}

/**
 * Définit un message flash
 */
function setFlash($key, $value) {
    setSession($key, $value);
}

/**
 * Vérifie si un fichier est une image
 */
function isImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    return in_array($file['type'], $allowedTypes);
}

/**
 * Génère un nom de fichier unique
 */
function uniqueFilename($filename) {
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    return uniqid() . '.' . $extension;
}

/**
 * Déplace un fichier uploadé
 */
function moveUploadedFile($file, $destination) {
    return move_uploaded_file($file['tmp_name'], $destination);
}

/**
 * Supprime un fichier
 */
function deleteFile($path) {
    if (file_exists($path)) {
        unlink($path);
    }
}

/**
 * Crée un dossier
 */
function createDir($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

/**
 * Vérifie si une chaîne est un email valide
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Vérifie si une chaîne est une URL valide
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Nettoie une chaîne
 */
function clean($string) {
    return trim(strip_tags($string));
}

/**
 * Génère un slug à partir d'une chaîne
 */
function slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Retourne l'URL courante
 */
function currentUrl() {
    return $_SERVER['REQUEST_URI'];
}

/**
 * Retourne le domaine courant
 */
function currentDomain() {
    return $_SERVER['HTTP_HOST'];
}

/**
 * Retourne le protocole courant
 */
function currentProtocol() {
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
}

/**
 * Retourne l'URL complète courante
 */
function currentFullUrl() {
    return currentProtocol() . '://' . currentDomain() . currentUrl();
}

/**
 * Retourne la méthode HTTP courante
 */
function currentMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * Retourne l'IP du client
 */
function clientIp() {
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Retourne le User Agent du client
 */
function userAgent() {
    return $_SERVER['HTTP_USER_AGENT'];
}

/**
 * Vérifie si la requête est sécurisée
 */
function isSecure() {
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
}

/**
 * Vérifie si la requête est locale
 */
function isLocal() {
    return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
}

/**
 * Retourne l'environnement courant
 */
function environment() {
    return getenv('APP_ENV') ?: 'production';
}

/**
 * Vérifie si l'environnement est en production
 */
function isProduction() {
    return environment() === 'production';
}

/**
 * Vérifie si l'environnement est en développement
 */
function isDevelopment() {
    return environment() === 'development';
}

/**
 * Vérifie si le mode debug est activé
 */
function isDebug() {
    return defined('APP_DEBUG') && APP_DEBUG === true;
}

/**
 * Retourne la version de l'application
 */
function appVersion() {
    return defined('APP_VERSION') ? APP_VERSION : '1.0.0';
}

/**
 * Retourne le nom de l'application
 */
function appName() {
    return defined('APP_NAME') ? APP_NAME : 'Application';
}

/**
 * Retourne l'URL de base de l'application
 */
function baseUrl() {
    return defined('APP_URL') ? APP_URL : '/';
}

/**
 * Retourne le chemin de base de l'application
 */
function basePath() {
    return defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
}

/**
 * Retourne le chemin des vues
 */
function viewPath() {
    return defined('VIEW_PATH') ? VIEW_PATH : APP_PATH . '/views';
}

/**
 * Retourne le chemin des assets
 */
function assetPath() {
    return defined('ASSET_PATH') ? ASSET_PATH : '/assets';
}

/**
 * Retourne le chemin des uploads
 */
function uploadPath() {
    return defined('UPLOAD_PATH') ? UPLOAD_PATH : ROOT_PATH . '/public/uploads';
}

/**
 * Retourne le chemin des logs
 */
function logPath() {
    return defined('LOG_PATH') ? LOG_PATH : ROOT_PATH . '/logs';
}

/**
 * Retourne le chemin du cache
 */
function cachePath() {
    return defined('CACHE_PATH') ? CACHE_PATH : ROOT_PATH . '/cache';
}

/**
 * Retourne le chemin des médias
 */
function mediaPath() {
    return defined('MEDIA_PATH') ? MEDIA_PATH : ROOT_PATH . '/public/media';
}

if (!function_exists('format_money')) {
    function format_money($amount, string $currency = '€', int $decimals = 2, string $decimalSeparator = ',', string $thousandSeparator = ' '): string {
        if (!is_numeric($amount)) {
            $amount = 0;
        }

        $formatted = number_format((float) $amount, $decimals, $decimalSeparator, $thousandSeparator);

        return trim($formatted . ' ' . $currency);
    }
}

if (!function_exists('flash_messages')) {
    function flash_messages(): array {
        $messages = [];
        $session = \App\Core\App::make(\App\Core\Session::class);

        if ($session->has('flash')) {
            $flash = $session->get('flash');

            if (isset($flash['message'])) {
                $messages[] = $flash;
            } elseif (is_array($flash)) {
                foreach ($flash as $item) {
                    if (is_array($item) && isset($item['message'])) {
                        $messages[] = $item;
                    }
                }
            }

            $session->remove('flash');
        }

        if ($session->has('flash_messages')) {
            $flashMessages = $session->get('flash_messages');
            foreach ($flashMessages as $type => $entries) {
                foreach ((array) $entries as $entry) {
                    if (is_array($entry) && isset($entry['message'])) {
                        $messages[] = [
                            'type' => $entry['type'] ?? $type,
                            'message' => $entry['message'],
                        ];
                    } else {
                        $messages[] = [
                            'type' => is_string($type) ? $type : 'info',
                            'message' => (string) $entry,
                        ];
                    }
                }
            }

            $session->remove('flash_messages');
        }

        return $messages;
    }
}

if (!function_exists('flash_class')) {
    function flash_class(string $type): string {
        return match (strtolower($type)) {
            'error', 'danger' => 'alert-danger',
            'warning' => 'alert-warning',
            'success' => 'alert-success',
            'info' => 'alert-info',
            default => 'alert-secondary',
        };
    }
}

if (!function_exists('paginate')) {
    function paginate(int $current, int $total, array $params = []): array {
        $current = max(1, $current);
        $total = max(1, $total);

        $cleanParams = [];
        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $cleanParams[$key] = $value;
        }

        $buildUrl = static function (int $page) use ($cleanParams): string {
            $query = http_build_query(array_merge($cleanParams, ['page' => $page]));

            return $query ? '?' . $query : '?page=' . $page;
        };

        $pages = [];
        for ($page = 1; $page <= $total; $page++) {
            $pages[] = [
                'number' => $page,
                'url' => $buildUrl($page),
                'is_current' => $page === $current,
            ];
        }

        return [
            'current' => $current,
            'total' => $total,
            'has_previous' => $current > 1,
            'has_next' => $current < $total,
            'previous_url' => $current > 1 ? $buildUrl($current - 1) : null,
            'next_url' => $current < $total ? $buildUrl($current + 1) : null,
            'pages' => $pages,
        ];
    }
}

if (!function_exists('is_active_path')) {
    function is_active_path(string $uri, ?string $currentUri = null, bool $exact = false): bool {
        $currentUri = $currentUri ?? ($_SERVER['REQUEST_URI'] ?? '/');

        if ($exact) {
            return $currentUri === $uri;
        }

        if ($uri === '/') {
            return $currentUri === '/';
        }

        if ($uri === '/dashboard') {
            return $currentUri === '/dashboard' || str_starts_with($currentUri, '/dashboard/stats');
        }

        return str_starts_with($currentUri, $uri);
    }
}
