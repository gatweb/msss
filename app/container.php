<?php

use League\Container\Container;
use League\Container\ReflectionContainer;
use App\Core\Database;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;

// Create the container
$container = new Container();

// Use reflection to automatically resolve dependencies
$container->delegate(new ReflectionContainer());

// Register Core Services
$container->add(Database::class, function () {
    return Database::getInstance();
}, true);

$container->add(PDO::class, function () {
    return Database::getInstance()->getConnection();
}, true);

$container->add(App\Core\Session::class, function () {
    return new App\Core\Session();
}, true);

$container->add(View::class, View::class);
$container->add(Auth::class, Auth::class)->addArgument(App\Core\Session::class);
$container->add(Flash::class, Flash::class)->addArgument(App\Core\Session::class);
$container->add(App\Core\Csrf::class, App\Core\Csrf::class)->addArgument(App\Core\Session::class);

use App\Models\Creator;
use App\Models\Donation;
use App\Models\Pack;
use App\Models\Media;
use App\Models\Message;

// Register Models
$models = [
    Creator::class,
    Donation::class,
    Pack::class,
    Media::class,
    Message::class,
];

foreach ($models as $model) {
    $container->add($model, $model);
}

// Register Repositories
$repositories = [
    'App\Repositories\CreatorRepository',
    'App\Repositories\DonationRepository',
    'App\Repositories\DonatorNoteRepository',
    'App\Repositories\LinkRepository',
    'App\Repositories\MessageRepository',
    'App\Repositories\PackRepository',
];

foreach ($repositories as $repo) {
    $container->add($repo, $repo)->addArgument(Database::class);
}

use App\Http\Middlewares\AuthMiddleware;
use App\Http\Middlewares\AdminMiddleware;
use App\Http\Middlewares\CreatorMiddleware;

// Register Middlewares
$container->add(AuthMiddleware::class, AuthMiddleware::class);
$container->add(AdminMiddleware::class, AdminMiddleware::class);
$container->add(CreatorMiddleware::class, CreatorMiddleware::class);

// Register Controllers
$controllers = [
    'App\Controllers\AdminController',
    'App\Controllers\AiToolsController',
    'App\Controllers\AuthController',
    'App\Controllers\DashboardController',
    'App\Controllers\DonationsController',
    'App\Controllers\HomeController',
    'App\Controllers\LinksController',
    'App\Controllers\MediaController',
    'App\Controllers\MessagesController',
    'App\Controllers\PackController',
    'App\Controllers\ProfileController',
    'App\Controllers\PublicController',
    'App\Controllers\DonorController',
    'App\Controllers\Api\TestController',
    'App\Controllers\Api\CreatorController',
    'App\Controllers\Api\DonationController',
];

// This is a simplified registration. Some controllers might need manual wiring
// if their dependencies are not straightforward class names.
// The ReflectionContainer will handle most of them.

return $container;
