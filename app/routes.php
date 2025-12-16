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
use App\Core\View;
use App\Http\Middlewares\AuthMiddleware;
use App\Http\Middlewares\CreatorMiddleware;
use App\Http\Middlewares\AdminMiddleware;

// Routes publiques
$router->get('/', [PublicController::class, 'index']);
$router->get('/creator/:username', [PublicController::class, 'showCreator']);

// Routes d'authentification
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'forgotPasswordForm']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/reset-password', [AuthController::class, 'resetPasswordForm']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);

// Routes du tableau de bord
$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/dashboard/donators', [DashboardController::class, 'donators'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/dashboard/donators/profile', [DashboardController::class, 'donatorProfile'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/dashboard/donators/profile', [DashboardController::class, 'donatorProfile'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/dashboard/ai-tools', [AiToolsController::class, 'index'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/dashboard/stats', [DashboardController::class, 'stats'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/dashboard/settings', [ProfileController::class, 'settings'], [AuthMiddleware::class, CreatorMiddleware::class]);

// Route API
$router->post('/api/ai/enhance-text', ['App\Controllers\AiToolsController', 'enhanceText'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/api/ai/suggest-reply', ['App\Controllers\AiToolsController', 'suggestReply'], [AuthMiddleware::class, CreatorMiddleware::class]);

// Routes du profil
$router->get('/profile', [ProfileController::class, 'index'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/profile/messages', [MessagesController::class, 'index'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/profile/messages/conversation/:id', [MessagesController::class, 'showConversation'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/profile/messages/reply/:id', [MessagesController::class, 'reply'], [AuthMiddleware::class, CreatorMiddleware::class]);

// API pour la messagerie
$router->get('/api/messages/:id', [MessagesController::class, 'getMessage'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/api/messages/:id/read', [MessagesController::class, 'markAsRead'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/api/messages/:id/archive', [MessagesController::class, 'archiveMessage'], [AuthMiddleware::class, CreatorMiddleware::class]);

$router->post('/profile', [ProfileController::class, 'updateProfile'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/profile/avatar', [ProfileController::class, 'updateAvatar'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/profile/banner', [ProfileController::class, 'updateBanner'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/profile/password', [ProfileController::class, 'updatePassword'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/profile/settings', [ProfileController::class, 'settings'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/profile/ia-tools', [ProfileController::class, 'iaTools'], [AuthMiddleware::class, CreatorMiddleware::class]);

// Routes des liens
$router->get('/dashboard/links', [LinksController::class, 'index'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/dashboard/links/save', [LinksController::class, 'save'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/dashboard/links/delete/:id', [LinksController::class, 'delete'], [AuthMiddleware::class, CreatorMiddleware::class]);

// Routes des packs
$router->get('/profile/packs', [PackController::class, 'index'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/profile/packs/create', [PackController::class, 'create'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/profile/packs/create', [PackController::class, 'create'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/profile/packs/edit/:id', [PackController::class, 'edit'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/profile/packs/edit/:id', [PackController::class, 'edit'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/profile/packs/delete/:id', [PackController::class, 'delete'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/profile/packs/toggle/:id', [PackController::class, 'toggle'], [AuthMiddleware::class, CreatorMiddleware::class]);

// Routes d'administration
$router->get('/profile/admin', [AdminController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/profile/admin/creators', [AdminController::class, 'creators'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/profile/admin/creators/toggle-status', [AdminController::class, 'toggleCreatorStatus'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/profile/admin/creators/delete', [AdminController::class, 'deleteCreator'], [AuthMiddleware::class, AdminMiddleware::class]);

// Routes pour les paramètres admin
$router->get('/profile/admin/settings', [AdminController::class, 'settings'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/settings/save', [AdminController::class, 'saveSettings'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/settings/test-smtp', [AdminController::class, 'testSmtp'], [AuthMiddleware::class, AdminMiddleware::class]);

// Routes pour les statistiques admin
$router->get('/admin/stats', [AdminController::class, 'stats'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/api/admin/stats', [AdminController::class, 'getStatsApi'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/api/admin/stats/export', [AdminController::class, 'exportStats'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/donations', [DonationsController::class, 'creatorIndex'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/donation/form/:creator_id', [DonationsController::class, 'form']);
$router->post('/donation/initiate', [DonationsController::class, 'initiate']);
$router->get('/donation/success', [DonationsController::class, 'success']);
$router->get('/donation/error', [DonationsController::class, 'error']);
$router->get('/donor', ['App\Controllers\DonorController', 'index'], [AuthMiddleware::class]);
$router->get('/api/ping', ['App\Controllers\Api\TestController', 'ping']);
$router->get('/api/creators', ['App\Controllers\Api\CreatorController', 'index']);
$router->get('/api/creators/:id', ['App\Controllers\Api\CreatorController', 'show']);
$router->get('/api/donations', ['App\Controllers\Api\DonationController', 'index']);
$router->get('/api/donations/:id', ['App\Controllers\Api\DonationController', 'show']);









// Routes pour les transactions admin
$router->get('/admin/transactions', [AdminController::class, 'transactions'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/transactions/view/:id', [AdminController::class, 'viewTransaction'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/transactions/anonymize', [AdminController::class, 'anonymizeTransaction'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/transactions/export-csv', [AdminController::class, 'exportTransactions'], [AuthMiddleware::class, AdminMiddleware::class]);




// Routes des dons
$router->get('/dashboard/donations', [DonationsController::class, 'index'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/donations/add', [DonationsController::class, 'publicAdd']);
$router->post('/dashboard/donations/add', [DonationsController::class, 'add'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/creator/donations', [DashboardController::class, 'donations'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/api/dashboard/donation-types', [DonationsController::class, 'getDonationTypes'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/api/dashboard/donations-evolution', [DonationsController::class, 'getDonationsEvolution'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/api/donations/:id', [DonationsController::class, 'getDonationDetails'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->post('/api/donations/:id/stop-timer', [DonationsController::class, 'stopTimer'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->delete('/api/donations/:id', [DonationsController::class, 'delete'], [AuthMiddleware::class, CreatorMiddleware::class]);
$router->get('/api/dashboard/export-donations', [DonationsController::class, 'export'], [AuthMiddleware::class, CreatorMiddleware::class]);

// Route par défaut pour les erreurs 404
$router->notFound(function() use ($container) {
    header('HTTP/1.1 404 Not Found');
    $view = $container->get(View::class);
    $view->setTitle('Page non trouvée');
    $view->render('errors/404.html.twig');
});
