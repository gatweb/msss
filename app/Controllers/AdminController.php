<?php

namespace App\Controllers;

use PDO;
use PDOException;
use Exception;
use App\Core\BaseController;
use App\Models\Creator;
use App\Models\Donation;
use App\Models\Pack;
use App\Core\Database;

class AdminController extends BaseController {
    protected $creator;
    protected $donation;
    protected $pack;

    public function __construct() {
        parent::__construct();
        $this->creator = new Creator();
        $this->donation = new Donation();
        $this->pack = new Pack();
    }

    public function index() {
        // Vérifier l'authentification et les droits admin
        $this->auth->requireAdmin();
        
        // Récupérer les statistiques globales
        $stats = [
            'total_creators' => $this->getCreatorCount(),
            'total_donations' => $this->getTotalDonations(),
            'total_amount' => $this->getTotalAmount(),
            'total_packs' => $this->getPackCount(),
            'recent_donations' => $this->getRecentDonations(),
            'top_creators' => $this->getTopCreators(),
            'monthly_stats' => $this->getMonthlyStats(),
            'donation_types' => $this->getDonationTypes()
        ];
        
        $this->render('admin/index', ['stats' => $stats], 'admin');
    }

    private function getCreatorCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM creators WHERE is_active = true");
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage des créateurs : " . $e->getMessage());
            return 0;
        }
    }

    private function getTotalDonations() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM donations");
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage des dons : " . $e->getMessage());
            return 0;
        }
    }

    private function getTotalAmount() {
        try {
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations");
            $amount = $stmt->fetchColumn();
            return $amount === null ? 0 : (float)$amount;
        } catch (\PDOException $e) {
            error_log("Erreur lors du calcul du montant total : " . $e->getMessage());
            return 0;
        }
    }

    private function getPackCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM packs WHERE is_active = true");
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage des packs : " . $e->getMessage());
            return 0;
        }
    }

    private function getRecentDonations($limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT d.*, c.name as creator_name 
                FROM donations d
                JOIN creators c ON d.creator_id = c.id
                ORDER BY d.created_at DESC
                LIMIT :limit
            ");
            $stmt->execute(['limit' => $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des dons récents : " . $e->getMessage());
            return [];
        }
    }

    private function getTopCreators($limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.*, 
                       COUNT(d.id) as donation_count,
                       COALESCE(SUM(d.amount), 0) as total_amount
                FROM creators c
                LEFT JOIN donations d ON c.id = d.creator_id
                WHERE c.is_active = true
                GROUP BY c.id
                ORDER BY total_amount DESC
                LIMIT :limit
            ");
            $stmt->execute(['limit' => $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des meilleurs créateurs : " . $e->getMessage());
            return [];
        }
    }

    private function getMonthlyStats($months = 12) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT strftime('%Y-%m', created_at) as month,
                       COUNT(*) as count,
                       SUM(amount) as total
                FROM donations
                WHERE created_at >= date('now', '-' || :months || ' months')
                GROUP BY month
                ORDER BY month DESC
            ");
            $stmt->execute(['months' => $months]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques mensuelles : " . $e->getMessage());
            return [];
        }
    }

    private function getDonationTypes() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    donation_type,
                    COUNT(*) as count,
                    COALESCE(SUM(amount), 0) as total_amount
                FROM donations
                GROUP BY donation_type
                ORDER BY total_amount DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des types de dons : " . $e->getMessage());
            return [];
        }
    }

    public function creators() {
        // Vérifier l'authentification et les droits admin
        $this->auth->requireAdmin();

        // Récupérer les paramètres de pagination
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Récupérer les créateurs avec leurs statistiques
        $creators = $this->getCreatorsWithStats($offset, $perPage);
        $totalCreators = $this->getCreatorCount();
        $totalPages = ceil($totalCreators / $perPage);

        $this->render('admin/creators', [
            'creators' => $creators,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalCreators' => $totalCreators
        ], 'admin');
    }

    private function getCreatorsWithStats($offset, $limit) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    c.*,
                    COUNT(DISTINCT d.id) as donation_count,
                    COALESCE(SUM(d.amount), 0) as total_amount,
                    COUNT(DISTINCT p.id) as pack_count,
                    MAX(d.created_at) as last_donation
                FROM creators c
                LEFT JOIN donations d ON c.id = d.creator_id
                LEFT JOIN packs p ON c.id = p.creator_id
                GROUP BY c.id
                ORDER BY c.created_at DESC
                LIMIT :offset, :limit
            ");
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des créateurs : " . $e->getMessage());
            return [];
        }
    }

    public function toggleCreatorStatus() {
        // Vérifier les droits admin
        $this->requireAdmin();
        
        // Vérifier le jeton CSRF
        $this->validateToken();
        
        $creatorId = intval($_POST['creator_id']);
        
        try {
            $creator = $this->creator->getCreatorById($creatorId);
            if (!$creator) {
                throw new Exception("Créateur non trouvé.");
            }
            
            // Inverser le statut
            $newStatus = !$creator['is_active'];
            
            $stmt = $this->pdo->prepare("
                UPDATE creators 
                SET is_active = :is_active 
                WHERE id = :id
            ");
            
            if ($stmt->execute([
                'is_active' => $newStatus,
                'id' => $creatorId
            ])) {
                $_SESSION['success'] = "Le statut du créateur a été mis à jour.";
            } else {
                throw new Exception("Erreur lors de la mise à jour du statut.");
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /profile/admin/creators');
        exit;
    }

    public function deleteCreator() {
        // Vérifier les droits admin
        $this->requireAdmin();
        
        // Vérifier le jeton CSRF
        $this->validateToken();
        
        $creatorId = intval($_POST['creator_id']);
        
        try {
            $creator = $this->creator->getCreatorById($creatorId);
            if (!$creator) {
                throw new Exception("Créateur non trouvé.");
            }
            
            // Commencer une transaction
            $this->pdo->beginTransaction();
            
            try {
                // Supprimer les liens sociaux
                $stmt = $this->pdo->prepare("DELETE FROM social_links WHERE creator_id = :id");
                $stmt->execute(['id' => $creatorId]);
                
                // Supprimer les packs
                $stmt = $this->pdo->prepare("DELETE FROM packs WHERE creator_id = :id");
                $stmt->execute(['id' => $creatorId]);
                
                // Anonymiser les dons (au lieu de les supprimer)
                $stmt = $this->pdo->prepare("
                    UPDATE donations 
                    SET creator_id = NULL 
                    WHERE creator_id = :id
                ");
                $stmt->execute(['id' => $creatorId]);
                
                // Supprimer le créateur
                $stmt = $this->pdo->prepare("DELETE FROM creators WHERE id = :id");
                $stmt->execute(['id' => $creatorId]);
                
                // Valider la transaction
                $this->pdo->commit();
                
                // Supprimer les fichiers
                if ($creator['profile_pic_url']) {
                    $this->fileUploader->deleteFile($creator['profile_pic_url']);
                }
                if ($creator['banner_url']) {
                    $this->fileUploader->deleteFile($creator['banner_url']);
                }
                
                $_SESSION['success'] = "Le créateur a été supprimé avec succès.";
                
            } catch (Exception $e) {
                // Annuler la transaction en cas d'erreur
                $this->pdo->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /profile/admin/creators');
        exit;
    }
}
