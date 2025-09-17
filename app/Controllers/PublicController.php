<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Creator;

class PublicController extends BaseController {
    private $creatorModel;
    
    public function __construct() {
        parent::__construct();
        $this->creatorModel = new Creator();
    }
    
    public function index()
    {
        $creators = $this->creatorModel->getAllCreators();
        $this->view->setTitle('Découvrez nos Créatrices');
        $this->render('public/index', [
            'creators' => $creators
        ]);
    }
    
    public function showCreator($username) {
        $creator = $this->creatorModel->getCreatorByUsername($username);
        
        if (!$creator) {
            http_response_code(404);
            $this->view->setTitle('Page non trouvée');
            $this->render('errors/404');
            return;
        }
        
        $links = $this->creatorModel->getCreatorLinks($creator['id']);
        
        $this->view->setTitle($creator['name'] . ' - Profil Créatrice');
        $this->render('public/creator', [
            'creator' => $creator,
            'links' => $links
        ]);
    }
}
