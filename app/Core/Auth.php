<?php

namespace App\Core;

class Auth {
    private $pdo;
    private $session;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function login($email, $password) {
        try {
            error_log("DEBUG: Début de la méthode login");
            error_log("DEBUG: Email fourni = " . $email);
            error_log("DEBUG: Password fourni = " . substr($password, 0, 1) . '***');
            
            if (!$this->pdo) {
                error_log("ERREUR: PDO n'est pas initialisé!");
                return false;
            }
            
            $stmt = $this->pdo->prepare("SELECT * FROM creators WHERE email = ? AND is_active = 1");
            if (!$stmt) {
                error_log("ERREUR: Échec de la préparation de la requête");
                return false;
            }
            
            $stmt->execute([$email]);
            $creator = $stmt->fetch();
            
            error_log("DEBUG: Résultat de la requête : " . ($creator ? "Utilisateur trouvé" : "Utilisateur non trouvé"));
            if ($creator) {
                error_log("DEBUG: Hash stocké = " . $creator['password']);
            }
            
            if (!$creator) {
                error_log("DEBUG: Aucun utilisateur trouvé avec cet email");
                return false;
            }
            
            $passwordValid = password_verify($password, $creator['password']);
            error_log("DEBUG: Résultat de password_verify = " . ($passwordValid ? "true" : "false"));
            
            if ($passwordValid) {
                // Initialiser la session
                if (!isset($_SESSION['initialized'])) {
                    session_regenerate_id(true);
                    $_SESSION['initialized'] = 1;
                    $_SESSION['csrf_token'] = $this->generateToken();
                }

                // Définir les données de session
                $_SESSION['creator_id'] = $creator['id'];
                $_SESSION['creator_name'] = $creator['name'];
                $_SESSION['creator_username'] = $creator['username'] ?? null;
                $_SESSION['creator_is_admin'] = $creator['is_admin'];
                
                return true;
            }

            return false;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la connexion : ' . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['creator_id']) && isset($_SESSION['initialized']);
    }

    public function getCurrentUserId() {
        return $_SESSION['creator_id'] ?? null;
    }

    public function getCurrentUserRole() {
        return $_SESSION['creator_is_admin'] ? 'admin' : 'user';
    }

    public function isAdmin() {
        return isset($_SESSION['creator_is_admin']) && $_SESSION['creator_is_admin'] === 1;
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireAdmin() {
        if (!$this->isAdmin()) {
            header('Location: /403');
            exit;
        }
    }

    public function generatePasswordHash($password) {
        return password_hash($password, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
    }

    public function generateToken() {
        return bin2hex(random_bytes(32));
    }

    public function validateToken($token) {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
