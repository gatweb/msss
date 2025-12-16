<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Core\Csrf;
use App\Repositories\CreatorRepository;
use App\Repositories\LinkRepository;
use App\Models\Media;

class ProfileController extends BaseController
{
    private $linkRepo;
    private $mediaModel;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        LinkRepository $linkRepo,
        Media $mediaModel
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->linkRepo = $linkRepo;
        $this->mediaModel = $mediaModel;
    }

    public function index() {
        $this->requireLogin();
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepository->findById($creatorId);
        $links = $this->linkRepo->getLinksByCreator($creatorId);
        
        $isCurrentUser = true;
        
        $this->view->addScript('/assets/js/profile.js');
        $this->render('creator/profile.html.twig', [
            'creator' => $creator,
            'links' => $links,
            'isCurrentUser' => $isCurrentUser,
            'pageTitle' => 'Mon Profil',
            'csrf_token' => Csrf::generateToken()
        ], 'creator_dashboard');
    }



    public function updateProfile() {
        $this->requireLogin();

        if (!Csrf::verifyToken($_POST['csrf_token'])) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/profile');
            return;
        }
        
        $creatorId = $this->auth->getCurrentUserId();
        
        $data = [
            'name' => trim($_POST['name']),
            'tagline' => trim($_POST['tagline']),
            'description' => trim($_POST['description']),
            'donation_goal' => floatval($_POST['donation_goal'])
        ];
        
        if (empty($data['name'])) {
            $this->flash->error("Le nom est requis.");
            header('Location: /profile');
            exit;
        }
        
        if ($this->creatorRepository->updateProfile($creatorId, $data)) {
            $this->flash->success("Profil mis à jour avec succès.");
        } else {
            $this->flash->error("Erreur lors de la mise à jour du profil.");
        }
        
        header('Location: /profile');
        exit;
    }

    public function updateAvatar() {
        $this->requireLogin();

        if (!Csrf::verifyToken($_POST['csrf_token'])) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/profile');
            return;
        }
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepository->findById($creatorId);
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $this->flash->error("Erreur lors de l'upload de l'avatar.");
            header('Location: /profile');
            exit;
        }
        
        $file = $_FILES['avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $this->flash->error("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
            header('Location: /profile');
            exit;
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('avatar_') . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/uploads/avatars/' . $filename;
        
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            if ($creator['profile_pic_url']) {
                $oldPath = __DIR__ . '/../../public' . $creator['profile_pic_url'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            if ($this->creatorRepository->updateAvatar($creatorId, '/uploads/avatars/' . $filename)) {
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
        $this->requireLogin();
        if (!Csrf::verifyToken($_POST['csrf_token'])) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/profile');
            return;
        }

        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepository->findById($creatorId);
        
        if (!isset($_FILES['banner']) || $_FILES['banner']['error'] !== UPLOAD_ERR_OK) {
            $this->flash->error("Erreur lors de l'upload de la bannière.");
            header('Location: /profile');
            exit;
        }
        
        $file = $_FILES['banner'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $this->flash->error("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
            header('Location: /profile');
            exit;
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('banner_') . '.' . $extension;
        $uploadPath = PUBLIC_PATH . '/uploads/banners/' . $filename;
        
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            if ($creator['banner_url']) {
                $oldPath = PUBLIC_PATH . $creator['banner_url'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            if ($this->creatorRepository->updateBanner($creatorId, '/uploads/banners/' . $filename)) {
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
        $this->requireLogin();
        if (!Csrf::verifyToken($_POST['csrf_token'])) {
            $this->flash->error('Session invalide. Veuillez réessayer.');
            $this->redirect('/profile');
            return;
        }

        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepository->findById($creatorId);
        
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
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        if ($this->creatorRepository->updatePassword($creatorId, $hashedPassword)) {
            $this->flash->success("Mot de passe mis à jour avec succès.");
        } else {
            $this->flash->error("Erreur lors de la mise à jour du mot de passe.");
        }
        
        header('Location: /profile');
        exit;
    }

    public function settings()
    {
        $this->requireLogin();
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepository->findById($creatorId);

        if (!$creator) {
            $this->auth->logout();
            return $this->redirect('/login?error=user_not_found');
        }

        $this->render('creator/settings.html.twig', [
            'pageTitle' => 'Paramètres',
            'creator' => (array) $creator,
            'csrf_token' => Csrf::generateToken()
        ], 'creator_dashboard');
    }

    public function iaTools()
    {
        $this->requireLogin();
        
        $creatorId = $this->auth->getCurrentUserId();
        $creator = $this->creatorRepository->findById($creatorId);

        if (!$creator) {
            $this->auth->logout();
            return $this->redirect('/login?error=user_not_found');
        }

        $this->render('creator/ia_tools.html.twig', [
            'pageTitle' => 'Outils IA',
            'creator' => (array) $creator
        ], 'creator_dashboard');
    }
}
