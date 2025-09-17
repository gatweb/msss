<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Creator;

class AuthController extends BaseController {
    private $creatorModel;

    public function __construct() {
        parent::__construct();
        $this->creatorModel = new Creator($this->pdo);
    }

    public function loginForm() {
        // Rediriger si déjà connecté
        if (isset($_SESSION['creator_id'])) {
            $this->redirect('/dashboard');
        }

        $this->view->setTitle('Connexion');
        $this->view->addScript('/assets/js/auth.js');
        $this->render('auth/login', [], 'auth');
    }

    public function login() {
        error_log("=== Début de la tentative de connexion ===");
        error_log("Méthode HTTP : " . $_SERVER['REQUEST_METHOD']);
        error_log("URI : " . $_SERVER['REQUEST_URI']);
        error_log("POST data : " . print_r($_POST, true));
        error_log("Session actuelle : " . print_r($_SESSION, true));

        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            error_log("Email fourni : " . $email);
            error_log("Remember me : " . ($remember ? 'oui' : 'non'));

            // Validation du CSRF
            if (!isset($_POST['csrf_token'])) {
                error_log("Erreur : Token CSRF manquant");
                $_SESSION['error'] = "Session expirée. Veuillez réessayer.";
                header('Location: /login');
                exit;
            }

            if (!verifyCsrfToken($_POST['csrf_token'])) {
                error_log("Erreur : Token CSRF invalide");
                error_log("Token reçu : " . $_POST['csrf_token']);
                error_log("Token attendu : " . ($_SESSION['csrf_token'] ?? 'non défini'));
                $_SESSION['error'] = "Session expirée. Veuillez réessayer.";
                header('Location: /login');
                exit;
            }

            error_log("Validation CSRF réussie");

            // Validation basique
            if (empty($email) || empty($password)) {
                error_log("Champs manquants");
                $_SESSION['error'] = "Tous les champs sont requis";
                header('Location: /login');
                exit;
            }

            // Vérification des identifiants
            error_log("Tentative de récupération du créateur avec l'email : " . $email);
            $creator = $this->creatorModel->getCreatorByEmail($email);
            error_log("Résultat de getCreatorByEmail : " . print_r($creator, true));
            
            if (!$creator) {
                error_log("Échec de l'authentification : créateur non trouvé pour l'email : " . $email);
                $_SESSION['error'] = "Email ou mot de passe incorrect";
                header('Location: /login');
                exit;
            }

            error_log("Vérification du mot de passe pour l'email : " . $email);
            error_log("Hash stocké : " . $creator['password']);
            error_log("Mot de passe fourni : " . $password);
            error_log("Tentative de vérification avec password_verify()");
            
            $isValid = password_verify($password, $creator['password']);
            error_log("Résultat de password_verify() : " . ($isValid ? 'true' : 'false'));
            
            if (!$isValid) {
                error_log("Échec de l'authentification : mot de passe incorrect pour l'email : " . $email);
                $_SESSION['error'] = "Email ou mot de passe incorrect";
                header('Location: /login');
                exit;
            }
            
            error_log("Mot de passe vérifié avec succès");

            // Vérification du statut du compte
            error_log("Statut du compte : " . ($creator['is_active'] ? 'actif' : 'inactif'));
            if (!$creator['is_active']) {
                error_log("Compte non actif pour l'email : " . $email);
                $_SESSION['error'] = "Votre compte n'est pas actif. Veuillez vérifier votre email ou contacter le support.";
                header('Location: /login');
                exit;
            }

            // Connexion réussie
            $_SESSION['user_id'] = $creator['id'];
            $_SESSION['username'] = $creator['name'];
            $_SESSION['is_admin'] = $creator['is_admin'];
            $_SESSION['creator_id'] = $creator['id'];
            $_SESSION['creator_name'] = $creator['name'];
            $_SESSION['creator_is_admin'] = $creator['is_admin'];
            $_SESSION['initialized'] = true;

            error_log("Session après connexion : " . print_r($_SESSION, true));

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
                header('Location: /profile/admin');
                exit;
            } else {
                header('Location: /dashboard');
                exit;
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de la connexion : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
            header('Location: /login');
            exit;
        }
    }

    public function registerForm() {
        if (isset($_SESSION['creator_id'])) {
            if (!empty($_SESSION['creator_is_admin'])) {
                header('Location: /profile/admin');
                exit;
            } else {
                header('Location: /dashboard');
                exit;
            }
        }
        
        $this->view->setTitle('Inscription');
        $this->render('auth/register', [], 'auth');
    }

    public function register() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validation basique
        if (empty($name) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $_SESSION['error'] = "Tous les champs sont requis";
            header('Location: /register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Email invalide";
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = "Le mot de passe doit faire au moins 8 caractères";
            header('Location: /register');
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas";
            header('Location: /register');
            exit;
        }

        // Vérifier si l'email existe déjà
        if ($this->creatorModel->getCreatorByEmail($email)) {
            $_SESSION['error'] = "Cet email est déjà utilisé";
            header('Location: /register');
            exit;
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
            
            $_SESSION['success'] = "Votre compte a été créé. Veuillez vérifier votre email pour l'activer.";
            header('Location: /login');
            exit;
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la création du compte";
            header('Location: /register');
            exit;
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

    public function forgotPassword() {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $_SESSION['error'] = "L'email est requis";
            header('Location: /forgot-password');
            exit;
        }

        $creator = $this->creatorModel->getCreatorByEmail($email);

        if (!$creator) {
            // Ne pas indiquer si l'email existe ou non
            $_SESSION['success'] = "Si votre email est enregistré, vous recevrez un lien de réinitialisation.";
            header('Location: /login');
            exit;
        }

        $resetToken = bin2hex(random_bytes(32));
        $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        if ($this->creatorModel->setResetToken($creator['id'], $resetToken, $resetExpires)) {
            // Envoyer l'email de réinitialisation
            $this->sendResetEmail($email, $resetToken);
            
            $_SESSION['success'] = "Un email de réinitialisation vous a été envoyé.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
        }

        header('Location: /login');
        exit;
    }

    public function resetPassword() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($token)) {
            $_SESSION['error'] = "Token invalide";
            header('Location: /login');
            exit;
        }

        $creator = $this->creatorModel->getCreatorByResetToken($token);

        if (!$creator) {
            $_SESSION['error'] = "Token invalide ou expiré";
            header('Location: /login');
            exit;
        }

        if (empty($password) || strlen($password) < 8) {
            $_SESSION['error'] = "Le mot de passe doit faire au moins 8 caractères";
            header("Location: /reset-password?token=$token");
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas";
            header("Location: /reset-password?token=$token");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($this->creatorModel->updatePassword($creator['id'], $hashedPassword)) {
            // Invalider le token de réinitialisation
            $this->creatorModel->clearResetToken($creator['id']);
            
            $_SESSION['success'] = "Votre mot de passe a été mis à jour. Vous pouvez maintenant vous connecter.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du mot de passe";
        }

        header('Location: /login');
        exit;
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
