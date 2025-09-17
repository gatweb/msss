<?php

namespace App\Controllers;

use App\Core\BaseController; // Hériter de BaseController
use App\Core\Auth;
use App\Core\View;
use App\Repositories\LinkRepository;

class LinksController extends BaseController { // Étendre BaseController
    private $linkRepo;
    protected $view;
    // Auth et Flash sont maintenant hérités de BaseController

    // Mettre à jour le constructeur pour inclure Auth et appeler le parent
    public function __construct(LinkRepository $linkRepo, View $view) {
        parent::__construct(); // Appel TRES IMPORTANT pour initialiser $this->flash et $this->auth
        $this->linkRepo = $linkRepo;
        $this->view = $view;
        // $this->auth est initialisé par parent::__construct()
    }

    // Méthode pour afficher les liens (Read)
    public function index() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login'); // Utiliser la méthode redirect de BaseController
            return;
        }

        $creatorId = $this->auth->getCurrentUserId();
        if (!$creatorId) {
            $this->flash->error("Impossible de récupérer l'identifiant du créateur.");
            $this->redirect('/dashboard'); // Rediriger vers une page sûre
            return;
        }

        $links = $this->linkRepo->getLinksByCreator($creatorId); // Assurez-vous que cette méthode existe

        $this->view->setLayout('creator_dashboard'); // Spécifier le layout
        $this->view->render('creator/links', [ // Utiliser la vue qu'on a modifiée
            'pageTitle' => 'Gérer mes liens',
            'links' => $links,
            'csrf_token' => $_SESSION['csrf_token'] ?? '' // Passer le token CSRF à la vue
        ]);
    }

    // Méthode pour ajouter ou mettre à jour un lien (Create/Update)
    // Fusionne create et update en une seule méthode save
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard/links');
            return;
        }

        if (!$this->auth->isLoggedIn()) {
            $this->flash->error("Vous devez être connecté pour gérer vos liens.");
            $this->redirect('/login');
            return;
        }

        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            $this->flash->error('Erreur de sécurité (jeton CSRF invalide).');
            $this->redirect('/dashboard/links');
            return;
        }

        $creatorId = $this->auth->getCurrentUserId();
        if (!$creatorId) {
            $this->flash->error("Impossible de récupérer l'identifiant du créateur.");
            $this->redirect('/dashboard/links');
            return;
        }

        // Récupérer les données du formulaire
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null; // ID pour l'update, null pour create
        $title = trim($_POST['title'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-link'); // Icône par défaut si vide

        // Validation simple
        if (empty($title) || empty($url)) {
            $this->flash->error('Le titre et l\'URL sont requis.');
            $this->redirect('/dashboard/links');
            return;
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->flash->error('Le format de l\'URL est invalide.');
            $this->redirect('/dashboard/links');
            return;
        }

        $data = [
            'title' => $title,
            'url' => $url,
            'icon' => $icon,
            'creator_id' => $creatorId // Ajout du creator_id pour la création
        ];

        $success = false;
        if ($id) {
            // Mise à jour
            $link = $this->linkRepo->findById($id);
            if ($link && $link['creator_id'] == $creatorId) {
                $success = $this->linkRepo->update($id, $data); // Assurez-vous que update attend $id et un tableau
                if ($success) {
                    $this->flash->success('Lien mis à jour avec succès.');
                } else {
                    $this->flash->error('Erreur lors de la mise à jour du lien.');
                }
            } else {
                $this->flash->error('Tentative de modification d\'un lien non autorisé.');
            }
        } else {
            // Création
            // 'creator_id' est déjà dans $data
            $newLinkId = $this->linkRepo->create($data); // Assurez-vous que create attend un tableau et retourne l'ID ou true
            if ($newLinkId) {
                $success = true;
                $this->flash->success('Lien ajouté avec succès.');
            } else {
                $this->flash->error('Erreur lors de l\'ajout du lien.');
            }
        }

        $this->redirect('/dashboard/links');
    }


    // Méthode pour supprimer un lien (Delete)
    // Utilise POST pour la simplicité avec les formulaires HTML
    public function delete($id) {
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard/links');
            return;
        }

        if (!$this->auth->isLoggedIn()) {
            $this->flash->error("Vous devez être connecté pour supprimer un lien.");
             $this->redirect('/login');
            return;
        }

         // Vérifier le token CSRF (attendu dans le corps POST pour delete)
         if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
             $this->flash->error('Erreur de sécurité (jeton CSRF invalide).');
             $this->redirect('/dashboard/links');
             return;
         }

        $creatorId = $this->auth->getCurrentUserId();
         if (!$creatorId) {
             $this->flash->error("Impossible de récupérer l'identifiant du créateur.");
             $this->redirect('/dashboard/links');
             return;
         }

        // Utiliser la méthode deleteByIdAndCreator pour la sécurité
        $deleted = $this->linkRepo->deleteByIdAndCreator($id, $creatorId);

        if ($deleted) {
            $this->flash->success('Lien supprimé avec succès.');
        } else {
            $this->flash->error('Erreur lors de la suppression du lien ou lien non trouvé/non autorisé.');
        }

        $this->redirect('/dashboard/links');
    }

    // Méthode utilitaire de redirection (si non présente dans BaseController)
    // Commentez ou supprimez ceci si BaseController a déjà une méthode redirect
    /*
    private function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    */
}