<?php

namespace App\Controllers;

use PDO;
use Exception;
use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;
use App\Repositories\DonationRepository;
use App\Repositories\PackRepository;
use App\Core\Csrf;

class AdminController extends BaseController {
    protected $donationRepository;
    protected $packRepository;
    protected $pdo;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        DonationRepository $donationRepository,
        PackRepository $packRepository,
        PDO $pdo
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->donationRepository = $donationRepository;
        $this->packRepository = $packRepository;
        $this->pdo = $pdo;
    }

    public function index() {
        $this->requireAdmin();
        
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
        
        $this->render('admin/index.html.twig', ['stats' => $stats], 'admin');
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
            $stmt = $this->pdo->query("
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
        $this->requireAdmin();

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $creators = $this->getCreatorsWithStats($offset, $perPage);
        $totalCreators = $this->getCreatorCount();
        $totalPages = ceil($totalCreators / $perPage);

        $this->render('admin/creators.html.twig', [
            'creators' => $creators,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalCreators' => $totalCreators,
            'csrf_token' => $this->generateCsrfToken()
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
        $this->requireAdmin();
        
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/profile/admin/creators');
            return;
        }
        
        $creatorId = intval($_POST['creator_id']);
        
        try {
            $creator = $this->creatorRepository->findById($creatorId);
            if (!$creator) {
                throw new Exception("Créateur non trouvé.");
            }
            
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
                $this->flash->success("Le statut du créateur a été mis à jour.");
            } else {
                throw new Exception("Erreur lors de la mise à jour du statut.");
            }
            
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
        }
        
        header('Location: /profile/admin/creators');
        exit;
    }

    public function deleteCreator() {
        $this->requireAdmin();
        
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/profile/admin/creators');
            return;
        }
        
        $creatorId = intval($_POST['creator_id']);
        
        try {
            $creator = $this->creatorRepository->findById($creatorId);
            if (!$creator) {
                throw new Exception("Créateur non trouvé.");
            }
            
            $this->pdo->beginTransaction();
            
            try {
                $stmt = $this->pdo->prepare("DELETE FROM creator_links WHERE creator_id = :id");
                $stmt->execute(['id' => $creatorId]);
                
                $stmt = $this->pdo->prepare("DELETE FROM packs WHERE creator_id = :id");
                $stmt->execute(['id' => $creatorId]);
                
                $stmt = $this->pdo->prepare("UPDATE donations SET creator_id = NULL WHERE creator_id = :id");
                $stmt->execute(['id' => $creatorId]);
                
                $stmt = $this->pdo->prepare("DELETE FROM creators WHERE id = :id");
                $stmt->execute(['id' => $creatorId]);
                
                $this->pdo->commit();
                
                $this->flash->success("Le créateur a été supprimé avec succès.");
                
            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
        }
        
        header('Location: /profile/admin/creators');
        exit;
    }

    public function settings() {
        $this->requireAdmin();

        // Dummy data for now
        $settings = [
            'site_name' => 'MSSS',
            'site_description' => 'Plateforme de dons.',
            'contact_email' => 'contact@msss.com',
            'maintenance_mode' => false,
            'min_donation' => 1.0,
            'max_donation' => 1000.0,
            'platform_fee' => 5.0,
            'enabled_payment_methods' => ['cb', 'paypal'],
            'max_packs' => 10,
            'max_file_size' => 16,
            'allowed_file_types' => 'jpg,png,gif',
            'auto_approve' => true,
            'max_login_attempts' => 5,
            'lockout_duration' => 15,
            'session_timeout' => 60,
            'require_strong_passwords' => true,
            'enable_2fa' => false,
            'enabled_email_notifications' => ['new_donation', 'new_message'],
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_user' => 'user',
            'smtp_pass' => 'password',
        ];

        $paymentMethods = [
            ['id' => 'cb', 'name' => 'Carte Bancaire'],
            ['id' => 'paypal', 'name' => 'PayPal'],
        ];

        $emailNotificationTypes = [
            ['id' => 'new_donation', 'name' => 'Nouveau don'],
            ['id' => 'new_message', 'name' => 'Nouveau message'],
        ];

        $this->render('admin/settings.html.twig', [
            'settings' => $settings,
            'paymentMethods' => $paymentMethods,
            'emailNotificationTypes' => $emailNotificationTypes,
            'csrf_token' => $this->generateCsrfToken()
        ], 'admin');
    }

    public function saveSettings() {
        $this->requireAdmin();
        // TODO: Implement save logic
        $this->flash->success('Paramètres enregistrés.');
        $this->redirect('/profile/admin/settings');
    }

    public function testSmtp() {
        $this->requireAdmin();
        // TODO: Implement SMTP test logic
        return $this->jsonResponse(['success' => true]);
    }

    public function stats() {
        $this->requireAdmin();

        // Dummy data for now
        $stats = [
            'active_creators' => 123,
            'creators_trend' => 5,
            'total_donations' => 12345.67,
            'donations_trend' => 12,
            'active_packs' => 45,
            'total_packs' => 100,
            'average_donation' => 25.50,
            'total_transactions' => 484,
            'top_creators' => [
                [
                    'id' => 1,
                    'name' => 'Creator 1',
                    'avatar' => '/assets/img/default-avatar.png',
                    'created_at' => '2023-01-15',
                    'total_donations' => 5000,
                    'total_donors' => 100,
                    'average_donation' => 50,
                    'growth' => 10,
                ]
            ],
            'donations_data' => [
                'labels' => ['Jan', 'Feb', 'Mar'],
                'values' => [1000, 2000, 1500]
            ],
            'creators_data' => [
                'labels' => ['Jan', 'Feb', 'Mar'],
                'values' => [10, 5, 12]
            ]
        ];

        $this->render('admin/stats.html.twig', [
            'stats' => $stats
        ], 'admin');
    }

    public function getStatsApi() {
        $this->requireAdmin();
        // TODO: Implement API logic
        return $this->jsonResponse([]);
    }

    public function exportStats() {
        $this->requireAdmin();
        // TODO: Implement export logic
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="stats.csv"');
        echo "col1,col2,col3";
        exit;
    }

    public function transactions() {
        $this->requireAdmin();

        $filters = $_GET;

        // Dummy data for now
        $transactions = [
            [
                'id' => 1,
                'created_at' => '2023-03-15 10:00:00',
                'creator_name' => 'Creator 1',
                'donor_name' => 'Donor 1',
                'amount' => 50.0,
                'type' => 'Donation',
                'message' => 'Great work!'
            ]
        ];
        $creators = [
            ['id' => 1, 'name' => 'Creator 1'],
            ['id' => 2, 'name' => 'Creator 2'],
        ];

        $this->render('admin/transactions.html.twig', [
            'transactions' => $transactions,
            'creators' => $creators,
            'page' => 1,
            'totalPages' => 1,
            'filters' => $filters,
            'csrf_token' => $this->generateCsrfToken()
        ], 'admin');
    }

    public function viewTransaction($id) {
        $this->requireAdmin();
        // TODO: Implement view logic
        echo "Viewing transaction $id";
    }

    public function anonymizeTransaction() {
        $this->requireAdmin();
        // TODO: Implement anonymize logic
        $this->flash->success('Transaction anonymisée.');
        $this->redirect('/admin/transactions');
    }

    public function exportTransactions() {
        $this->requireAdmin();
        // TODO: Implement export logic
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="transactions.csv"');
        echo "col1,col2,col3";
        exit;
    }
}
