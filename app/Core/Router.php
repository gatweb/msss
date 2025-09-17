<?php

namespace App\Core;

class Router {
    private $routes = [];
    private $notFoundCallback;
    private $errorCallback;

    public function __construct() {
        $this->notFoundCallback = function() {
            header('HTTP/1.0 404 Not Found');
            require APP_PATH . '/views/errors/404.php';
        };

        $this->errorCallback = function($e) {
            error_log('Erreur lors du routage : ' . $e->getMessage());
            header('HTTP/1.0 500 Internal Server Error');
            if (APP_DEBUG) {
                echo '<h1>Erreur 500</h1>';
                echo '<p>' . $e->getMessage() . '</p>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
            } else {
                require APP_PATH . '/views/errors/500.php';
            }
        };
    }

    public function addRoute($method, $path, $callback) {
        $path = '/' . trim($path, '/');
        $this->routes[$method][$path] = $callback;
    }

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    public function put($path, $callback) {
        $this->addRoute('PUT', $path, $callback);
    }

    public function delete($path, $callback) {
        $this->addRoute('DELETE', $path, $callback);
    }

    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }

    public function error($callback) {
        $this->errorCallback = $callback;
    }

    public function resolve() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = '/' . trim($path, '/');

            error_log("=== Router Resolution ===");
            error_log("Method: " . $method);
            error_log("Path: " . $path);

            // Vérifier si la route existe pour la méthode
            if (!isset($this->routes[$method])) {
                throw new \Exception('Méthode non supportée');
            }

            // Chercher une correspondance exacte
            if (isset($this->routes[$method][$path])) {
                return $this->handleRoute($this->routes[$method][$path]);
            }

            // Chercher une correspondance avec paramètres
            foreach ($this->routes[$method] as $route => $callback) {
                $pattern = preg_replace('/\/:([^\/]+)/', '/([^/]+)', $route);
                $pattern = '@^' . $pattern . '$@D';

                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches);
                    return $this->handleRoute($callback, $matches);
                }
            }

            // Aucune route trouvée
            call_user_func($this->notFoundCallback);

        } catch (\Exception $e) {
            call_user_func($this->errorCallback, $e);
        }
    }

    private function handleRoute($callback, $params = []) {
        try {
            if (is_array($callback)) {
                if (!class_exists($callback[0])) {
                    throw new \Exception("Contrôleur non trouvé : {$callback[0]}");
                }

                // Injection manuelle des dépendances pour chaque controller refactorisé
                switch ($callback[0]) {
                    case 'App\Controllers\DashboardController':
                        $db = \App\Core\Database::getInstance();
                        $aiToolsController = new \App\Controllers\AiToolsController();
                        $controller = new \App\Controllers\DashboardController(
                            $db,
                            new \App\Core\View(),
                            new \App\Core\Auth(),
                            new \App\Repositories\CreatorRepository($db),
                            new \App\Repositories\PackRepository($db),
                            new \App\Repositories\LinkRepository($db),
                            new \App\Repositories\DonationRepository($db),
                            $aiToolsController
                        );
                        break;
                    case 'App\Controllers\PackController':
                        $db = \App\Core\Database::getInstance();
                        $controller = new \App\Controllers\PackController(
                            new \App\Repositories\PackRepository($db),
                            new \App\Repositories\CreatorRepository($db),
                            new \App\Core\Auth(),
                            new \App\Core\Flash(),
                            new \App\Core\View()
                        );
                        break;
                    case 'App\Controllers\ProfileController':
                        $db = \App\Core\Database::getInstance();
                        $controller = new \App\Controllers\ProfileController(
                            new \App\Repositories\CreatorRepository($db),
                            new \App\Repositories\LinkRepository($db),
                            new \App\Models\Media(),
                            new \App\Core\Auth(),
                            new \App\Core\View()
                        );
                        break;
                    case 'App\Controllers\DonationsController':
                        $db = \App\Core\Database::getInstance();
                        $controller = new \App\Controllers\DonationsController(
                            new \App\Repositories\DonationRepository($db),
                            new \App\Repositories\CreatorRepository($db),
                            null
                        );
                        break;
                    case 'App\Controllers\LinksController':
                        $db = \App\Core\Database::getInstance();
                        $controller = new \App\Controllers\LinksController(
                            new \App\Repositories\LinkRepository($db),
                            new \App\Core\View()
                        );
                        break;
                    case 'App\Controllers\AiToolsController':
                        // Le constructeur de BaseController est appelé automatiquement
                        $controller = new \App\Controllers\AiToolsController();
                        break;
                    default:
                        $controller = new $callback[0]();
                        break;
                }
                if (!method_exists($controller, $callback[1])) {
                    throw new \Exception("Méthode non trouvée : {$callback[1]}");
                }

                return call_user_func_array([$controller, $callback[1]], $params);
            }

            return call_user_func_array($callback, $params);
        } catch (\Exception $e) {
            error_log("Erreur lors de l'exécution de la route : " . $e->getMessage());
            throw $e;
        }
    }
}
