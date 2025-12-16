<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;
use App\Repositories\PackRepository;
use App\Core\Csrf;

class PackController extends BaseController
{
    protected $packRepo;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        PackRepository $packRepo
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->packRepo = $packRepo;
    }

    public function index()
    {
        $this->requireCreator();

        $creatorId = $this->creator['id'];
        $packs = $this->packRepo->getPacksByCreator($creatorId);

        $this->render('creator/packs.html.twig', [
            'packs' => $packs,
            'pageTitle' => 'Mes Packs',
            'csrf_token' => Csrf::generateToken()
        ], 'creator_dashboard');
    }

    public function create()
    {
        $this->requireCreator();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::verifyToken($_POST['csrf_token'])) {
                $this->flash->error('Session invalide. Veuillez réessayer.');
                $this->redirect('/profile/packs/create');
                return;
            }

            $data = [
                'creator_id' => $this->creator['id'],
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT),
                'perks' => isset($_POST['perks']) ? (is_array($_POST['perks']) ? implode("\n", array_map('trim', $_POST['perks'])) : trim($_POST['perks'])) : '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if (empty($data['name']) || $data['price'] === false || $data['price'] <= 0) {
                $this->flash->error('Veuillez remplir les champs obligatoires (Nom, Prix > 0).');
                $this->render('creator/packs_create.html.twig', ['pageTitle' => 'Créer un Pack', 'formData' => $data, 'csrf_token' => Csrf::generateToken()], 'creator_dashboard');
                return; 
            }

            if ($this->packRepo->createPack($data)) {
                $this->flash->success('Pack créé avec succès !');
                $this->redirect('/profile/packs');
            } else {
                $this->flash->error('Erreur lors de la création du pack.');
                $this->render('creator/packs_create.html.twig', ['pageTitle' => 'Créer un Pack', 'formData' => $data, 'csrf_token' => Csrf::generateToken()], 'creator_dashboard');
            }
        } else {
            $this->render('creator/packs_create.html.twig', ['pageTitle' => 'Créer un Pack', 'csrf_token' => Csrf::generateToken()], 'creator_dashboard');
        }
    }

    public function edit($packId)
    {
        $this->requireCreator();

        $pack = $this->packRepo->findById($packId);

        if (!$pack || $pack['creator_id'] != $this->creator['id']) {
            $this->flash->error('Pack non trouvé ou accès non autorisé.');
            $this->redirect('/profile/packs');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::verifyToken($_POST['csrf_token'])) {
                $this->flash->error('Session invalide. Veuillez réessayer.');
                $this->redirect('/profile/packs/edit/' . $packId);
                return;
            }

             $data = [
                'id' => $packId,
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT),
                'perks' => isset($_POST['perks']) ? (is_array($_POST['perks']) ? implode("\n", array_map('trim', $_POST['perks'])) : trim($_POST['perks'])) : '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

             if (empty($data['name']) || $data['price'] === false || $data['price'] <= 0) {
                $this->flash->error('Veuillez remplir les champs obligatoires (Nom, Prix > 0).');
                $this->render('creator/packs_edit.html.twig', ['pageTitle' => 'Modifier le Pack', 'pack' => array_merge($pack, $data), 'csrf_token' => Csrf::generateToken()], 'creator_dashboard');
                return; 
            }
            
            $updateData = $data;
            unset($updateData['id']);

            if ($this->packRepo->updatePack($packId, $updateData)) {
                $this->flash->success('Pack mis à jour avec succès !');
                $this->redirect('/profile/packs');
            } else {
                $this->flash->error('Erreur lors de la mise à jour du pack.');
                 $this->render('creator/packs_edit.html.twig', ['pageTitle' => 'Modifier le Pack', 'pack' => array_merge($pack, $data), 'csrf_token' => Csrf::generateToken()], 'creator_dashboard');
            }

        } else {
            if (!empty($pack['perks']) && is_string($pack['perks'])) {
                $pack['perks'] = preg_split('/\r?\n/', $pack['perks']);
            } else {
                $pack['perks'] = [];
            }
            $this->render('creator/packs_edit.html.twig', ['pageTitle' => 'Modifier le Pack', 'pack' => $pack, 'csrf_token' => Csrf::generateToken()], 'creator_dashboard');
        }
    }

    public function delete($packId)
    {
        $this->requireCreator();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile/packs');
            return;
        }

        if (!Csrf::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/profile/packs');
            return;
        }

        $pack = $this->packRepo->findById($packId);
        if (!$pack || $pack['creator_id'] != $this->creator['id']) {
            $this->flash->error('Pack non trouvé ou accès non autorisé.');
            $this->redirect('/profile/packs');
            return;
        }

        if ($this->packRepo->deletePack($packId, $this->creator['id'])) {
            $this->flash->success('Pack supprimé.');
        } else {
            $this->flash->error('Erreur lors de la suppression. Aucun pack supprimé.');
        }
        $this->redirect('/profile/packs');
    }

    public function toggle($packId)
    {
        $this->requireCreator();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile/packs');
            return;
        }

        if (!Csrf::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/profile/packs');
            return;
        }

        $pack = $this->packRepo->findById($packId);
        if (!$pack || $pack['creator_id'] != $this->creator['id']) {
            $this->flash->error('Pack non trouvé ou accès non autorisé.');
            $this->redirect('/profile/packs');
            return;
        }

        $newStatus = $pack['is_active'] ? 0 : 1;
        $updated = $this->packRepo->updatePack($packId, [
            'is_active' => $newStatus
        ]);

        if ($updated) {
            $this->flash->success($newStatus ? 'Pack activé.' : 'Pack mis en pause.');
        } else {
            $this->flash->error('Erreur lors du changement de statut.');
        }
        $this->redirect('/profile/packs');
    }
}
