<?php

namespace App\Http\Middlewares;

use App\Core\Auth;
use App\Core\Flash;

class AuthMiddleware
{
    private $auth;
    private $flash;

    public function __construct(Auth $auth, Flash $flash)
    {
        $this->auth = $auth;
        $this->flash = $flash;
    }

    public function handle()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->flash->error('Vous devez être connecté pour accéder à cette page.');
            header('Location: /login');
            exit;
        }
    }
}