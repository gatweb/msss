<?php

// Définir BASE_PATH si ce n'est pas déjà fait (au cas où bootstrap serait appelé directement)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}

// Chargement de l'autoloader Composer
require_once BASE_PATH . '/vendor/autoload.php';

// Charger les variables d'environnement depuis .env
// Assurez-vous que le fichier .env est à la racine du projet (BASE_PATH)
try {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
    error_log('.env chargé avec succès.');
} catch (\Dotenv\Exception\InvalidPathException $e) {
    error_log('Erreur: Le fichier .env n\'a pas été trouvé dans ' . BASE_PATH . '. Les configurations par défaut seront utilisées.');
    // Continuer sans .env peut être acceptable si des valeurs par défaut existent ou si les variables sont définies ailleurs (serveur web, etc.)
} catch (\Exception $e) {
    error_log('Erreur lors du chargement du fichier .env : ' . $e->getMessage());
    // Gérer d'autres erreurs potentielles lors du chargement
}

// Chargement des configurations
require_once __DIR__ . '/config/app.php';

// Chargement du BaseController
require_once APP_PATH . '/Core/BaseController.php';

// Chargement des fonctions utilitaires
require_once __DIR__ . '/helpers.php';

// Chargement de l'autoloader
spl_autoload_register(function ($class) {
    // Conversion du namespace en chemin de fichier
    $prefix = 'App\\';
    $base_dir = dirname(__DIR__) . '/app/';

    // Vérifie si la classe utilise le namespace App\\
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Récupère le chemin relatif de la classe
    $relative_class = substr($class, $len);

    // Remplace les \\ par / et ajoute .php
    $file = str_replace('\\', '/', $relative_class) . '.php';

    // Essayer de trouver le fichier dans les sous-dossiers
    $directories = ['Core', 'Controllers', 'Models'];
    foreach ($directories as $dir) {
        $try_file = $base_dir . $dir . '/' . basename($file);
        if (file_exists($try_file)) {
            require_once $try_file;
            error_log('Classe chargée avec succès depuis ' . $dir . ' : ' . $try_file);
            return;
        }
    }

    // Si le fichier n'est pas trouvé dans les sous-dossiers, essayer le chemin complet
    $full_path = $base_dir . $file;
    if (file_exists($full_path)) {
        require_once $full_path;
        error_log('Classe chargée avec succès : ' . $full_path);
        return;
    }

    error_log('Fichier non trouvé : ' . $class);
    error_log('Chemins essayés : ' . implode(', ', array_map(function($dir) use ($base_dir, $file) {
        return $base_dir . $dir . '/' . basename($file);
    }, $directories)));
});

// Configuration des erreurs
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');

// Créer le dossier logs s'il n'existe pas
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0777, true);
}

// S'assurer que le fichier error.log existe et est accessible en écriture
$errorLogFile = LOG_PATH . '/error.log';
if (!file_exists($errorLogFile)) {
    touch($errorLogFile);
    chmod($errorLogFile, 0666);
}

ini_set('error_log', $errorLogFile);

// Configuration et démarrage de la session
session_name('msss_session');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Désactivé pour le développement local
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_log('=== Session startée ===');
    error_log('Session ID : ' . session_id());
    error_log('Session data : ' . print_r($_SESSION, true));
    
    if (!isset($_SESSION['initialized'])) {
        error_log("Initialisation d'une nouvelle session");
        session_regenerate_id(true);
        $_SESSION['initialized'] = true;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        error_log("Nouveau CSRF Token : " . $_SESSION['csrf_token']);
    }
}

// Initialisation de la base de données
try {
    $pdo = App\Core\Database::getInstance();
} catch (\Exception $e) {
    error_log('Erreur lors de l\'initialisation de la base de données : ' . $e->getMessage());
    header('Location: /error');
    exit;
}

// Initialiser le middleware d'authentification
$auth = new App\Core\Auth();

// Définir les routes protégées qui nécessitent une authentification
$protectedRoutes = [
    '/profile',
    '/media',
    '/settings',
    '/links'
];

// Vérifier si la route actuelle nécessite une authentification
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

foreach ($protectedRoutes as $route) {
    if (strpos($currentPath, $route) === 0) {
        if (!$auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        break;
    }
}
