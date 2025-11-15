<?php

use App\Controllers\PublicController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\LinksController;
use App\Controllers\PackController;
use App\Controllers\MessagesController;
use App\Controllers\AdminController;
use App\Controllers\DonationsController;
use App\Controllers\AiToolsController;

// Routes publiques
$router->get('/', [PublicController::class, 'index']);
$router->get('/creator/:username', [PublicController::class, 'showCreator']);

// Routes d'authentification
error_log("Configuration des routes d'authentification");
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Routes du tableau de bord
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/dashboard/donators', [DashboardController::class, 'donators']);
$router->get('/dashboard/donators/profile', [DashboardController::class, 'donatorProfile']);
$router->post('/dashboard/donators/profile', [DashboardController::class, 'donatorProfile']);
$router->get('/dashboard/ai-tools', [AiToolsController::class, 'index']);
$router->get('/dashboard/stats', [DashboardController::class, 'stats']);
$router->get('/dashboard/settings', [ProfileController::class, 'settings']);

// Route API pour l'amélioration de texte
$router->addRoute('POST', '/api/ai/enhance-text', ['App\Controllers\AiToolsController', 'enhanceText']);

// NOUVELLE Route pour l'API de suggestion de réponse
$router->addRoute('POST', '/api/ai/suggest-reply', ['App\Controllers\AiToolsController', 'suggestReply']);

// Routes du profil
$router->get('/profile', [ProfileController::class, 'index']);
$router->get('/profile/messages', [MessagesController::class, 'index']);
$router->get('/profile/messages/conversation/:id', [MessagesController::class, 'showConversation']);
$router->post('/profile/messages/reply/:id', [MessagesController::class, 'reply']);

// API pour la messagerie
$router->get('/api/messages/:id', [MessagesController::class, 'getMessage']);
$router->post('/api/messages/:id/read', [MessagesController::class, 'markAsRead']);
$router->post('/api/messages/:id/archive', [MessagesController::class, 'archiveMessage']);
$router->get('/profile/edit', [ProfileController::class, 'edit']);
$router->post('/profile', [ProfileController::class, 'updateProfile']);
$router->post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
$router->post('/profile/banner', [ProfileController::class, 'updateBanner']);
$router->post('/profile/password', [ProfileController::class, 'updatePassword']);
// Routes pour la gestion des liens depuis le profil
$router->get('/profile/settings', [ProfileController::class, 'settings']);
$router->get('/profile/ia-tools', [ProfileController::class, 'iaTools']);

// Routes des liens (Refactorisées)
$router->get('/dashboard/links', [LinksController::class, 'index']); // Affiche la page de gestion des liens
$router->post('/dashboard/links/save', [LinksController::class, 'save']); // Ajoute ou met à jour un lien
$router->post('/dashboard/links/delete/:id', [LinksController::class, 'delete']); // Supprime un lien (utilisation de POST pour formulaire simple)

// Routes des packs
$router->get('/profile/packs', [PackController::class, 'index']);
$router->get('/profile/packs/create', [PackController::class, 'create']);
$router->post('/profile/packs/create', [PackController::class, 'create']);
$router->get('/profile/packs/edit/:id', [PackController::class, 'edit']);
$router->post('/profile/packs/edit/:id', [PackController::class, 'edit']);
$router->post('/profile/packs/delete/:id', [PackController::class, 'delete']);
$router->get('/profile/packs/delete/:id', [PackController::class, 'delete']);
$router->get('/profile/packs/toggle/:id', [PackController::class, 'toggle']);

// Routes d'administration
$router->get('/profile/admin', [AdminController::class, 'index']);
$router->get('/profile/admin/creators', [AdminController::class, 'creators']);
$router->post('/profile/admin/creators/toggle-status', [AdminController::class, 'toggleCreatorStatus']);
$router->post('/profile/admin/creators/delete', [AdminController::class, 'deleteCreator']);

// Routes des dons
$router->get('/dashboard/donations', [DonationsController::class, 'index']);
$router->post('/dashboard/donations/add', [DonationsController::class, 'add']);
$router->get('/creator/donations', [DashboardController::class, 'donations']);
$router->get('/api/dashboard/donation-types', [DonationsController::class, 'getDonationTypes']);
$router->get('/api/dashboard/donations-evolution', [DonationsController::class, 'getDonationsEvolution']);
$router->get('/api/donations/:id', [DonationsController::class, 'getDonationDetails']);
$router->post('/api/donations/:id/stop-timer', [DonationsController::class, 'stopTimer']);
$router->delete('/api/donations/:id', [DonationsController::class, 'delete']);
$router->get('/api/dashboard/export-donations', [DonationsController::class, 'export']);

// Route par défaut pour les erreurs 404
$router->notFound(function() {
    header('HTTP/1.1 404 Not Found');
    require_once APP_PATH . '/views/errors/404.php';
});
