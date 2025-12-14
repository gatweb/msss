<?php

namespace App\Core;

use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Core\Database;
use App\Repositories\CreatorRepository;

class BaseController {
    protected $db;
    protected $pdo;
    protected $view;
    protected $auth;
    protected $flash;
    protected $creator;
    protected $creatorRepository;

    public function __construct(
        ?Database $db = null,
        ?View $view = null,
        ?Auth $auth = null,
        ?Flash $flash = null,
        ?CreatorRepository $creatorRepository = null
    ) {
        // Initialiser les dépendances de base avec possibilité d'injection pour les tests/contrôleurs
        $this->db = $db ?: Database::getInstance();
        $this->pdo = $this->db;
        $this->view = $view ?: new View();
        $this->auth = $auth ?: new Auth();
        $this->flash = $flash ?: new Flash();

        // Initialiser le repository nécessaire AVANT de l'utiliser
        $this->creatorRepository = $creatorRepository ?: new CreatorRepository($this->db);

        // Charger le créateur si l'utilisateur est connecté et est un créateur
        /* Suppression de la logique de chargement du créateur ici.
         * Les contrôleurs enfants doivent charger $this->creator explicitement
         * dans leurs méthodes si nécessaire pour éviter les problèmes de timing
         * lors de l'instanciation via le Router.
        if ($this->auth->isLoggedIn() && isset($_SESSION['creator_id'])) {
            $creatorId = $_SESSION['creator_id'];
            error_log("BaseController Constructor: Tentative de chargement creator ID {$creatorId}.");
            // Vérifier si le repository est disponible
            if ($this->creatorRepository) {
                $this->creator = $this->creatorRepository->findById($creatorId);
                // Log pour vérifier si le créateur a été trouvé
                $isCreatorNull = is_null($this->creator) ? 'Oui' : 'Non';
                error_log("BaseController Constructor: Tentative de chargement creator ID {$creatorId}. Trouvé ? {$isCreatorNull}");
            } else {
                error_log("BaseController Constructor: ERREUR - creatorRepository n'est PAS disponible !");
                $this->creator = null; // Assurer que la propriété existe mais est null
            }
        } else {
            $loggedIn = $this->auth->isLoggedIn() ? 'Oui' : 'Non';
            $creatorIdSet = isset($_SESSION['creator_id']) ? ('Oui (' . $_SESSION['creator_id'] . ')') : 'Non'; // Correction parenthèse
            error_log("BaseController Constructor: Non loggué ou creator_id non défini. LoggedIn: {$loggedIn}, CreatorIdSet: {$creatorIdSet}");
            $this->creator = null; // Assurer que la propriété existe mais est null
        }
        */
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
            require APP_PATH . '/views/errors/500.php';
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
        return isset($_SESSION['creator_id']);
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     *
     * @return bool
     */
    protected function isAdmin() {
        return isset($_SESSION['creator_is_admin']) && $_SESSION['creator_is_admin'] === true;
    }

    /**
     * Retourne l'ID de l'utilisateur connecté
     *
     * @return int|null
     */
    protected function getCurrentUserId() {
        return $_SESSION['creator_id'] ?? null;
    }

    /**
     * Vérifie si le token CSRF est valide
     *
     * @param string $token Token CSRF à vérifier
     * @return bool
     */
    protected function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Génère un nouveau token CSRF
     *
     * @return string
     */
    protected function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
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
}
