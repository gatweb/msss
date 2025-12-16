<?php

// Forcer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration du gestionnaire d'erreurs
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Erreur PHP [$errno] $errstr dans $errfile ligne $errline");
    return false;
});

// Configuration du gestionnaire d'exceptions
set_exception_handler(function($e) {
    error_log("Exception non gérée : " . $e->getMessage());
    error_log("Stack trace : " . $e->getTraceAsString());
    http_response_code(500);
    echo "Une erreur est survenue. Vérifiez les logs pour plus de détails.";
});

try {
    // Définir le chemin racine de l'application
    define('BASE_PATH', dirname(__DIR__));
    define('APP_PATH', BASE_PATH . '/app');

    // Charger l'autoloader de Composer
    require_once BASE_PATH . '/vendor/autoload.php';

    // Charger le bootstrap de l'application
    require_once APP_PATH . '/bootstrap.php';

    // Créer l'instance du routeur
    $router = new \App\Core\Router($container);

    // Charger les routes
    require_once __DIR__ . '/../app/routes.php';

    // Résoudre la route
    error_log("=== Début du traitement de la requête ===");
    error_log("Méthode : " . $_SERVER['REQUEST_METHOD']);
    error_log("URI : " . $_SERVER['REQUEST_URI']);
    error_log("POST data : " . print_r($_POST, true));
    
    $router->resolve();
    
    error_log("=== Fin du traitement de la requête ===");
} catch (Exception $e) {
    error_log("Erreur critique : " . $e->getMessage());
    error_log("Stack trace : " . $e->getTraceAsString());
    http_response_code(500);
    echo "Une erreur critique est survenue. Vérifiez les logs pour plus de détails.";
}
