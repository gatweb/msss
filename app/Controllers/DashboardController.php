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

    public function stats() {
        if (!isset($_SESSION['creator_id'])) {
            header('Location: /login');
            exit;
        }

        $creatorId = (int)$_SESSION['creator_id'];
        $creator = $this->creatorRepo->findById($creatorId);

        if (!$creator) {
            header('Location: /logout');
            exit;
        }

        $creator['is_creator'] = true;
        $this->creator = $creator;

        $now = new DateTimeImmutable('now');
        $recentRange = new DateInterval('P30D');
        $rangeStart = $now->sub($recentRange);
        $previousStart = $now->sub(new DateInterval('P60D'));

        $donationsRaw = $this->donationRepo->getDonationsByCreator($creatorId);
        $donations = array_map(static function (array $donation) {
            $timestamp = $donation['donation_timestamp'] ?? $donation['created_at'] ?? 'now';

            try {
                $createdAt = new DateTimeImmutable($timestamp);
            } catch (\Exception $e) {
                $createdAt = new DateTimeImmutable('now');
            }

            $email = strtolower(trim($donation['donor_email'] ?? ''));
            $name = trim($donation['donor_name'] ?? '');
            $donorKey = $email;

            if ($donorKey === '') {
                if ($name !== '') {
                    $normalizedName = function_exists('mb_strtolower') ? mb_strtolower($name) : strtolower($name);
                    $donorKey = md5($normalizedName);
                } else {
                    $donorKey = 'anonymous_' . ($donation['id'] ?? spl_object_id((object)[]));
                }
            }

            return [
                'id' => $donation['id'] ?? null,
                'amount' => (float)($donation['amount'] ?? 0),
                'donation_type' => strtolower((string)($donation['donation_type'] ?? 'one_time')),
                'pack_id' => $donation['pack_id'] ?? null,
                'donor_key' => $donorKey,
                'created_at' => $createdAt,
                'donor_email' => $donation['donor_email'] ?? '',
                'donor_name' => $name !== '' ? $name : 'Anonyme',
            ];
        }, $donationsRaw);

        $totalRevenue = array_sum(array_column($donations, 'amount'));
        $donationCount = count($donations);
        $amounts = array_column($donations, 'amount');

        $medianDonation = 0.0;
        if ($donationCount > 0) {
            sort($amounts, SORT_NUMERIC);
            $middleIndex = (int) floor(($donationCount - 1) / 2);
            if ($donationCount % 2 === 0) {
                $medianDonation = ($amounts[$middleIndex] + $amounts[$middleIndex + 1]) / 2;
            } else {
                $medianDonation = $amounts[$middleIndex];
            }
        }

        $averageDonation = $donationCount > 0 ? $totalRevenue / $donationCount : 0.0;
        $highestDonation = $donationCount > 0 ? max($amounts) : 0.0;

        $recentDonations = array_filter($donations, static fn (array $donation) => $donation['created_at'] >= $rangeStart);
        $previousDonations = array_filter($donations, static fn (array $donation) => $donation['created_at'] >= $previousStart && $donation['created_at'] < $rangeStart);

        $totalRecent = array_sum(array_map(static fn (array $donation) => $donation['amount'], $recentDonations));
        $totalPrevious = array_sum(array_map(static fn (array $donation) => $donation['amount'], $previousDonations));

        $revenueTrend = 0;
        if ($totalPrevious > 0) {
            $revenueTrend = (int) round((($totalRecent - $totalPrevious) / $totalPrevious) * 100);
        } elseif ($totalRecent > 0) {
            $revenueTrend = 100;
        }

        $firstDonationByDonor = [];
        $donationsByDonor = [];
        foreach ($donations as $donation) {
            $donorKey = $donation['donor_key'];
            $donationsByDonor[$donorKey][] = $donation;
            if (!isset($firstDonationByDonor[$donorKey]) || $donation['created_at'] < $firstDonationByDonor[$donorKey]) {
                $firstDonationByDonor[$donorKey] = $donation['created_at'];
            }
        }

        $newDonors = 0;
        $previousNewDonors = 0;
        foreach ($firstDonationByDonor as $firstDonation) {
            if ($firstDonation >= $rangeStart) {
                $newDonors++;
            } elseif ($firstDonation >= $previousStart && $firstDonation < $rangeStart) {
                $previousNewDonors++;
            }
        }

        $donorsTrend = 0;
        if ($previousNewDonors > 0) {
            $donorsTrend = (int) round((($newDonors - $previousNewDonors) / $previousNewDonors) * 100);
        } elseif ($newDonors > 0) {
            $donorsTrend = 100;
        }

        $regularDonors = 0;
        $lifetimeMonths = [];
        foreach ($donationsByDonor as $donorDonations) {
            usort($donorDonations, static fn ($a, $b) => $a['created_at'] <=> $b['created_at']);
            if (count($donorDonations) > 1) {
                $regularDonors++;
            }

            $first = $donorDonations[0]['created_at'];
            $last = end($donorDonations)['created_at'];
            $diff = $last->diff($first);
            $months = $diff->days / 30;
            $lifetimeMonths[] = round($months, 1);
        }

        $totalDonors = count($donationsByDonor);
        $retentionRate = $totalDonors > 0 ? round(($regularDonors / $totalDonors) * 100, 1) : 0.0;
        $averageLifetime = !empty($lifetimeMonths) ? round(array_sum($lifetimeMonths) / count($lifetimeMonths), 1) : 0.0;

        $ranges = [
            ['label' => '0€ - 10€', 'min' => 0, 'max' => 10],
            ['label' => '10€ - 25€', 'min' => 10, 'max' => 25],
            ['label' => '25€ - 50€', 'min' => 25, 'max' => 50],
            ['label' => '50€ - 100€', 'min' => 50, 'max' => 100],
            ['label' => '100€ et +', 'min' => 100, 'max' => null],
        ];
        $distributionCounts = array_fill(0, count($ranges), 0);
        foreach ($amounts as $amount) {
            foreach ($ranges as $index => $range) {
                $min = $range['min'];
                $max = $range['max'];
                $inRange = $max === null ? $amount >= $min : ($amount >= $min && $amount < $max);
                if ($inRange) {
                    $distributionCounts[$index]++;
                    break;
                }
            }
        }

        $monthsMap = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthPoint = $now->sub(new DateInterval('P' . $i . 'M'));
            $key = $monthPoint->format('Y-m');
            $monthsMap[$key] = [
                'label' => $monthPoint->format('m/Y'),
                'value' => 0.0,
            ];
        }

        foreach ($donations as $donation) {
            $monthKey = $donation['created_at']->format('Y-m');
            if (isset($monthsMap[$monthKey])) {
                $monthsMap[$monthKey]['value'] += $donation['amount'];
            }
        }

        $donorsByMonth = array_fill_keys(array_keys($monthsMap), 0);
        foreach ($firstDonationByDonor as $firstDonation) {
            $monthKey = $firstDonation->format('Y-m');
            if (isset($donorsByMonth[$monthKey])) {
                $donorsByMonth[$monthKey]++;
            }
        }

        $donorMonths = [];
        foreach ($donationsByDonor as $donorKey => $donorDonations) {
            foreach ($donorDonations as $donation) {
                $monthKey = $donation['created_at']->format('Y-m');
                $donorMonths[$monthKey][$donorKey] = true;
            }
        }

        $monthKeys = array_keys($monthsMap);
        $retentionLabels = [];
        $retentionRates = [];
        foreach ($monthKeys as $index => $monthKey) {
            $retentionLabels[] = $monthsMap[$monthKey]['label'];
            $activeDonors = isset($donorMonths[$monthKey]) ? count($donorMonths[$monthKey]) : 0;
            $returning = 0;
            if ($index > 0 && $activeDonors > 0) {
                $previousKey = $monthKeys[$index - 1];
                if (isset($donorMonths[$previousKey])) {
                    $currentDonors = array_keys($donorMonths[$monthKey]);
                    $previousDonors = array_keys($donorMonths[$previousKey]);
                    $returning = count(array_intersect($currentDonors, $previousDonors));
                }
            }
            $retentionRates[] = $activeDonors > 0 ? round(($returning / $activeDonors) * 100, 1) : 0.0;
        }

        $packs = $this->packRepo->getPacksByCreator($creatorId);
        $packStats = [];
        $bestRevenue = 0.0;
        foreach ($packs as $pack) {
            $packDonations = array_filter($donations, static function (array $donation) use ($pack) {
                if (!empty($donation['pack_id'])) {
                    return (int) $donation['pack_id'] === (int) $pack['id'];
                }

                return $donation['donation_type'] === 'monthly'
                    && isset($pack['price'])
                    && abs($donation['amount'] - (float) $pack['price']) < 0.01;
            });

            $uniquePackDonors = [];
            $currentMonthKey = $now->format('Y-m');
            $previousMonthKey = $now->sub(new DateInterval('P1M'))->format('Y-m');
            $currentMonthRevenue = 0.0;
            $previousMonthRevenue = 0.0;

            foreach ($packDonations as $packDonation) {
                $donorKey = $packDonation['donor_key'];
                $uniquePackDonors[$donorKey] = ($uniquePackDonors[$donorKey] ?? 0) + 1;
                $donationMonth = $packDonation['created_at']->format('Y-m');
                if ($donationMonth === $currentMonthKey) {
                    $currentMonthRevenue += $packDonation['amount'];
                }
                if ($donationMonth === $previousMonthKey) {
                    $previousMonthRevenue += $packDonation['amount'];
                }
            }

            $subscribers = count($uniquePackDonors);
            $monthlyRevenue = array_sum(array_map(static fn (array $donation) => $donation['amount'], $packDonations));
            $regularForPack = count(array_filter($uniquePackDonors, static fn (int $count) => $count > 1));
            $packRetention = $subscribers > 0 ? round(($regularForPack / $subscribers) * 100, 1) : 0.0;
            $growth = 0;
            if ($previousMonthRevenue > 0) {
                $growth = (int) round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100);
            } elseif ($currentMonthRevenue > 0) {
                $growth = 100;
            }

            $packStats[] = [
                'name' => $pack['name'],
                'price' => (float) ($pack['price'] ?? 0),
                'subscribers' => $subscribers,
                'monthly_revenue' => round($monthlyRevenue, 2),
                'retention_rate' => $packRetention,
                'growth' => $growth,
                'is_best_performer' => false,
            ];

            $bestRevenue = max($bestRevenue, $monthlyRevenue);
        }

        if ($bestRevenue > 0) {
            foreach ($packStats as &$packStat) {
                if (abs($packStat['monthly_revenue'] - round($bestRevenue, 2)) < 0.01) {
                    $packStat['is_best_performer'] = true;
                }
            }
            unset($packStat);
        }

        $stats = [
            'total_revenue' => round($totalRevenue, 2),
            'revenue_trend' => $revenueTrend,
            'new_donors' => $newDonors,
            'donors_trend' => $donorsTrend,
            'median_donation' => round($medianDonation, 2),
            'average_donation' => round($averageDonation, 2),
            'highest_donation' => round($highestDonation, 2),
            'retention_rate' => $retentionRate,
            'regular_donors' => $regularDonors,
            'average_donor_lifetime' => $averageLifetime,
            'distribution_data' => [
                'ranges' => array_column($ranges, 'label'),
                'counts' => $distributionCounts,
            ],
            'revenue_data' => [
                'labels' => array_values(array_column($monthsMap, 'label')),
                'values' => array_values(array_map(static fn (array $month) => round($month['value'], 2), $monthsMap)),
            ],
            'donors_data' => [
                'labels' => array_column($monthsMap, 'label'),
                'values' => array_values($donorsByMonth),
            ],
            'retention_data' => [
                'months' => $retentionLabels,
                'rates' => $retentionRates,
            ],
            'packs_performance' => $packStats,
        ];

        $this->render('creator/stats', [
            'creator' => $this->creator,
            'stats' => $stats,
            'pageTitle' => 'Statistiques détaillées',
        ], 'creator_dashboard');
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
