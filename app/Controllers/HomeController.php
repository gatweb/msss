<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;
use App\Repositories\PackRepository;

class HomeController extends BaseController {
    protected $packRepository;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        PackRepository $packRepository
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->packRepository = $packRepository;
    }

    public function index() {
        $this->redirect('/');
    }
}
