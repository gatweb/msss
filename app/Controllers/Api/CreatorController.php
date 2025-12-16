<?php

namespace App\Controllers\Api;

use App\Repositories\CreatorRepository;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;

class CreatorController extends BaseApiController
{
    private $creatorRepository;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->creatorRepository = $creatorRepository;
    }

    public function index()
    {
        $creators = $this->creatorRepository->getAllCreators();
        $this->jsonResponse($creators);
    }

    public function show($id)
    {
        $creator = $this->creatorRepository->findById($id);
        if ($creator) {
            $this->jsonResponse($creator);
        } else {
            $this->jsonError('Creator not found', 404);
        }
    }
}
