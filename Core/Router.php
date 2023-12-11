<?php

namespace Core;

class Router
{
    static protected array $routes = [], $params = [];
    static public function add(string $router, array $params): void
    {
        static::$routes[$router] = $params;
    }
    static public function dispatch(string $uri): void
    {

    }
}