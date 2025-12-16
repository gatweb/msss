<?php

namespace App\Http\Middlewares;

use App\Core\Auth;
use App\Core\Flash;

class AdminMiddleware
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
        if (!$this->auth->isAdmin()) {
            $this->flash->error('Accès réservé aux administrateurs.');
            header('Location: /');
            exit;
        }
    }
}
