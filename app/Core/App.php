<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

class App
{
    private static $container;

    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    public static function make($id)
    {
        return self::$container->get($id);
    }
}
