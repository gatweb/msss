<?php

namespace App\Controllers;

use App\Models\Creator;
use App\Models\Pack;

class HomeController extends BaseController {
    protected $creatorModel;
    protected $packModel;

    public function __construct() {
        parent::__construct();
        $this->creatorModel = new Creator();
        $this->packModel = new Pack();
    }

    public function index() {
        // Récupérer les créateurs actifs
        $creators = $this->creatorModel->getActiveCreators();
        
        // Récupérer les packs populaires
        $popularPacks = $this->packModel->getPopularPacks();
        
        // Rendre la vue
        $this->render('home/index', [
            'creators' => $creators,
            'popularPacks' => $popularPacks,
            'pageTitle' => 'Accueil'
        ]);
    }
}
