<?php

namespace App\Core;

use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Core\Database;
use App\Repositories\CreatorRepository;

class BaseController {
    protected $view;
    protected $auth;
    protected $flash;
    public $creatorRepository; // Temporarily public for child controllers
    protected $creator;

    public function __construct(View $view, Auth $auth, Flash $flash, CreatorRepository $creatorRepository) {
        $this->view = $view;
        $this->auth = $auth;
        $this->flash = $flash;
        $this->creatorRepository = $creatorRepository;
        $this->creator = null; // Initialiser à null par défaut
    }

    /**
     * Rend une vue avec le layout spécifié
     *
     * @param string $view Chemin de la vue à rendre
     * @param array $data Données à passer à la vue
     * @param string $layout Layout à utiliser (default par défaut)
     * @return void
     */
    protected function render($view, $data = [], $layout = 'default') {
        try {
            if (!isset($data['flash'])) {
                $data['flash'] = $this->flash;
            }

            if ($this->view->getTitle() && empty($data['pageTitle'])) {
                $data['pageTitle'] = $this->view->getTitle();
            }

            $scripts = $this->view->getScripts();
            if (!empty($scripts)) {
                $data['scripts'] = $scripts;
            }

            if (!isset($data['dailyTip']) && property_exists($this, 'dailyTip') && $this->dailyTip !== null) {
                $data['dailyTip'] = $this->dailyTip;
            }

            if (!isset($data['creator']) && property_exists($this, 'creator') && $this->creator !== null) {
                $data['creator'] = $this->creator;
            }

            $this->view->render($view, $data, $layout);
        } catch (\Throwable $e) {
            error_log('Erreur lors du rendu de la vue : ' . $e->getMessage());
            if (defined('APP_DEBUG') && APP_DEBUG) {
                throw $e;
            }

            http_response_code(500);
            try {
                $this->view->setTitle('Erreur serveur');
                $this->view->render('errors/500.html.twig', [], 'default');
            } catch (\Throwable $inner) {
                error_log('Erreur lors du rendu du template 500 : ' . $inner->getMessage());
                echo '<h1>Erreur interne</h1><p>Veuillez réessayer ultérieurement.</p>';
            }
        }
    }

    /**
     * Redirige vers une URL donnée
     *
     * @param string $url URL de redirection
     * @return void
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     *
     * @return bool
     */
    protected function isAuthenticated() {
        return $this->auth->isLoggedIn();
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     *
     * @return bool
     */
    protected function isAdmin() {
        return $this->auth->isAdmin();
    }

    /**
     * Retourne l'ID de l'utilisateur connecté
     *
     * @return int|null
     */
    protected function getCurrentUserId() {
        return $this->auth->getCurrentUserId();
    }

    /**
     * Vérifie si le token CSRF est valide
     *
     * @param string $token Token CSRF à vérifier
     * @return bool
     */
    protected function verifyCsrfToken($token) {
        return \App\Core\App::make(\App\Core\Csrf::class)->verifyToken($token);
    }

    /**
     * Génère un nouveau token CSRF
     *
     * @return string
     */
    protected function generateCsrfToken() {
        return \App\Core\App::make(\App\Core\Csrf::class)->generateToken();
    }

    /**
     * Retourne une réponse JSON
     *
     * @param mixed $data Données à retourner
     * @param int $status Code HTTP
     * @return void
     */
    protected function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Retourne une réponse d'erreur JSON
     *
     * @param string $message Message d'erreur
     * @param int $status Code HTTP
     * @return void
     */
    protected function jsonError($message, $status = 400) {
        $this->jsonResponse(['error' => $message], $status);
    }

    protected function requireLogin() {
        if (!$this->auth->isLoggedIn()) {
            $this->flash->error("Vous devez être connecté pour accéder à cette page.");
            $this->redirect('/login');
            exit;
        }
    }
}
