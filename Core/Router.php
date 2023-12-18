<?php

namespace Core;

use mysql_xdevapi\Exception;

class Router
{
    static protected array $routes = [], $params = [];

    static protected array $convertTypes = [
        'd' => 'int',
        '.' => 'string'
    ];
    static public function add(string $route, array $params): void
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z_]+):([^}]+)}/', '(?P<$1>$2)', $route);
        $route = "/^$route$/i";
        var_dump($route);
        static::$routes[$route] = $params;
    }
    static public function dispatch(string $uri): void
    {
        $uri = static::removeQueryVariables($uri);
        $uri = trim($uri, '/');

        if (static::match($uri)) {
            static::checkRequestMethod();

            $controller = static::getController();
            $action = static::getAction($controller);

            if ($controller->before($action, static::$params)) {
                call_user_func_array([$controller, $action], static::$params);
                $controller->after($action);
            }
        }
    }
    static protected function getAction(Controller $controller): string
    {
        $action = static::$params['action'] ?? null;

        if (!method_exists($controller, $action)) {
            throw new Exception("'$controller' doesn't contain '$action'");
        }
        unset(static::$params['action']);

        return $action;
    }
    static protected function getController(): Controller
    {
        $controller = static::$params['controller'] ?? null;
        if (!class_exists($controller)) {
            throw new \Exception("controller '$controller' doesn't exist");
        }
        unset(static::$params['controller']);

        return new $controller;
    }

    static protected function checkRequestMethod()
    {
        if (array_key_exists('method', static::$params)) {
            $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
            if ($requestMethod !== strtolower(static::$params['method'])) {
                throw new \Exception("method '$requestMethod' isn`t allowed for this route");
            }
            unset(static::$params['method']);
        }
    }

    static protected function match(string $uri)
    {
        foreach (static::$routes as $route=>$params) {
            if (preg_match($route, $uri, $matches)) {
                static::$params = static::buildParams($route, $matches, $params);
                return true;
            }
        }
        throw new \Exception("route [$uri] not found", 404);
    }

    static protected function buildParams(string $route, array $matches, array $params): array
    {
        preg_match_all('/\(\?P<[\w]+>(\\\\)?([\w\.][\+]*)\)/', $route, $types);
        $matches = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

        if (!empty($types)) {
            $lastKey = array_key_last($types);
            $step = 0;
            $types[$lastKey] = array_map(fn($value) => str_replace('+', '' , $value), $types[$lastKey]);
            foreach ($matches as $name => $match) {
                settype($match, static::$convertTypes[$types[$lastKey][$step]]);
                $params[$name] = $match;
                $step++;
            }
        }
        var_dump($types, $matches, $lastKey);


        return $params;
    }

    static protected function removeQueryVariables(string $uri): string
    {
        return preg_replace('/([\w\/\-]+)\?([\w\/=\d%*&\?]+)/i', '$1', $uri);
    }
}