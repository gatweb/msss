<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;

class DonorController extends BaseController
{
    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
    }

    public function index()
    {
        // Dummy data for now
        $stats = [
            'supported_creators' => 5,
            'total_donations' => 1234.56,
            'monthly_donations' => 50.00,
        ];

        $followedCreators = [
            [
                'id' => 1,
                'name' => 'Creator 1',
                'tagline' => 'Tagline 1',
                'profile_pic_url' => '/assets/img/default-avatar.png',
                'last_donation' => '2023-03-15',
                'total_donated' => 500,
                'active_pack' => 'Gold',
            ]
        ];

        $this->render('donor/index.html.twig', [
            'stats' => $stats,
            'followedCreators' => $followedCreators,
            'pageTitle' => 'Mon Espace Donateur'
        ], 'donor');
    }
}
