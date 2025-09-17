<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Models\Creator; // Utilisé dans les nouvelles méthodes
use App\Models\Link; // Utilisé dans les nouvelles méthodes
use App\Repositories\CreatorRepository; // Ajout pour le constructeur
use App\Repositories\LinkRepository; // Ajout pour le constructeur

class ProfileController extends BaseController
{
    protected $creatorRepo;
    protected $linkRepo;
    protected $mediaModel;
    protected $auth;
    protected $view;

    public function __construct(CreatorRepository $creatorRepo, LinkRepository $linkRepo, $mediaModel, Auth $auth, \App\Core\View $view) {
        parent::__construct(); // Appel du constructeur parent
        $this->creatorRepo = $creatorRepo;
        $this->linkRepo = $linkRepo;
        $this->mediaModel = $mediaModel;
        $this->auth = $auth;
        $this->view = $view;
    }

    public function index() {
        // Vérifier l'authentification
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        // Récupérer le créateur connecté
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepo->findById($creatorId);
        $links = $this->linkRepo->getLinksByCreator($creatorId);
        
        // Déterminer si c'est le profil de l'utilisateur connecté
        $isCurrentUser = true; // Dans ce cas, c'est toujours vrai car on est sur /profile
        
        // Charger la vue
        $this->view->render('profile/index', [
            'creator' => $creator,
            'links' => $links,
            'isCurrentUser' => $isCurrentUser,
            'pageTitle' => 'Mon Profil'
        ], 'creator_dashboard');
    }

    public function edit() {
        // Vérifier l'authentification
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        // Récupérer le créateur connecté
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorModel->getCreatorById($creatorId);
        
        // Charger la vue
        $this->view->render('profile/edit', [
            'creator' => $creator,
            'pageTitle' => 'Modifier mon profil'
        ], 'user');
    }

    public function updateProfile() {
        // Vérifier l'authentification
        $this->auth->handle();
        
        // Vérifier l'authentification
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorModel->getCreatorById($creatorId);
        
        // Valider les données
        $data = [
            'name' => trim($_POST['name']),
            'tagline' => trim($_POST['tagline']),
            'description' => trim($_POST['description']),
            'donation_goal' => floatval($_POST['donation_goal'])
        ];
        
        // Valider les champs requis
        if (empty($data['name'])) {
            $this->flash->error("Le nom est requis.");
            header('Location: /profile');
            exit;
        }
        
        // Mettre à jour le profil
        if ($this->creatorRepo->updateProfile($creator['id'], $data)) {
            $this->flash->success("Profil mis à jour avec succès.");
        } else {
            $this->flash->error("Erreur lors de la mise à jour du profil.");
        }
        
        header('Location: /profile');
        exit;
    }

    public function updateAvatar() {
        // Vérifier l'authentification
        $this->auth->handle();
        
        // Vérifier l'authentification
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorModel->getCreatorById($creatorId);
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $this->flash->error("Erreur lors de l'upload de l'avatar.");
            header('Location: /profile');
            exit;
        }
        
        $file = $_FILES['avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Vérifier le type de fichier
        if (!in_array($file['type'], $allowedTypes)) {
            $this->flash->error("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
            header('Location: /profile');
            exit;
        }
        
        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('avatar_') . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/uploads/avatars/' . $filename;
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }
        
        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Supprimer l'ancien avatar s'il existe
            if ($creator['profile_pic_url']) {
                $oldPath = __DIR__ . '/../../public' . $creator['profile_pic_url'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Mettre à jour le chemin dans la base de données
            if ($this->creatorModel->updateAvatar($creator['id'], '/uploads/avatars/' . $filename)) {
                $this->flash->success("Avatar mis à jour avec succès.");
            } else {
                $this->flash->error("Erreur lors de la mise à jour de l'avatar.");
            }
        } else {
            $this->flash->error("Erreur lors de l'upload de l'avatar.");
        }
        
        header('Location: /profile');
        exit;
    }

    public function updateBanner() {
        // Vérifier l'authentification
        $this->auth->handle();
        
        // Vérifier l'authentification
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorModel->getCreatorById($creatorId);
        
        if (!isset($_FILES['banner']) || $_FILES['banner']['error'] !== UPLOAD_ERR_OK) {
            $this->flash->error("Erreur lors de l'upload de la bannière.");
            header('Location: /profile');
            exit;
        }
        
        $file = $_FILES['banner'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Vérifier le type de fichier
        if (!in_array($file['type'], $allowedTypes)) {
            $this->flash->error("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
            header('Location: /profile');
            exit;
        }
        
        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('banner_') . '.' . $extension;
        $uploadPath = PUBLIC_PATH . '/uploads/banners/' . $filename;
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }
        
        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Supprimer l'ancienne bannière s'il existe
            if ($creator['banner_url']) {
                $oldPath = PUBLIC_PATH . $creator['banner_url'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Mettre à jour le chemin dans la base de données
            if ($this->creatorModel->updateBanner($creator['id'], '/uploads/banners/' . $filename)) {
                $this->flash->success("Bannière mise à jour avec succès.");
            } else {
                $this->flash->error("Erreur lors de la mise à jour de la bannière.");
            }
        } else {
            $this->flash->error("Erreur lors de l'upload de la bannière.");
        }
        
        header('Location: /profile');
        exit;
    }

    public function updatePassword() {
        // Vérifier l'authentification
        $this->auth->handle();
        
        // Vérifier l'authentification
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorModel->getCreatorById($creatorId);
        
        // Valider les mots de passe
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (!password_verify($currentPassword, $creator['password_hash'])) {
            $this->flash->error("Mot de passe actuel incorrect.");
            header('Location: /profile');
            exit;
        }
        
        if ($newPassword !== $confirmPassword) {
            $this->flash->error("Les nouveaux mots de passe ne correspondent pas.");
            header('Location: /profile');
            exit;
        }
        
        if (strlen($newPassword) < 8) {
            $this->flash->error("Le nouveau mot de passe doit faire au moins 8 caractères.");
            header('Location: /profile');
            exit;
        }
        
        // Hasher et mettre à jour le mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        if ($this->creatorRepo->updatePassword($creator['id'], $hashedPassword)) {
            $this->flash->success("Mot de passe mis à jour avec succès.");
        } else {
            $this->flash->error("Erreur lors de la mise à jour du mot de passe.");
        }
        
        header('Location: /profile');
        exit;
    }

    /**
     * Affiche la page des paramètres du profil.
     */
    public function settings()
    {
        $this->view->setLayout('creator_dashboard'); // Définir le layout
        if (!$this->auth->isLoggedIn()) {
            return $this->redirect('/login');
        }
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepo->findById($creatorId); // Utilisation de findById

        if (!$creator) {
            error_log("Erreur: Créateur avec ID {$creatorId} non trouvé pour utilisateur connecté.");
            $this->auth->logout();
            return $this->redirect('/login?error=user_not_found');
        }

        // Logique future : Récupérer les paramètres actuels

        $this->view->render('creator/settings', [
            'pageTitle' => 'Paramètres',
            'creator' => (array) $creator, // Passer l'objet créateur trouvé
            // Passer d'autres données de paramètres si nécessaire
        ]);
    }

    /**
     * Affiche la page des outils IA.
     */
    public function iaTools()
    {
        $this->view->setLayout('creator_dashboard'); // Définir le layout
        if (!$this->auth->isLoggedIn()) {
            return $this->redirect('/login');
        }
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepo->findById($creatorId); // Utilisation de findById

        if (!$creator) {
            error_log("Erreur: Créateur avec ID {$creatorId} non trouvé pour utilisateur connecté.");
            $this->auth->logout();
            return $this->redirect('/login?error=user_not_found');
        }

        $this->view->render('creator/ia_tools', [
            'pageTitle' => 'Outils IA',
            'creator' => (array) $creator // Passer l'objet créateur trouvé
        ]);
    }

}
