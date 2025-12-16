<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Core\View;
use App\Core\Flash;
use App\Core\Csrf;
use App\Repositories\CreatorRepository;
use App\Repositories\LinkRepository;

class LinksController extends BaseController {
    private $linkRepo;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        LinkRepository $linkRepo
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->linkRepo = $linkRepo;
    }

    public function index() {
        $this->requireLogin();

        $creatorId = $this->auth->getCurrentUserId();
        if (!$creatorId) {
            $this->flash->error("Impossible de récupérer l'identifiant du créateur.");
            $this->redirect('/dashboard');
            return;
        }

        $links = $this->linkRepo->getLinksByCreator($creatorId);

        $this->render('creator/links.html.twig', [
            'pageTitle' => 'Gérer mes liens',
            'links' => $links,
            'csrf_token' => Csrf::generateToken()
        ], 'creator_dashboard');
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard/links');
            return;
        }

        $this->requireLogin();

        if (!Csrf::verifyToken($_POST['csrf_token'])) {
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

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $title = trim($_POST['title'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-link');

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
            'creator_id' => $creatorId
        ];

        if ($id) {
            $link = $this->linkRepo->findById($id);
            if ($link && $link['creator_id'] == $creatorId) {
                if ($this->linkRepo->update($id, $data)) {
                    $this->flash->success('Lien mis à jour avec succès.');
                } else {
                    $this->flash->error('Erreur lors de la mise à jour du lien.');
                }
            } else {
                $this->flash->error('Tentative de modification d\'un lien non autorisé.');
            }
        } else {
            if ($this->linkRepo->create($data)) {
                $this->flash->success('Lien ajouté avec succès.');
            } else {
                $this->flash->error('Erreur lors de l\'ajout du lien.');
            }
        }

        $this->redirect('/dashboard/links');
    }

    public function delete($id) {
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard/links');
            return;
        }

        $this->requireLogin();

         if (!Csrf::verifyToken($_POST['csrf_token'])) {
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

        $deleted = $this->linkRepo->deleteByIdAndCreator($id, $creatorId);

        if ($deleted) {
            $this->flash->success('Lien supprimé avec succès.');
        } else {
            $this->flash->error('Erreur lors de la suppression du lien ou lien non trouvé/non autorisé.');
        }

        $this->redirect('/dashboard/links');
    }
}