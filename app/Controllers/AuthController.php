<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Creator;
use App\Core\Csrf;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;

class AuthController extends BaseController {
    private $creatorModel;

    public function __construct(View $view, Auth $auth, Flash $flash, CreatorRepository $creatorRepository, Creator $creatorModel) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->creatorModel = $creatorModel;
    }

    public function loginForm() {
        // Rediriger si déjà connecté
        if (isset($_SESSION['creator_id'])) {
            $this->redirect('/dashboard');
        }

        $old = $_SESSION['old'] ?? [];
        if (isset($_SESSION['old'])) {
            unset($_SESSION['old']);
        }

        $this->view->setTitle('Connexion');
        $this->view->addScript('/assets/js/auth.js');

        $this->render('auth/login.html.twig', [
            'csrf_token' => Csrf::generateToken(),
            'old' => $old
        ], 'auth');
    }

    public function login() {
        if (!Csrf::verifyToken($_POST['csrf_token'])) {
            $this->flash->error("Session expirée. Veuillez réessayer.");
            $this->redirect('/login');
            return;
        }

        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            // Validation basique
            if (empty($email) || empty($password)) {
                $this->flash->error("Tous les champs sont requis");
                $_SESSION['old']['email'] = $email;
                $this->redirect('/login');
                return;
            }

            // Vérification des identifiants
            $creator = $this->creatorModel->getCreatorByEmail($email);
            
            if (!$creator || !password_verify($password, $creator['password'])) {
                $this->flash->error("Email ou mot de passe incorrect");
                $_SESSION['old']['email'] = $email;
                $this->redirect('/login');
                return;
            }
            
            // Vérification du statut du compte
            if (!$creator['is_active']) {
                $this->flash->error("Votre compte n'est pas actif. Veuillez vérifier votre email ou contacter le support.");
                $_SESSION['old']['email'] = $email;
                $this->redirect('/login');
                return;
            }

            // Connexion réussie
            $_SESSION['user_id'] = $creator['id'];
            $_SESSION['username'] = $creator['name'];
            $_SESSION['is_admin'] = $creator['is_admin'];
            $_SESSION['creator_id'] = $creator['id'];
            $_SESSION['creator_name'] = $creator['name'];
            $_SESSION['creator_username'] = $creator['username'] ?? null;
            $_SESSION['creator_is_admin'] = $creator['is_admin'];
            $_SESSION['initialized'] = true;

            // Gestion du "Se souvenir de moi"
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $this->creatorModel->setRememberToken($creator['id'], $token);
                
                setcookie(
                    'remember_token',
                    $token,
                    [
                        'expires' => time() + (30 * 24 * 60 * 60), // 30 jours
                        'path' => '/',
                        'domain' => '',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );
            }

            if (!empty($creator['is_admin'])) {
                $this->redirect('/profile/admin');
            } else {
                $this->redirect('/dashboard');
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de la connexion : " . $e->getMessage());
            $this->flash->error("Une erreur est survenue. Veuillez réessayer.");
            $_SESSION['old']['email'] = $email;
            $this->redirect('/login');
        }
    }

    public function registerForm() {
        if (isset($_SESSION['creator_id'])) {
            if (!empty($_SESSION['creator_is_admin'])) {
                $this->redirect('/profile/admin');
            } else {
                $this->redirect('/dashboard');
            }
        }
        
        $this->view->setTitle('Inscription');
        $this->view->addScript('/assets/js/auth.js');
        $old = $_SESSION['old'] ?? [];
        if (isset($_SESSION['old'])) {
            unset($_SESSION['old']);
        }
        $this->render('auth/register.html.twig', [
            'csrf_token' => Csrf::generateToken(),
            'old' => $old
        ], 'auth');
    }

    public function register() {
        if (!Csrf::verifyToken($_POST['csrf_token'])) {
            $this->flash->error("Session expirée. Veuillez réessayer.");
            $this->redirect('/register');
            return;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $_SESSION['old'] = ['name' => $name, 'email' => $email];

        // Validation basique
        if (empty($name) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $this->flash->error("Tous les champs sont requis");
            $this->redirect('/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash->error("Email invalide");
            $this->redirect('/register');
            return;
        }

        if (strlen($password) < 8) {
            $this->flash->error("Le mot de passe doit faire au moins 8 caractères");
            $this->redirect('/register');
            return;
        }

        if ($password !== $passwordConfirm) {
            $this->flash->error("Les mots de passe ne correspondent pas");
            $this->redirect('/register');
            return;
        }

        // Vérifier si l'email existe déjà
        if ($this->creatorModel->getCreatorByEmail($email)) {
            $this->flash->error("Cet email est déjà utilisé");
            $this->redirect('/register');
            return;
        }

        // Création du compte
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $verificationToken = bin2hex(random_bytes(32));

        $creator = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'verification_token' => $verificationToken,
            'is_active' => false
        ];

        if ($this->creatorModel->createCreator($creator)) {
            // Envoyer l'email de vérification
            $this->sendVerificationEmail($email, $verificationToken);
            
            unset($_SESSION['old']);
            $this->flash->success("Votre compte a été créé. Veuillez vérifier votre email pour l'activer.");
            $this->redirect('/login');
        } else {
            $this->flash->error("Une erreur est survenue lors de la création du compte");
            $this->redirect('/register');
        }
    }

    public function verify() {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $_SESSION['error'] = "Token invalide";
            header('Location: /login');
            exit;
        }

        $creator = $this->creatorModel->getCreatorByVerificationToken($token);

        if (!$creator) {
            $_SESSION['error'] = "Token invalide ou expiré";
            header('Location: /login');
            exit;
        }

        if ($this->creatorModel->activateCreator($creator['id'])) {
            $_SESSION['success'] = "Votre compte a été activé. Vous pouvez maintenant vous connecter.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de l'activation du compte";
        }

        header('Location: /login');
        exit;
    }

    public function forgotPasswordForm() {
        $old = $_SESSION['old'] ?? [];
        if (isset($_SESSION['old'])) {
            unset($_SESSION['old']);
        }
        $this->render('auth/forgot-password.html.twig', [
            'csrf_token' => Csrf::generateToken(),
            'old' => $old
        ], 'auth');
    }

    public function forgotPassword() {
        if (!Csrf::verifyToken($_POST['csrf_token'])) {
            $this->flash->error("Session expirée. Veuillez réessayer.");
            $this->redirect('/forgot-password');
            return;
        }

        $email = $_POST['email'] ?? '';
        $_SESSION['old']['email'] = $email;

        if (empty($email)) {
            $this->flash->error("L'email est requis");
            $this->redirect('/forgot-password');
            return;
        }

        $creator = $this->creatorModel->getCreatorByEmail($email);

        if (!$creator) {
            // Ne pas indiquer si l'email existe ou non
            $this->flash->success("Si votre email est enregistré, vous recevrez un lien de réinitialisation.");
            $this->redirect('/login');
            return;
        }

        $resetToken = bin2hex(random_bytes(32));
        $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        if ($this->creatorModel->setResetToken($creator['id'], $resetToken, $resetExpires)) {
            // Envoyer l'email de réinitialisation
            $this->sendResetEmail($email, $resetToken);
            
            unset($_SESSION['old']);
            $this->flash->success("Un email de réinitialisation vous a été envoyé.");
        } else {
            $this->flash->error("Une erreur est survenue. Veuillez réessayer.");
        }

        $this->redirect('/login');
    }

    public function resetPasswordForm() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->flash->error("Token invalide ou manquant.");
            $this->redirect('/login');
            return;
        }

        $this->render('auth/reset-password.html.twig', [
            'token' => $token,
            'csrf_token' => Csrf::generateToken()
        ], 'auth');
    }

    public function resetPassword() {
        if (!Csrf::verifyToken($_POST['csrf_token'])) {
            $this->flash->error("Session expirée. Veuillez réessayer.");
            $this->redirect('/login');
            return;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($token)) {
            $this->flash->error("Token invalide");
            $this->redirect('/login');
            return;
        }

        $creator = $this->creatorModel->getCreatorByResetToken($token);

        if (!$creator) {
            $this->flash->error("Token invalide ou expiré");
            $this->redirect('/login');
            return;
        }

        if (empty($password) || strlen($password) < 8) {
            $this->flash->error("Le mot de passe doit faire au moins 8 caractères");
            $this->redirect("/reset-password?token=$token");
            return;
        }

        if ($password !== $passwordConfirm) {
            $this->flash->error("Les mots de passe ne correspondent pas");
            $this->redirect("/reset-password?token=$token");
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($this->creatorModel->updatePassword($creator['id'], $hashedPassword)) {
            // Invalider le token de réinitialisation
            $this->creatorModel->clearResetToken($creator['id']);
            
            $this->flash->success("Votre mot de passe a été mis à jour. Vous pouvez maintenant vous connecter.");
        } else {
            $this->flash->error("Une erreur est survenue lors de la mise à jour du mot de passe");
        }

        $this->redirect('/login');
    }

    public function logout() {
        // Supprimer le cookie "Se souvenir de moi"
        if (isset($_COOKIE['remember_token'])) {
            $this->creatorModel->clearRememberToken($_SESSION['creator_id']);
            
            setcookie(
                'remember_token',
                '',
                [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );
        }

        // Détruire la session
        session_destroy();
        
        header('Location: /login');
        exit;
    }

    private function sendVerificationEmail($email, $token) {
        $to = $email;
        $subject = "Vérification de votre compte";
        
        $message = "Bonjour,\n\n";
        $message .= "Merci de vous être inscrit. Pour activer votre compte, veuillez cliquer sur le lien suivant :\n\n";
        $message .= "https://" . $_SERVER['HTTP_HOST'] . "/verify?token=" . $token . "\n\n";
        $message .= "Ce lien est valable pendant 24 heures.\n\n";
        $message .= "Si vous n'avez pas créé de compte, vous pouvez ignorer cet email.\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe " . SITE_NAME;
        
        $headers = "From: " . MAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        mail($to, $subject, $message, $headers);
    }

    private function sendResetEmail($email, $token) {
        $to = $email;
        $subject = "Réinitialisation de votre mot de passe";
        
        $message = "Bonjour,\n\n";
        $message .= "Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien suivant pour procéder :\n\n";
        $message .= "https://" . $_SERVER['HTTP_HOST'] . "/reset-password?token=" . $token . "\n\n";
        $message .= "Ce lien est valable pendant 1 heure.\n\n";
        $message .= "Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe " . SITE_NAME;
        
        $headers = "From: " . MAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        mail($to, $subject, $message, $headers);
    }
}
