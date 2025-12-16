<?php

namespace App\Controllers\Api;

class TestController extends BaseApiController
{
    public function ping()
    {
        $this->jsonResponse(['message' => 'pong']);
    }
}
