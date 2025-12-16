<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\View;
use App\Core\Flash;
use App\Repositories\CreatorRepository;
use App\Repositories\DonationRepository;
use App\Repositories\DonatorNoteRepository;
use App\Repositories\LinkRepository;
use App\Repositories\PackRepository;
use DateInterval;
use DateTimeImmutable;

class DashboardController extends BaseController {
    private $donatorNoteRepo;
    private $packRepo;
    private $linkRepo;
    private $donationRepo;
    private $aiToolsController;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        PackRepository $packRepo,
        LinkRepository $linkRepo,
        DonationRepository $donationRepo,
        AiToolsController $aiToolsController,
        DonatorNoteRepository $donatorNoteRepo
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->packRepo = $packRepo;
        $this->linkRepo = $linkRepo;
        $this->donationRepo = $donationRepo;
        $this->aiToolsController = $aiToolsController;
        $this->donatorNoteRepo = $donatorNoteRepo;
    }
    
    public function index() {
        $this->requireCreator();
        
        $creatorId = $this->creator['id'];

        try {
            $creator = $this->creatorRepository->findById($creatorId);

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

            $donators = $this->donationRepo->getDonatorsByCreator($creatorId);
            foreach ($donators as &$donator) {
                $note = $this->donatorNoteRepo->getNote($creatorId, $donator['donor_email']);
                $donator['crm_status'] = isset($note['crm_status']) ? $note['crm_status'] : 'prospect';
            }
            unset($donator);

            $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $per_page = 10;
            $total_pages = max(1, ceil(count($donators) / $per_page));
            $donators_paginated = array_slice($donators, ($current_page - 1) * $per_page, $per_page);

            $pageTitle = 'Tableau de Bord';
            
            $viewData = [
                'stats' => $stats,
                'progress' => $stats['progress_percentage'],
                'totalDonations' => $stats['total_donations'],
                'donationGoal' => $stats['donation_goal'],
                'goalReached' => $stats['goal_reached'],
                'closeToGoal' => $stats['close_to_goal'],
                'remainingAmount' => $stats['remaining_amount'],
                'recentDonations' => $stats['recent_donations'],
                'donators' => $donators_paginated,
                'total_pages' => $total_pages,
                'current_page' => $current_page,
                'pageTitle' => $pageTitle
            ];

            $this->render('creator/dashboard.html.twig', $viewData, 'creator_dashboard');

        } catch (\Exception $e) {
            error_log("Erreur critique dans le dashboard : " . $e->getMessage());
            throw $e;
        }
    }

    public function stats() {
        $this->requireCreator();

        $creatorId = $this->creator['id'];
        
        // Dummy data for now
        $stats = [
            'total_revenue' => 12345.67,
            'revenue_trend' => 12,
            'new_donors' => 123,
            'donors_trend' => 5,
            'median_donation' => 25.0,
            'average_donation' => 35.50,
            'highest_donation' => 500,
            'retention_rate' => 60,
            'regular_donors' => 15,
            'average_donor_lifetime' => 6,
            'packs_performance' => [
                [
                    'name' => 'Pack 1',
                    'price' => 5,
                    'subscribers' => 50,
                    'monthly_revenue' => 250,
                    'retention_rate' => 80,
                    'growth' => 10,
                    'is_best_performer' => true,
                ]
            ],
            'revenue_data' => ['labels' => [], 'values' => []],
            'donors_data' => ['labels' => [], 'values' => []],
            'distribution_data' => ['ranges' => [], 'counts' => []],
            'retention_data' => ['months' => [], 'rates' => []],
        ];

        $this->view->addScript('https://cdn.jsdelivr.net/npm/chart.js');
        $this->render('creator/stats.html.twig', [
            'stats' => $stats,
            'pageTitle' => 'Statistiques'
        ], 'creator_dashboard');
    }
    
    public function donations() {
        $this->requireCreator();
        
        $creatorId = $this->creator['id'];

        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 10;

        $donations = $this->donationRepo->getDonationsByCreator($creatorId, 'all', $currentPage, $perPage);
        $totalItems = $this->donationRepo->getTotalDonationsCount($creatorId, 'all');
        $totalPages = max(1, ceil($totalItems / $perPage));

        $stats = [
            'total_amount' => $this->donationRepo->getTotalAmount($creatorId),
            'unique_donors' => $this->donationRepo->getUniqueDonorsCount($creatorId),
        ];

        $this->view->addScript('https://cdn.jsdelivr.net/npm/chart.js');
        $this->view->addScript('/assets/js/donations.js');

        $this->render('creator/donations.html.twig', [
            'donations' => $donations,
            'stats' => $stats,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'pageTitle' => 'Mes Dons',
        ], 'creator_dashboard');
    }


    

    


    public function donatorProfile() {
        $this->requireCreator();
        
        $creatorId = $this->creator['id'];
        $email = $_GET['email'] ?? null;
        if (!$email) {
            $this->redirect('/dashboard/donators');
            return;
        }
        
        $donator = null;
        $allDonators = $this->donationRepo->getDonatorsByCreator($creatorId);
        foreach ($allDonators as $d) {
            if ($d['donor_email'] === $email) {
                $donator = $d;
                break;
            }
        }
        if (!$donator) {
            $this->redirect('/dashboard/donators');
            return;
        }
        
        $donations = $this->donationRepo->getDonationsByCreator($creatorId);
        $donations = array_filter($donations, function($don) use ($email) {
            return $don['donor_email'] === $email;
        });

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ... (form processing logic remains the same)
        }
        
        $notes = $this->donatorNoteRepo->getNote($creatorId, $email);
        // ... (the rest of the method remains the same)

        $this->render('creator/donator_profile.html.twig', [
            'donator' => $donator,
            'donations' => $donations,
            'notes' => $notes,
            'pageTitle' => 'Fiche Donateur',
            'csrf_token' => $this->generateCsrfToken()
        ], 'dashboard');
    }


    public function donators() {
        $this->requireCreator();
        
        $creatorId = $this->creator['id'];

        $donators = $this->donationRepo->getDonatorsByCreator($creatorId);
        foreach ($donators as &$donator) {
            $note = $this->donatorNoteRepo->getNote($creatorId, $donator['donor_email']);
            $donator['crm_status'] = isset($note['crm_status']) ? $note['crm_status'] : 'prospect';
        }
        unset($donator);

        $stats = [
            'total_donators' => count($donators),
            'recurring_donators' => 0,
            'average_donation' => ($donators && count($donators) > 0) ? round(array_sum(array_column($donators, 'total_amount')) / count($donators), 2) : 0,
        ];
        
        $this->render('creator/donators.html.twig', [
            'donators' => $donators,
            'pageTitle' => 'Mes Donateurs',
            'stats' => $stats,
            'total_pages' => 1,
            'current_page' => 1
        ], 'creator_dashboard');
    }
}
