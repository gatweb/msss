<?php

namespace App\Http\Middlewares;

use App\Core\Auth;
use App\Core\Flash;

class CreatorMiddleware
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
        if (!$this->auth->isCreator()) {
            $this->flash->error('Accès réservé aux créateurs.');
            header('Location: /');
            exit;
        }
    }
}
