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

// Configuration de la session (le démarrage est géré par la classe Session)
session_name('msss_session');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Désactivé pour le développement local
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// Initialisation de la base de données
try {
    $pdo = App\Core\Database::getInstance();
} catch (\Exception $e) {
    error_log('Erreur lors de l\'initialisation de la base de données : ' . $e->getMessage());
    header('Location: /error');
    exit;
}

// Initialisation du conteneur de dépendances
$container = require_once __DIR__ . '/container.php';

// Enregistrement du conteneur dans la classe App pour accès global
App\Core\App::setContainer($container);

// Initialisation de la session via le service
$session = $container->get(App\Core\Session::class);

if (!$session->has('initialized')) {
    error_log("Initialisation d'une nouvelle session");
    $session->regenerate();
    $session->set('initialized', true);
    $csrf = $container->get(App\Core\Csrf::class);
    $token = $csrf->generateToken();
    error_log("Nouveau CSRF Token : " . $token);
} else {
    error_log('=== Session active ===');
    error_log('Session ID : ' . session_id());
}

// Initialiser le middleware d'authentification (deprecated usage, prefer DI)
// $auth = new App\Core\Auth($session); // Usage direct si nécessaire, mais préférer l'injection

