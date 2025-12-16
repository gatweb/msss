<?php

namespace App\Controllers\Api;

use App\Repositories\DonationRepository;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;

class DonationController extends BaseApiController
{
    private $donationRepository;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        DonationRepository $donationRepository
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->donationRepository = $donationRepository;
    }

    public function index()
    {
        $donations = $this->donationRepository->getAllDonations();
        $this->jsonResponse($donations);
    }

    public function show($id)
    {
        $donation = $this->donationRepository->findById($id);
        if ($donation) {
            $this->jsonResponse($donation);
        } else {
            $this->jsonError('Donation not found', 404);
        }
    }
}
