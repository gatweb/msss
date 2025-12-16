<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;
use App\Repositories\PackRepository;
use App\Core\Csrf;

class PublicController extends BaseController {
    private $packRepository;
    
    public function __construct(View $view, Auth $auth, Flash $flash, CreatorRepository $creatorRepository, PackRepository $packRepository) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->packRepository = $packRepository;
    }
    
    public function index()
    {
        $creators = $this->creatorRepository->getAllCreators();
        $this->view->setTitle('Découvrez nos Créatrices');
        $this->render('public/index.html.twig', [
            'creators' => $creators,
            'auth' => $this->auth
        ]);
    }
    
    public function showCreator($username) {
        $creator = $this->creatorRepository->getCreatorByUsername($username);
        
        if (!$creator) {
            http_response_code(404);
            $this->view->setTitle('Page non trouvée');
            $this->render('errors/404.html.twig');
            return;
        }
        
        $links = $this->creatorRepository->getCreatorLinks($creator['id']);
        $packs = $this->packRepository->getPublicPacksByCreator($creator['id']);
        
        $this->view->setTitle($creator['name'] . ' - Profil Créatrice');
        $this->render('public/creator.html.twig', [
            'creator' => $creator,
            'links' => $links,
            'packs' => $packs,
            'csrf_token' => Csrf::generateToken(),
            'donation_types' => ['PayPal', 'Photo', 'Cadeau', 'Autre']
        ]);
    }
}
