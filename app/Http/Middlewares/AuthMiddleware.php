<?php

class AuthMiddleware {
    private $pdo;
    private $creatorModel;

    public function __construct() {
        $this->pdo = Database::getInstance();
        $this->creatorModel = new Creator($this->pdo);
    }

    public function handle() {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est déjà connecté via la session
        if (isset($_SESSION['creator_id'])) {
            return true;
        }

        // Vérifier le cookie "Se souvenir de moi"
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $creator = $this->creatorModel->getCreatorByRememberToken($token);

            if ($creator && $creator['remember_token_expires'] > date('Y-m-d H:i:s')) {
                // Le token est valide, connecter l'utilisateur
                $_SESSION['creator_id'] = $creator['id'];
                $_SESSION['creator_name'] = $creator['name'];
                $_SESSION['creator_role'] = $creator['role'];

                // Renouveler le token pour 30 jours
                $newToken = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                $this->creatorModel->saveRememberToken($creator['id'], $newToken, $expires);
                
                setcookie(
                    'remember_token',
                    $newToken,
                    [
                        'expires' => strtotime('+30 days'),
                        'path' => '/',
                        'domain' => '',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );

                return true;
            } else {
                // Token invalide ou expiré, supprimer le cookie
                setcookie(
                    'remember_token',
                    '',
                    [
                        'expires' => time() - 3600,
                        'path' => '/',
                        'domain' => '',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );
            }
        }

        // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
        header('Location: /login');
        exit;
    }

    public function isAdmin() {
        if (!isset($_SESSION['creator_role'])) {
            return false;
        }

        return $_SESSION['creator_role'] === 'admin';
    }

    public function isCreator($creatorId) {
        if (!isset($_SESSION['creator_id'])) {
            return false;
        }

        return $_SESSION['creator_id'] == $creatorId || $this->isAdmin();
    }

    public function getCurrentCreator() {
        if (!isset($_SESSION['creator_id'])) {
            return null;
        }

        return $this->creatorModel->getCreatorById($_SESSION['creator_id']);
    }

    public function requireAdmin() {
        if (!$this->isAdmin()) {
            http_response_code(403);
            require_once APP_PATH . '/views/errors/403.php';
            exit;
        }
    }

    public function requireCreator($creatorId) {
        if (!$this->isCreator($creatorId)) {
            http_response_code(403);
            require_once APP_PATH . '/views/errors/403.php';
            exit;
        }
    }
}
