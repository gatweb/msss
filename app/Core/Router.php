<?php

namespace App\Core;

use League\Container\Container;

class Router {
    private $routes = [];
    private $notFoundCallback;
    private $errorCallback;
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->notFoundCallback = function() {
            header('HTTP/1.0 404 Not Found');
            $view = $this->container->get(View::class);
            $view->render('errors/404.html.twig');
        };

        $this->errorCallback = function($e) {
            error_log('Erreur lors du routage : ' . $e->getMessage());
            header('HTTP/1.0 500 Internal Server Error');
            if (defined('APP_DEBUG') && APP_DEBUG) {
                echo '<h1>Erreur 500</h1>';
                echo '<p>' . $e->getMessage() . '</p>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
            } else {
                $view = $this->container->get(View::class);
                $view->render('errors/500.html.twig');
            }
        };
    }

    public function addRoute($method, $path, $callback, $middlewares = []) {
        $path = '/' . trim($path, '/');
        $this->routes[$method][$path] = ['callback' => $callback, 'middlewares' => $middlewares];
    }

    public function get($path, $callback, $middlewares = []) {
        $this->addRoute('GET', $path, $callback, $middlewares);
    }

    public function post($path, $callback, $middlewares = []) {
        $this->addRoute('POST', $path, $callback, $middlewares);
    }

    public function put($path, $callback, $middlewares = []) {
        $this->addRoute('PUT', $path, $callback, $middlewares);
    }

    public function delete($path, $callback, $middlewares = []) {
        $this->addRoute('DELETE', $path, $callback, $middlewares);
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

            if (!isset($this->routes[$method])) {
                throw new \Exception('Méthode non supportée');
            }

            if (isset($this->routes[$method][$path])) {
                $route = $this->routes[$method][$path];
                $this->executeMiddlewares($route['middlewares']);
                return $this->handleRoute($route['callback']);
            }

            foreach ($this->routes[$method] as $routePath => $route) {
                $pattern = preg_replace('/\/:([^\/]+)/', '/([^/]+)', $routePath);
                $pattern = '@^' . $pattern . '$@D';

                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches);
                    $this->executeMiddlewares($route['middlewares']);
                    return $this->handleRoute($route['callback'], $matches);
                }
            }

            call_user_func($this->notFoundCallback);

        } catch (\Exception $e) {
            call_user_func($this->errorCallback, $e);
        }
    }

    private function executeMiddlewares(array $middlewares)
    {
        foreach ($middlewares as $middlewareClass) {
            $middleware = $this->container->get($middlewareClass);
            $middleware->handle();
        }
    }

    private function handleRoute($callback, $params = []) {
        try {
            if (is_array($callback)) {
                $controller = $this->container->get($callback[0]);
                
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
