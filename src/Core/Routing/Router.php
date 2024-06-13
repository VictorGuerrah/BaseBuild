<?php

namespace App\Core\Routing;

class Router
{
    private static Router $instance;
    private static array $action = [];
    private static array $routes = [];
    private static int $currentLevel = 0;

    public static function getInstance(): Router
    {
        if (empty(self::$instance)) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    public static function prefix(string $prefix): Router
    {
        self::$action[self::$currentLevel]['prefix'] = trim($prefix);
        return self::getInstance();
    }

    public static function getPrefix(): string
    {
        $prefix = '';
        foreach(self::$action as $action) {
            if (!empty($action['prefix'])) {
                $prefix .= isset($action['prefix']) ? '/' . $action['prefix'] : '';
            }
        }
        return $prefix;
    }

    public static function register(string $method, string $uri, array $action): Router
    {
        if (empty($action) || !is_array($action)) {
            throw new \Exception("Error Processing Request"); 
        }

        $uri = self::getPrefix() . '/' . $uri;
        $uri = ltrim($uri, '/');
        $uri = rtrim($uri, '/');
        self::$routes[$uri] = new Route($method, $uri, $action[0], $action[1]);
        return self::$routes[$uri];
    }

    public static function post(string $uri, array $action): Router
    {
        return self::register('POST', $uri, $action);
    }

    public static function group(callable $routes): void
    {
        self::$currentLevel++;

        call_user_func($routes);

        self::$currentLevel--;

        unset(self::$action[self::$currentLevel]);
    }
}