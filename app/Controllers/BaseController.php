<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;

class BaseController {
    protected $db;
    protected $view;
    protected $auth;
    protected $flash;
    protected $user;
    protected $creator = null;
    protected $dailyTip = null;
    protected $creatorRepository;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->view = new View();
        $this->auth = new Auth();
        $this->flash = new Flash();
        $this->creatorRepository = new CreatorRepository($this->db);

        error_log("DEBUG BaseController::__construct - Initialisation.");

        if ($this->auth->isLoggedIn()) {
            $userId = $_SESSION['user_id'];
            error_log("DEBUG BaseController::__construct - Utilisateur ID {$userId} connecté.");
            $this->user = [
                'id' => $userId,
                'username' => $_SESSION['username'] ?? '',
                'is_admin' => $_SESSION['is_admin'] ?? false
            ];

            if (isset($_SESSION['creator_id']) && $_SESSION['creator_id'] === $userId) {
                $creatorId = $_SESSION['creator_id'];
                error_log("DEBUG BaseController::__construct - Tentative de chargement du créateur ID: " . $creatorId);
                $this->creator = $this->creatorRepository->findById($creatorId);
                if ($this->creator) {
                    error_log("DEBUG BaseController::__construct - Créateur chargé: " . print_r($this->creator, true));
                    try {
                        // $this->dailyTip = $this->dailyTipService->getTip(); // Commented out - Service unavailable
                        // error_log("DEBUG BaseController::__construct - Conseil du jour chargé: " . ($this->dailyTip ?: 'Aucun conseil trouvé'));
                        $this->dailyTip = null; // Set to null as service is unavailable
                        error_log("DEBUG BaseController::__construct - DailyTip défini à null car DailyTipService n'est pas disponible.");
                    } catch (\Exception $e) {
                        error_log("ERREUR BaseController::__construct - Échec de récupération du conseil du jour: " . $e->getMessage());
                        $this->dailyTip = null;
                    }
                } else {
                    error_log("ERREUR BaseController::__construct - Créateur non trouvé pour l'ID: " . $creatorId . " (associé à l'utilisateur ID: {$userId})");
                    $this->creator = null;
                    $this->dailyTip = null;
                }
            } else {
                error_log("DEBUG BaseController::__construct - Utilisateur ID {$userId} connecté mais pas un créateur (creator_id non défini ou différent en session).");
                $this->creator = null;
                $this->dailyTip = null;
            }
        } else {
            error_log("DEBUG BaseController::__construct - Utilisateur non connecté.");
            $this->user = null;
            $this->creator = null;
            $this->dailyTip = null;
        }
    }

    protected function getAppropriateLayout($layout = null) {
        if ($layout) {
            return $layout;
        }

        if (isset($this->creator)) {
            return 'creator_dashboard';
        }

        return 'user';
    }

    protected function render($view, $data = [], $layout = null) {
        $layoutName = $layout ?? $this->getAppropriateLayout();
        $layoutPath = APP_PATH . "/views/layouts/{$layoutName}.php";
        $viewPath = APP_PATH . "/views/{$view}.php";

        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout file not found: {$layoutPath}");
        }
        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: {$viewPath}");
        }

        if ($layoutName === 'creator_dashboard') {
            if (!isset($data['creator'])) {
                if (isset($this->creator)) {
                    $data['creator'] = $this->creator;
                } else {
                    error_log("ERREUR: Layout creator_dashboard demandé mais \$this->creator n'est pas défini (non connecté? pas créateur? Erreur DB?).");
                    $data['creator'] = ['id' => null, 'name' => 'Inconnu'];
                }
            }

            if (!isset($data['dailyTip'])) {
                if (isset($this->dailyTip)) {
                    $data['dailyTip'] = $this->dailyTip;
                    error_log("DEBUG dailyTip in BaseController::render (from property): " . print_r($data['dailyTip'] ?? 'NULL', true));
                } else {
                    error_log("DEBUG dailyTip in BaseController::render: \$this->dailyTip non défini.");
                    $data['dailyTip'] = null;
                }
            }
        }

        error_log("DEBUG BaseController::render - Données FINALES avant extract(): " . print_r($data, true));

        extract($data);
        $flashMessages = $this->flash->get();

        ob_start();

        require $viewPath;

        $content = ob_get_clean();

        require $layoutPath;
    }

    protected function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $this->flash->error('Vous devez être connecté pour accéder à cette page.');
            header('Location: /login');
            exit;
        }
    }

    protected function requireCreator() {
        $this->requireLogin();
        if (!isset($this->creator)) {
            $this->flash->error('Accès réservé aux créatrices.');
            header('Location: /');
            exit;
        }
    }

    protected function requireAdmin() {
        $this->requireLogin();
        if (!isset($this->user['is_admin']) || !$this->user['is_admin']) {
            $this->flash->error('Accès réservé aux administrateurs.');
            header('Location: /');
            exit;
        }
    }
}
