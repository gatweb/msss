<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Database;
use App\Core\View;
use App\Repositories\CreatorRepository;
use App\Repositories\DonationRepository;
use App\Repositories\DonatorNoteRepository;
use App\Repositories\LinkRepository;
use App\Repositories\PackRepository;
use DateTimeImmutable;

class DashboardController extends BaseController { // Ajouter "extends BaseController"
    private $donatorNoteRepo;
    protected $db;
    protected $view;
    protected $auth;
    private $creatorRepo;
    private $packRepo;
    private $linkRepo;
    private $donationRepo;
    private $aiToolsController;
    protected $creator;

    public function __construct(Database $db, View $view, Auth $auth, CreatorRepository $creatorRepo, PackRepository $packRepo, LinkRepository $linkRepo, DonationRepository $donationRepo, AiToolsController $aiToolsController, ?DonatorNoteRepository $donatorNoteRepo = null) {
        parent::__construct($db, $view, $auth, null, $creatorRepo); // Appel au constructeur parent avec injection
        $this->db = $db;
        $this->view = $view;
        $this->auth = $auth;
        $this->creatorRepo = $creatorRepo;
        $this->packRepo = $packRepo;
        $this->linkRepo = $linkRepo;
        $this->donationRepo = $donationRepo;
        $this->aiToolsController = $aiToolsController;
        $this->donatorNoteRepo = $donatorNoteRepo ?? new DonatorNoteRepository($db);
        error_log("=== Initialisation DashboardController (DI) ===");
    }
    
    public function index() {
        error_log("=== Début index DashboardController ===");
        error_log("Session actuelle : " . print_r($_SESSION, true));

        if (!isset($_SESSION['creator_id'])) {
            error_log("Pas de creator_id dans la session, redirection vers login");
            header('Location: /login');
            exit;
        }

        try {
            $creatorId = $_SESSION['creator_id'];
            error_log("Recherche du créateur avec l'ID : " . $creatorId);

            $creator = $this->creatorRepo->findById($creatorId);
            if ($creator) {
                $creator['is_creator'] = true;
                $this->creator = $creator;
                error_log("Résultat de CreatorRepository::findById : " . print_r($creator, true));
                error_log("DEBUG DashboardController::index - \$this->creator défini : " . print_r($this->creator, true)); // Ajout log
            } else {
                error_log("Aucun créateur trouvé, redirection vers logout");
                header('Location: /logout');
                exit;
            }

            // Statistiques dynamiques pour le dashboard
            $total_donations = (float) $this->donationRepo->getTotalAmount($creatorId);
            $donor_count = (int) $this->donationRepo->getUniqueDonorsCount($creatorId);
            $donation_goal = isset($creator['donation_goal']) ? (float)$creator['donation_goal'] : 1000;
            $progress_percentage = $donation_goal > 0 ? min(100, ($total_donations / $donation_goal) * 100) : 0;
            $recentDonationsRaw = $this->donationRepo->getDonationsByCreator($creatorId, 4);

            $typeColors = [
                'fan_fidele' => '#ffd700',
                'pack' => '#7C83FD',
                'ponctuel' => '#34d399',
                'default' => '#f59e42',
            ];

            $recent_donations = array_map(static function (array $donation) use ($typeColors) {
                $name = trim((string)($donation['donor_name'] ?? ''));
                $initials = '';

                if ($name !== '') {
                    foreach (preg_split('/\s+/', $name) as $part) {
                        if ($part === '') {
                            continue;
                        }

                        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                        if (mb_strlen($initials) >= 2) {
                            break;
                        }
                    }
                }

                if ($initials === '') {
                    $initials = '👤';
                }

                $typeKey = strtolower((string)($donation['donation_type'] ?? ''));
                $typeColor = $typeColors[$typeKey] ?? $typeColors['default'];

                $createdAt = isset($donation['created_at']) ? new DateTimeImmutable($donation['created_at']) : new DateTimeImmutable();

                return [
                    'donor_name' => $donation['donor_name'] ?? 'Anonyme',
                    'donor_email' => $donation['donor_email'] ?? '',
                    'amount' => (float)($donation['amount'] ?? 0),
                    'donation_type' => $donation['donation_type'] ?? 'Inconnu',
                    'type_color' => $typeColor,
                    'comment' => $donation['comment'] ?? null,
                    'initials' => $initials,
                    'created_at' => $createdAt,
                    'profile_url' => '/dashboard/donators/profile?email=' . urlencode($donation['donor_email'] ?? ''),
                ];
            }, $recentDonationsRaw);

            $stats = [
                'total_donations' => $total_donations,
                'donor_count' => $donor_count,
                'recent_donations' => $recent_donations,
                'donation_goal' => $donation_goal,
                'progress_percentage' => round($progress_percentage),
                'goal_reached' => $progress_percentage >= 100,
                'close_to_goal' => $progress_percentage >= 80 && $progress_percentage < 100,
                'remaining_amount' => max(0, $donation_goal - $total_donations),
            ];

            // --- DONATEURS pour le dashboard ---
            $donators = $this->donationRepo->getDonatorsByCreator($creatorId);
            // Synchronisation du statut CRM pour chaque donateur
            foreach ($donators as &$donator) {
                $note = $this->donatorNoteRepo->getNote($creatorId, $donator['donor_email']);
                $donator['crm_status'] = isset($note['crm_status']) ? $note['crm_status'] : 'prospect';
            }
            unset($donator); // break reference

            // Pagination
            $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $per_page = 10;
            $total_pages = max(1, ceil(count($donators) / $per_page));
            $donators_paginated = array_slice($donators, ($current_page - 1) * $per_page, $per_page);

            // Le conseil IA sera récupéré automatiquement par BaseController::render
            // $dailyTip = $this->aiToolsController->getDailyMotivationalTip(); 

            // Données pour la vue
            $pageTitle = 'Tableau de Bord';
            
            // $creator et $dailyTip seront injectés automatiquement par BaseController::render
            // pour le layout 'creator_dashboard'.
            $viewData = [
                'creator' => $this->creator, // Ajout explicite
                'stats' => $stats,
                'donators' => $donators_paginated,
                'total_pages' => $total_pages,
                'current_page' => $current_page,
                'pageTitle' => $pageTitle
            ];

            error_log("Chargement de la vue 'creator/dashboard' avec le layout 'creator_dashboard' et les données : " . print_r($viewData, true));

            $this->render('creator/dashboard', $viewData, 'creator_dashboard');

        } catch (\Exception $e) {
            error_log("Erreur critique dans le dashboard : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            throw $e;
        }
    }
    
    private function getAchievements($stats) {
        $achievements = [];
        
        // Badges basés sur le montant total des dons
        if ($stats['total_donations'] >= 1000) {
            $achievements[] = [
                'icon' => 'fa-crown',
                'title' => 'Reine des Dons',
                'description' => 'Vous avez atteint 1000€ de dons !'
            ];
        } elseif ($stats['total_donations'] >= 500) {
            $achievements[] = [
                'icon' => 'fa-star',
                'title' => 'Star Montante',
                'description' => 'Vous avez atteint 500€ de dons !'
            ];
        }
        
        // Badge basé sur le nombre de donateurs
        if ($stats['donor_count'] >= 10) {
            $achievements[] = [
                'icon' => 'fa-users',
                'title' => 'Communauté Fidèle',
                'description' => 'Vous avez plus de 10 donateurs !'
            ];
        }
        
        // Badge basé sur le pourcentage de l'objectif
        if ($stats['progress_percentage'] >= 100) {
            $achievements[] = [
                'icon' => 'fa-trophy',
                'title' => 'Objectif Atteint',
                'description' => 'Vous avez atteint votre objectif de dons !'
            ];
        }
        
        return $achievements;
    }
    

    
    /**
     * Affiche la gestion dynamique des dons pour la créatrice connectée
     */
    public function donations() {
        if (!isset($_SESSION['creator_id'])) {
            header('Location: /login');
            exit;
        }
        $creatorId = $_SESSION['creator_id'];
        $creator = $this->creatorRepo->findById($creatorId);
        $creator['is_creator'] = true;
        $this->creator = $creator;

        // Pagination
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 10;

        // Récupérer les dons de la créatrice
        $donations = $this->donationRepo->getDonationsByCreator($creatorId, 'all', $currentPage, $perPage);
        $totalItems = $this->donationRepo->getTotalDonationsCount($creatorId, 'all');
        $totalPages = max(1, ceil($totalItems / $perPage));

        // Statistiques
        $stats = [
            'total_amount' => $this->donationRepo->getTotalAmount($creatorId),
            'unique_donors' => $this->donationRepo->getUniqueDonorsCount($creatorId),
            // 'type_stats' => $this->donationRepo->getDonationTypeStats($creatorId), // à implémenter si besoin
        ];

        $donations = $this->donationRepo->getDonationsByCreator($creatorId); // pagination à gérer si besoin

        $this->view->render('creator/donations', [
            'donations' => $donations,
            'stats' => $stats,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'pageTitle' => 'Mes Dons',
            'creator' => $creator
        ], 'dashboard');
    }

    public function profile() {
        if (!isset($_SESSION['creator_id'])) {
            header('Location: /login');
            exit;
        }
        
        $creatorId = $_SESSION['creator_id'];
        $creator = $this->creatorRepo->findById($creatorId);
        if ($creator) {
            $creator['is_creator'] = true;
            $this->creator = $creator;
        } else {
            header('Location: /logout');
            exit;
        }
        $links = $this->linkRepo->getLinksByCreator($creatorId);
        $packs = $this->packRepo->getPacksByCreator($creatorId);
        
        $viewData = [
            'pageTitle' => 'Mon Profil',
            'creator' => $creator,
            'links' => $links,
            'packs' => $packs
        ];
        
        $this->view->render('dashboard/profile', $viewData, 'dashboard');
    }
    
    public function updateProfile() {
        if (!isset($_SESSION['creator_id'])) {
            return ['success' => false, 'message' => 'Non autorisé'];
        }
        
        $creatorId = $_SESSION['creator_id'];
        $data = [
            'name' => htmlspecialchars(trim($_POST['name'] ?? '')),
            'tagline' => htmlspecialchars(trim($_POST['tagline'] ?? '')),
            'description' => htmlspecialchars(trim($_POST['description'] ?? '')),
            'donation_goal' => filter_var($_POST['donation_goal'] ?? 0, FILTER_VALIDATE_FLOAT)
        ];
        
        // Gestion de l'upload de photos
        if (isset($_FILES['profile_pic'])) {
            $uploadResult = $this->handleImageUpload($_FILES['profile_pic'], 'profile');
            if ($uploadResult['success']) {
                $data['profile_pic_url'] = $uploadResult['path'];
            }
        }
        
        if (isset($_FILES['banner'])) {
            $uploadResult = $this->handleImageUpload($_FILES['banner'], 'banner');
            if ($uploadResult['success']) {
                $data['banner_url'] = $uploadResult['path'];
            }
        }
        
        $result = $this->creatorRepo->updateCreator($creatorId, $data);
        return $result;
    }
    
    private function handleImageUpload($file, $type) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Erreur lors de l\'upload'];
        }
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Type de fichier non autorisé'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Fichier trop volumineux'];
        }
        
        $uploadDir = __DIR__ . '/../../public/uploads/' . $type . 's/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'path' => '/uploads/' . $type . 's/' . $filename
            ];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de l\'enregistrement du fichier'];
    }

    /**
     * Affiche la liste des donateurs uniques pour la créatrice connectée
     */
    /**
     * Affiche la fiche technique d'un donateur (par email)
     */
    public function donatorProfile() {
    if (!isset($_SESSION['creator_id'])) {
        header('Location: /login');
        exit;
    }
    $creatorId = $_SESSION['creator_id'];
    $email = $_GET['email'] ?? null;
    if (!$email) {
        header('Location: /dashboard/donators');
        exit;
    }
    // Récupérer les infos du donateur (agrégées)
    $donator = null;
    $allDonators = $this->donationRepo->getDonatorsByCreator($creatorId);
    foreach ($allDonators as $d) {
        if ($d['donor_email'] === $email) {
            $donator = $d;
            break;
        }
    }
    if (!$donator) {
        header('Location: /dashboard/donators');
        exit;
    }
    // Historique des dons
    $donations = $this->donationRepo->getDonationsByCreator($creatorId);
    $donations = array_filter($donations, function($don) use ($email) {
        return $don['donor_email'] === $email;
    });

    // Traitement du formulaire POST pour sauvegarder les notes
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $notes = [
            'crm_phone' => trim($_POST['crm_phone'] ?? ''),
            'crm_status' => trim($_POST['crm_status'] ?? 'prospect'),
            'pref_gift' => isset($_POST['pref_gift']),
            'pref_anonymous' => isset($_POST['pref_anonymous']),
            'pref_birthday' => isset($_POST['pref_birthday']),
            'crm_source' => trim($_POST['crm_source'] ?? ''),
            'crm_birthday' => trim($_POST['crm_birthday'] ?? ''),
            'merci_envoye' => isset($_POST['merci_envoye']),
            'cadeau_envoye' => isset($_POST['cadeau_envoye']),
            'vip' => isset($_POST['vip']),
            'fan_fidele' => isset($_POST['fan_fidele']),
            'fan_fidele_since' => $_POST['fan_fidele_since'] ?? '',
            'commentaire' => trim($_POST['commentaire'] ?? ''),
        ];
        // Si "Fan fidèle" est coché, enregistrer la date d'activation si absente
        if ($notes['fan_fidele'] && empty($notes['fan_fidele_since'])) {
            $notes['fan_fidele_since'] = date('Y-m-d H:i:s');
        } elseif (!$notes['fan_fidele']) {
            $notes['fan_fidele_since'] = '';
        }
        $this->donatorNoteRepo->saveNote($creatorId, $email, $notes);
        // Reload to avoid resubmission
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
    // Charger les notes sauvegardées
    $notes = $this->donatorNoteRepo->getNote($creatorId, $email);
    if (!$notes) {
        $notes = [
            'crm_phone' => '',
            'crm_status' => 'prospect',
            'pref_gift' => false,
            'pref_anonymous' => false,
            'pref_birthday' => false,
            'crm_source' => '',
            'crm_birthday' => '',
            'merci_envoye' => false,
            'cadeau_envoye' => false,
            'vip' => false,
            'fan_fidele' => false,
            'fan_fidele_since' => '',
            'commentaire' => ''
        ];
    }
    $this->view->render('creator/donator_profile', [
        'donator' => $donator,
        'donations' => $donations,
        'notes' => $notes,
        'pageTitle' => 'Fiche Donateur'
    ], 'dashboard');
}


    public function donators() {
        if (!isset($_SESSION['creator_id'])) {
            header('Location: /login');
            exit;
        }
        $creatorId = $_SESSION['creator_id'];
        $creator = $this->creatorRepo->findById($creatorId);
        $creator['is_creator'] = true;
        $this->creator = $creator;

        $donators = $this->donationRepo->getDonatorsByCreator($creatorId);
        // --- Synchronisation du statut CRM pour chaque donateur (LED)
        foreach ($donators as &$donator) {
            $note = $this->donatorNoteRepo->getNote($creatorId, $donator['donor_email']);
            $donator['crm_status'] = isset($note['crm_status']) ? $note['crm_status'] : 'prospect';
        }
        unset($donator); // break reference

        // Statistiques minimales pour la vue
        $stats = [
            'total_donators' => count($donators),
            'recurring_donators' => 0, // Non géré ici
            'average_donation' => ($donators && count($donators) > 0) ? round(array_sum(array_column($donators, 'total_amount')) / count($donators), 2) : 0,
        ];
        $total_pages = 1;
        $current_page = 1;

        $this->view->render('creator/donators', [
            'donators' => $donators,
            'pageTitle' => 'Mes Donateurs',
            'creator' => $creator,
            'stats' => $stats,
            'total_pages' => $total_pages,
            'current_page' => $current_page
        ], 'creator_dashboard');
    }
}
