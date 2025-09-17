<?php

namespace App\Controllers;

use App\Core\BaseController;
// Classe renommée en DonationsController pour cohérence avec les routes et conventions MVC
use App\Models\Creator;
use App\Models\Donation;

use App\Repositories\DonationRepository;
use App\Repositories\CreatorRepository;

class DonationsController extends \App\Core\BaseController {
    private $donationRepo;
    private $creatorRepo;
    private $stripeService;

    public function __construct(DonationRepository $donationRepo, CreatorRepository $creatorRepo, $stripeService = null) {
        parent::__construct();
        $this->donationRepo = $donationRepo;
        $this->creatorRepo = $creatorRepo;
        $this->stripeService = $stripeService;
    }
    
    /**
     * Affiche les liens de soutien d'une créatrice
     */
    public function links($creatorId) {
        $creator = $this->creatorRepo->findById($creatorId);
        
        if (!$creator || !$creator['is_active']) {
            header('Location: /404');
            exit;
        }

        $this->render('donation/links', [
            'pageTitle' => 'Soutenir ' . $creator['name'],
            'creator' => $creator
        ]);
    }

    /**
     * Enregistre un don externe
     */
    public function record() {
        try {
            if (!isset($_POST['creator_id'], $_POST['amount'], $_POST['platform'])) {
                $this->jsonResponse(['error' => 'Données manquantes'], 400);
                return;
            }

            $result = $this->donationRepo->create([
                'creator_id' => intval($_POST['creator_id']),
                'amount' => floatval($_POST['amount']),
                'platform' => $_POST['platform'],
                'external_reference' => $_POST['reference'] ?? null,
                'status' => 'completed'
            ]);

            if ($result) {
                // Mise à jour des statistiques
                $this->creatorRepo->updateStats($_POST['creator_id']);
                $this->jsonResponse(['status' => 'success']);
            } else {
                $this->jsonResponse(['error' => 'Erreur lors de l\'enregistrement du don'], 500);
            }

        } catch (\Exception $e) {
            error_log('Erreur lors de l\'enregistrement du don: ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur serveur'], 500);
        }
    }

    public function index() {
        // Récupérer les données nécessaires
        $donations = $this->donationRepo->getAllDonations();
        $donationData = $this->donationRepo->getDonationGoalAndTotal();
        
        // Préparer les données pour la vue
        $viewData = [
            'donations' => $donations,
            'donation_goal' => $donationData['goal'],
            'total_donations' => $donationData['total'],
            'progress_percentage' => ($donationData['goal'] > 0) ? 
                                   min(100, ($donationData['total'] / $donationData['goal']) * 100) : 0,
            'valid_donation_types' => ['PayPal', 'Photo', 'Cadeau', 'Autre']
        ];
        
        // Charger la vue
        require_once APP_PATH . '/views/donations/index.php';
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }
        
        $donorName = trim(strip_tags(filter_input(INPUT_POST, 'donor_name')));
        $donorEmail = trim(strip_tags(filter_input(INPUT_POST, 'donor_email')));
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
        $donationType = trim(strip_tags(filter_input(INPUT_POST, 'donation_type')));
        $comment = trim(strip_tags(filter_input(INPUT_POST, 'comment')));
        
        if (!$donorName || !$donorEmail || !$amount || !$donationType) {
            return ['success' => false, 'message' => 'Données invalides'];
        }
        
        if ($this->donationRepo->addDonation($donorName, $donorEmail, $amount, $donationType, $comment)) {
            $this->flash->success('Don ajouté avec succès !');
            $this->redirect('/dashboard');
        }
        $this->flash->error("Erreur lors de l'ajout du don");
        $this->redirect('/dashboard');
    }
    
    public function updateTimer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }
        
        $donationId = filter_input(INPUT_POST, 'donation_id', FILTER_VALIDATE_INT);
        $action = isset($_POST['start_timer']) ? 'start' : 'stop';
        
        if (!$donationId) {
            return ['success' => false, 'message' => 'ID de don invalide'];
        }
        
        if ($this->donationRepo->updateTimer($donationId, $action)) {
            return ['success' => true, 'message' => 'Timer mis à jour avec succès'];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour du timer'];
    }
}
