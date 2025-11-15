<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Core\BaseModel;

class Creator extends BaseModel {
    protected $table = 'creators';
    
    public function __construct($pdo = null) {
        parent::__construct($pdo);
    }

    public function createCreator(array $data) {
        try {
            $this->pdo->beginTransaction();

            $name = trim($data['name'] ?? '');
            $email = strtolower(trim($data['email'] ?? ''));
            $username = $data['username'] ?? $this->generateUniqueUsername($name !== '' ? $name : $email);
            $isActive = !empty($data['is_active']);
            $isAdmin = !empty($data['is_admin']);

            $status = $isActive ? 'active' : 'pending';
            $role = $isAdmin ? 'admin' : 'creator';

            $stmt = $this->pdo->prepare("
                INSERT INTO creators (
                    name, username, email, password, tagline, bio, donation_goal,
                    status, role, is_active, is_admin, verification_token,
                    profile_pic_url, banner_url, remember_token, remember_token_expires,
                    reset_token, reset_token_expires, last_login
                ) VALUES (
                    :name, :username, :email, :password, :tagline, :bio, :donation_goal,
                    :status, :role, :is_active, :is_admin, :verification_token,
                    :profile_pic_url, :banner_url, NULL, NULL,
                    NULL, NULL, NULL
                )
            ");

            $stmt->execute([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => $data['password'],
                'tagline' => $data['tagline'] ?? null,
                'bio' => $data['bio'] ?? null,
                'donation_goal' => $data['donation_goal'] ?? 0,
                'status' => $status,
                'role' => $role,
                'is_active' => $isActive ? 1 : 0,
                'is_admin' => $isAdmin ? 1 : 0,
                'verification_token' => $data['verification_token'] ?? null,
                'profile_pic_url' => $data['profile_pic_url'] ?? null,
                'banner_url' => $data['banner_url'] ?? null,
            ]);

            $creatorId = (int)$this->pdo->lastInsertId();
            $this->pdo->commit();

            return $creatorId;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors de la création du créateur : " . $e->getMessage());
            return false;
        }
    }

    private function generateUniqueUsername(string $value): string {
        $base = strtolower($value);
        $base = preg_replace('/[^a-z0-9]+/', '-', $base);
        $base = trim($base, '-');

        if ($base === '') {
            $base = 'creator';
        }

        $username = $base;
        $suffix = 1;

        while ($this->usernameExists($username)) {
            $username = $base . '-' . $suffix;
            $suffix++;
        }

        return $username;
    }

    private function usernameExists(string $username): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM creators WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        return (bool) $stmt->fetchColumn();
    }
    
    public function updateProfile($id, $data) {
        try {
            $sql = "UPDATE creators SET 
                name = :name, 
                email = :email, 
                tagline = :tagline, 
                bio = :bio, 
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
            
            $params = [
                'name' => $data['name'],
                'email' => $data['email'],
                'tagline' => $data['tagline'] ?? null,
                'bio' => $data['bio'] ?? null,
                'id' => $id
            ];
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du profil : " . $e->getMessage());
            return false;
        }
    }
    
    public function updateAvatar($id, $avatarPath) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE creators
                SET profile_pic_url = :avatar,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            
            return $stmt->execute([
                'id' => $id,
                'avatar' => $avatarPath
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'avatar : " . $e->getMessage());
            return false;
        }
    }
    
    public function updateBanner($id, $bannerPath) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE creators
                SET banner_url = :banner,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            
            return $stmt->execute([
                'id' => $id,
                'banner' => $bannerPath
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour de la bannière : " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePassword($id, $hashedPassword) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE creators
                SET password = :password,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            
            return $stmt->execute([
                'id' => $id,
                'password' => $hashedPassword
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du mot de passe : " . $e->getMessage());
            return false;
        }
    }
    
    public function addLink($creatorId, $data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO creator_links (creator_id, title, url, icon)
                VALUES (:creator_id, :title, :url, :icon)
            ");
            
            return $stmt->execute([
                'creator_id' => $creatorId,
                'title' => $data['title'],
                'url' => $data['url'],
                'icon' => $data['icon'] ?? null
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout du lien : " . $e->getMessage());
            return false;
        }
    }
    
    public function updateLink($linkId, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE creator_links
                SET title = :title,
                    url = :url,
                    icon = :icon
                WHERE id = :id
            ");
            
            return $stmt->execute([
                'id' => $linkId,
                'title' => $data['title'],
                'url' => $data['url'],
                'icon' => $data['icon'] ?? null
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du lien : " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteLink($linkId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM creator_links WHERE id = :id");
            return $stmt->execute(['id' => $linkId]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression du lien : " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllCreators() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.*, 
                       COALESCE(SUM(d.amount), 0) as total_donations,
                       COUNT(DISTINCT d.donor_name) as donor_count
                FROM creators c
                LEFT JOIN donations d ON c.id = d.creator_id
                WHERE c.is_active = true
                GROUP BY c.id
                ORDER BY total_donations DESC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des créatrices : " . $e->getMessage());
            return [];
        }
    }
    
    public function getCreatorById($id) {
        error_log("=== Récupération du créateur par ID ===");
        error_log("ID recherché : " . $id);
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.id, c.name, c.username, c.email, c.tagline, c.bio, 
                       c.profile_pic_url, c.banner_url, c.is_active, c.is_admin,
                       c.created_at, c.updated_at,
                       COALESCE(SUM(d.amount), 0) as total_donations,
                       COUNT(DISTINCT d.donor_name) as donor_count
                FROM creators c
                LEFT JOIN donations d ON c.id = d.creator_id
                WHERE c.id = :id AND c.is_active = true
                GROUP BY c.id
            ");
            
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("Résultat de la requête : " . print_r($result, true));
            
            if (!$result) {
                error_log("Aucun créateur actif trouvé avec cet ID");
                return null;
            }
            
            // Ajouter un objectif de dons par défaut
            $result['donation_goal'] = 1000; // Valeur par défaut
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur PDO lors de la récupération du créateur : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            return null;
        }
    }
    
    public function getCreatorLinks($creatorId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM creator_links 
                WHERE creator_id = :creator_id
                ORDER BY title ASC
            ");
            $stmt->execute(['creator_id' => $creatorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des liens : " . $e->getMessage());
            return [];
        }
    }

    public function getCreatorByUsername($username) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.*, 
                       COALESCE(SUM(d.amount), 0) as total_donations,
                       COUNT(DISTINCT d.donor_name) as donor_count
                FROM creators c
                LEFT JOIN donations d ON c.id = d.creator_id
                WHERE c.username = :username AND c.is_active = true
                GROUP BY c.id
            ");
            $stmt->execute(['username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de la créatrice : " . $e->getMessage());
            return null;
        }
    }

    public function getCreatorByEmail($email) {
        error_log("=== Recherche de créateur par email ===");
        error_log("Email recherché : " . $email);
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.id, c.name, c.email, c.password, c.is_active, c.is_admin
                FROM creators c
                WHERE c.email = :email
            ");
            
            $stmt->execute(['email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("Résultat de la requête : " . print_r($result, true));
            
            if (!$result) {
                error_log("Aucun créateur trouvé avec cet email");
                return null;
            }
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur PDO lors de la récupération du créateur : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            return null;
        }
    }

    public function saveRememberToken($creatorId, $token, $expires) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE creators
                SET remember_token = :token,
                    remember_token_expires = :expires
                WHERE id = :id
            ");
            return $stmt->execute([
                'id' => $creatorId,
                'token' => $token,
                'expires' => $expires
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la sauvegarde du token : " . $e->getMessage());
            return false;
        }
    }

    public function updateLastLogin($creatorId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE creators
                SET last_login = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $creatorId]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour de la dernière connexion : " . $e->getMessage());
            return false;
        }
    }

    public function clearRememberToken($creatorId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE creators
                SET remember_token = NULL,
                    remember_token_expires = NULL
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $creatorId]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression du remember token : " . $e->getMessage());
            return false;
        }
    }
}
