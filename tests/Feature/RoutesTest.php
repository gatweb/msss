<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Controllers\AuthController;
use App\Controllers\PublicController;
use App\Controllers\AiToolsController;
use App\Controllers\DashboardController;
use App\Controllers\DonationsController;
use App\Core\Router;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RoutesTest extends TestCase
{
    private function loadRoutes(): Router
    {
        $router = new Router();
        require APP_PATH . '/routes.php';

        return $router;
    }

    private function getRoutesForMethod(Router $router, string $method): array
    {
        $reflection = new ReflectionClass($router);
        $property = $reflection->getProperty('routes');
        $property->setAccessible(true);

        $routes = $property->getValue($router);

        return $routes[$method] ?? [];
    }

    public function testPublicAndAuthenticationRoutesAreRegistered(): void
    {
        $router = $this->loadRoutes();
        $getRoutes = $this->getRoutesForMethod($router, 'GET');
        $postRoutes = $this->getRoutesForMethod($router, 'POST');

        self::assertArrayHasKey('/', $getRoutes);
        self::assertSame([PublicController::class, 'index'], $getRoutes['/']);

        self::assertArrayHasKey('/login', $getRoutes);
        self::assertSame([AuthController::class, 'loginForm'], $getRoutes['/login']);

        self::assertArrayHasKey('/login', $postRoutes);
        self::assertSame([AuthController::class, 'login'], $postRoutes['/login']);

        self::assertArrayHasKey('/register', $getRoutes);
        self::assertSame([AuthController::class, 'registerForm'], $getRoutes['/register']);
    }

    public function testApiRoutesAreRegisteredWithCorrectControllers(): void
    {
        $router = $this->loadRoutes();
        $postRoutes = $this->getRoutesForMethod($router, 'POST');
        $getRoutes = $this->getRoutesForMethod($router, 'GET');

        self::assertArrayHasKey('/api/ai/enhance-text', $postRoutes);
        self::assertSame([AiToolsController::class, 'enhanceText'], $postRoutes['/api/ai/enhance-text']);

        self::assertArrayHasKey('/api/ai/suggest-reply', $postRoutes);
        self::assertSame([AiToolsController::class, 'suggestReply'], $postRoutes['/api/ai/suggest-reply']);

        self::assertArrayHasKey('/api/dashboard/donations-evolution', $getRoutes);
        self::assertSame([DonationsController::class, 'getDonationsEvolution'], $getRoutes['/api/dashboard/donations-evolution']);
    }

    public function testDashboardRouteUsesDashboardController(): void
    {
        $router = $this->loadRoutes();
        $getRoutes = $this->getRoutesForMethod($router, 'GET');

        self::assertArrayHasKey('/dashboard', $getRoutes);
        self::assertSame([DashboardController::class, 'index'], $getRoutes['/dashboard']);
    }
}
