<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Pack;
use App\Models\Creator;

use App\Repositories\PackRepository;
use App\Repositories\CreatorRepository;
use App\Core\View;

class PackController extends BaseController
{
    protected $view;
    protected $packRepo;
    protected $creatorRepo;
    protected $auth;
    protected $flash;

    public function __construct(PackRepository $packRepo, CreatorRepository $creatorRepo, $auth, $flash, View $view)
    {
        $this->packRepo = $packRepo;
        $this->creatorRepo = $creatorRepo;
        $this->auth = $auth;
        $this->flash = $flash;
        $this->view = $view;
    }

    /**
     * Affiche la liste des packs pour la créatrice connectée.
     */
    public function index()
    {
        if (!$this->auth->isLoggedIn()) {
            // Rediriger vers la connexion si non connecté
            header('Location: /login');
            exit;
        }

        $creatorId = $_SESSION['creator_id']; // Utiliser la bonne clé !
        if (!$creatorId) {
             // Gérer le cas où l'ID n'est pas dans la session
            $this->flash->error('Impossible de récupérer votre identifiant. Veuillez vous reconnecter.');
            header('Location: /login');
            exit;
        }
        
        $packs = $this->packRepo->getPacksByCreator($creatorId);

        $creator = $this->creatorRepo->findById($creatorId);
        // Charger la vue pour afficher les packs
        $this->view->render('creator/packs', [
            'packs' => $packs,
            'pageTitle' => 'Mes Packs',
            'creator' => $creator
        ], 'creator_dashboard');
    }

    /**
     * Affiche le formulaire de création de pack ou traite la soumission.
     */
    public function create()
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $creatorId = $_SESSION['creator_id']; // Utiliser la bonne clé !
        if (!$creatorId) {
            $this->flash->error('Impossible de récupérer votre identifiant. Veuillez vous reconnecter.');
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- Traitement du formulaire POST --- 
            $data = [
                'creator_id' => $_SESSION['creator_id'],
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT),
                'perks' => isset($_POST['perks']) ? (is_array($_POST['perks']) ? implode("\n", array_map('trim', $_POST['perks'])) : trim($_POST['perks'])) : '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Validation basique (à améliorer)
            if (empty($data['name']) || $data['price'] === false || $data['price'] <= 0) {
                $this->flash->error('Veuillez remplir les champs obligatoires (Nom, Prix > 0).');
                // Re-afficher le formulaire avec les erreurs et les données précédentes si nécessaire
                $this->view->render('creator/packs_create', ['pageTitle' => 'Créer un Pack', 'formData' => $data], 'dashboard');
                return; 
            }

            if ($this->packRepo->createPack($data)) {
                $this->flash->success('Pack créé avec succès !');
                header('Location: /profile/packs'); // Rediriger vers la liste des packs
                exit;
            } else {
                $this->flash->error('Erreur lors de la création du pack.');
                // Re-afficher le formulaire avec les erreurs
                $this->view->render('creator/packs_create', ['pageTitle' => 'Créer un Pack', 'formData' => $data, 'creator' => $this->creatorRepo->findById($creatorId)], 'dashboard');
                return;
            }

        } else {
            // --- Affichage du formulaire GET --- 
            $creator = $this->creatorRepo->findById($creatorId);
            $this->view->render('creator/packs_create', ['pageTitle' => 'Créer un Pack', 'creator' => $creator], 'dashboard');
        }
    }

    /**
     * Affiche le formulaire d'édition de pack ou traite la soumission.
     */
    public function edit($packId)
    {
         if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $creatorId = $_SESSION['creator_id']; // Utiliser la bonne clé !
        $pack = $this->packRepo->findById($packId);

        // Vérifier si le pack existe et appartient à la créatrice
        if (!$pack || $pack['creator_id'] != $creatorId) {
            $this->flash->error('Pack non trouvé ou accès non autorisé.');
            header('Location: /profile/packs');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- Traitement du formulaire POST --- 
             $data = [
                'id' => $packId,
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT),
                'perks' => isset($_POST['perks']) ? (is_array($_POST['perks']) ? implode("\n", array_map('trim', $_POST['perks'])) : trim($_POST['perks'])) : '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Validation
             if (empty($data['name']) || $data['price'] === false || $data['price'] <= 0) {
                $this->flash->error('Veuillez remplir les champs obligatoires (Nom, Prix > 0).');
                $this->view->render('packs/edit', ['pageTitle' => 'Modifier le Pack', 'pack' => array_merge($pack, $data)]); // Renvoyer les données modifiées
                return; 
            }
            
            // Préparer les données pour l'update (ne pas inclure 'id')
            $updateData = $data;
            unset($updateData['id']);

            if ($this->packRepo->updatePack($packId, $updateData)) {
                $this->flash->success('Pack mis à jour avec succès !');
                header('Location: /profile/packs');
                exit;
            } else {
                $this->flash->error('Erreur lors de la mise à jour du pack.');
                 $this->view->render('creator/packs_edit', ['pageTitle' => 'Modifier le Pack', 'pack' => array_merge($pack, $data)], 'dashboard');
                 return;
            }

        } else {
            // --- Affichage du formulaire GET --- 
             $creator = $this->creatorRepo->findById($creatorId);
            // Convertir perks en tableau pour affichage dans la vue
            if (!empty($pack['perks']) && is_string($pack['perks'])) {
                $pack['perks'] = preg_split('/\r?\n/', $pack['perks']);
            } else {
                $pack['perks'] = [];
            }
            $this->view->render('creator/packs_edit', ['pageTitle' => 'Modifier le Pack', 'pack' => $pack, 'creator' => $creator], 'dashboard');
        }
    }

    /**
     * Supprime un pack de façon simple et directe.
     */
    public function delete($packId)
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        $creatorId = $_SESSION['creator_id'];
        if (!$creatorId) {
            $this->flash->error('Action non autorisée.');
            header('Location: /profile/packs');
            exit;
        }
        $pack = $this->packRepo->findById($packId);
        if (!$pack || $pack['creator_id'] != $creatorId) {
            $this->flash->error('Pack non trouvé ou accès non autorisé.');
            header('Location: /profile/packs');
            exit;
        }
        $deleted = $this->packRepo->deletePack($packId, $creatorId);
        if ($deleted) {
            $this->flash->success('Pack supprimé.');
        } else {
            $this->flash->error('Erreur lors de la suppression. Aucun pack supprimé.');
        }
        header('Location: /profile/packs');
        exit;
    }

    /**
     * Active ou désactive un pack
     */
    public function toggle($packId)
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        $creatorId = $_SESSION['creator_id'];
        $pack = $this->packRepo->findById($packId);
        if (!$pack || $pack['creator_id'] != $creatorId) {
            $this->flash->error('Pack non trouvé ou accès non autorisé.');
            header('Location: /profile/packs');
            exit;
        }
        $newStatus = $pack['is_active'] ? 0 : 1;
        $updated = $this->packRepo->updatePack($packId, [
            'name' => $pack['name'],
            'description' => $pack['description'],
            'price' => $pack['price'],
            'perks' => $pack['perks'],
            'is_active' => $newStatus
        ]);
        if ($updated) {
            $this->flash->success($newStatus ? 'Pack activé.' : 'Pack mis en pause.');
        } else {
            $this->flash->error('Erreur lors du changement de statut.');
        }
        header('Location: /profile/packs');
        exit;
    }
}
