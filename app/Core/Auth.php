<?php

namespace App\Core;

class Auth {
    private $pdo;
    private $session;

    public function __construct(Session $session) {
        $this->pdo = Database::getInstance()->getConnection();
        $this->session = $session;
    }

    public function login($email, $password) {
        try {
            error_log("DEBUG: Début de la méthode login");
            
            if (!$this->pdo) {
                return false;
            }
            
            $stmt = $this->pdo->prepare("SELECT * FROM creators WHERE email = ? AND is_active = 1");
            if (!$stmt) {
                return false;
            }
            
            $stmt->execute([$email]);
            $creator = $stmt->fetch();
            
            if (!$creator) {
                return false;
            }
            
            $passwordValid = password_verify($password, $creator['password']);
            
            if ($passwordValid) {
                // Initialiser la session
                if (!$this->session->has('initialized')) {
                    $this->session->regenerate();
                    $this->session->set('initialized', 1);
                    $this->session->set('csrf_token', $this->generateToken());
                }

                // Définir les données de session
                $this->session->set('creator_id', $creator['id']);
                $this->session->set('creator_name', $creator['name']);
                $this->session->set('creator_username', $creator['username'] ?? null);
                $this->session->set('creator_is_admin', $creator['is_admin']);
                
                return true;
            }

            return false;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la connexion : ' . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        $this->session->destroy();
        return true;
    }

    public function isLoggedIn() {
        return $this->session->has('creator_id') && $this->session->has('initialized');
    }

    public function getCurrentUserId() {
        return $this->session->get('creator_id');
    }

    public function getCurrentUserRole() {
        return $this->session->get('creator_is_admin') ? 'admin' : 'user';
    }

    public function isAdmin() {
        return $this->session->get('creator_is_admin') === 1;
    }

    public function isCreator() {
        return $this->isLoggedIn();
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
        return hash_equals($this->session->get('csrf_token', ''), $token);
    }
}
