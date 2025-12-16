<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;
use App\Repositories\DonationRepository;
use App\Services\StripeService;
use App\Core\Csrf;

class DonationsController extends BaseController {
    private $donationRepo;
    private $stripeService;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        DonationRepository $donationRepo,
        StripeService $stripeService
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->donationRepo = $donationRepo;
        $this->stripeService = $stripeService;
    }
    
    public function links($creatorId) {
        $creator = $this->creatorRepository->findById($creatorId);
        
        if (!$creator || !$creator['is_active']) {
            header('Location: /404');
            exit;
        }

        $this->render('donation/links.html.twig', [
            'pageTitle' => 'Soutenir ' . $creator['name'],
            'creator' => $creator
        ]);
    }

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
                $this->creatorRepository->updateStats($_POST['creator_id']);
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
        $donations = $this->donationRepo->getAllDonations();
        $donationData = $this->donationRepo->getDonationGoalAndTotal();
        
        $this->render('donations/index.html.twig', [
            'donations' => $donations,
            'donation_goal' => $donationData['goal'],
            'total_donations' => $donationData['total'],
            'progress_percentage' => ($donationData['goal'] > 0) ? 
                                   min(100, ($donationData['total'] / $donationData['goal']) * 100) : 0,
            'valid_donation_types' => ['PayPal', 'Photo', 'Cadeau', 'Autre']
        ]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
            return;
        }
        
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/dashboard');
            return;
        }

        $donorName = trim(strip_tags(filter_input(INPUT_POST, 'donor_name')));
        $donorEmail = trim(strip_tags(filter_input(INPUT_POST, 'donor_email')));
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
        $donationType = trim(strip_tags(filter_input(INPUT_POST, 'donation_type')));
        $comment = trim(strip_tags(filter_input(INPUT_POST, 'comment')));
        
        if (!$donorName || !$donorEmail || !$amount || !$donationType) {
            $this->flash->error('Données invalides');
            $this->redirect('/dashboard');
            return;
        }
        
        $creatorId = $this->getCurrentUserId();
        if (!$creatorId) {
            $this->flash->error('Créatrice introuvable.');
            $this->redirect('/dashboard');
            return;
        }

        if ($this->donationRepo->addDonation($creatorId, $donorName, $donorEmail, $amount, $donationType, $comment)) {
            $this->flash->success('Don ajouté avec succès !');
        } else {
            $this->flash->error("Erreur lors de l'ajout du don");
        }
        $this->redirect('/dashboard');
    }

    public function publicAdd() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            return;
        }

        $creatorId = filter_input(INPUT_POST, 'creator_id', FILTER_VALIDATE_INT);
        $donorName = trim(filter_input(INPUT_POST, 'donor_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $donorEmail = filter_input(INPUT_POST, 'donor_email', FILTER_VALIDATE_EMAIL);
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
        $donationType = trim(filter_input(INPUT_POST, 'donation_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $comment = trim(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) ?: null;

        if (!$creatorId || !$donorName || !$donorEmail || !$amount || !$donationType) {
            $this->flash->error('Merci de vérifier le formulaire de don.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            return;
        }

        $creator = $this->creatorRepository->findById($creatorId);
        if (!$creator || empty($creator['is_active'])) {
            $this->flash->error('Impossible de trouver cette créatrice.');
            $this->redirect('/'); 
            return;
        }

        $stored = $this->donationRepo->addDonation($creatorId, $donorName, $donorEmail, (float)$amount, $donationType, $comment);
        if ($stored) {
            $this->flash->success('Merci pour votre soutien ! Votre don a bien été enregistré.');
        } else {
            $this->flash->error('Erreur lors de la sauvegarde du don. Veuillez réessayer plus tard.');
        }

        $redirectTarget = !empty($creator['username']) ? '/creator/' . $creator['username'] : '/';
        $this->redirect($redirectTarget);
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

    public function creatorIndex() {
        $this->requireCreator();

        // Dummy data for now
        $donations = [
            [
                'id' => 1,
                'donor_name' => 'John Doe',
                'amount' => 50,
                'donation_type' => 'PayPal',
                'timer_status' => 'stopped',
                'timer_elapsed_seconds' => 0,
                'comment' => 'Great work!'
            ]
        ];
        $donationData = [
            'goal' => 1000,
            'total' => 500,
        ];

        $this->view->addScript('/assets/js/donations.js');
        $this->render('donation/index.html.twig', [
            'donations' => $donations,
            'donation_goal' => $donationData['goal'],
            'total_donations' => $donationData['total'],
            'progress_percentage' => ($donationData['goal'] > 0) ? 
                                   min(100, ($donationData['total'] / $donationData['goal']) * 100) : 0,
            'valid_donation_types' => ['PayPal', 'Photo', 'Cadeau', 'Autre'],
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function form($creatorId) {
        $creator = $this->creatorRepository->findById($creatorId);
        
        if (!$creator || !$creator['is_active']) {
            header('Location: /404');
            exit;
        }

        $this->render('donation/form.html.twig', [
            'pageTitle' => 'Faire un don à ' . $creator['name'],
            'creator' => $creator,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function initiate() {
        // Dummy implementation
        $this->jsonResponse(['url' => '/donation/success']);
    }

    public function success() {
        $this->render('donation/success.html.twig', [
            'pageTitle' => 'Merci pour votre don !'
        ]);
    }

    public function error() {
        $this->render('donation/error.html.twig', [
            'pageTitle' => 'Erreur de don'
        ]);
    }
}
