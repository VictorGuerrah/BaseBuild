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
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function prefix(string $prefix): Router
    {
        self::$action[self::$currentLevel]['prefix'] = trim($prefix, '/');
        return self::getInstance();
    }

    public static function getPrefix(): string
    {
        $prefix = '';
        foreach (self::$action as $action) {
            if (!empty($action['prefix'])) {
                $prefix .= '/' . $action['prefix'];
            }
        }
        return $prefix;
    }

    public static function register(string $method, string $uri, array $action): void
    {
        if (count($action) !== 2 || !is_string($action[0]) || !is_string($action[1])) {
            throw new \Exception("Invalid action provided for route registration.", 500);
        }

        $uri = self::getPrefix() . '/' . trim($uri, '/');
        $middlewares = self::collectMiddlewares();
        self::$routes[$uri] = new Route($method, $uri, $action[0], $action[1], $middlewares);
    }

    public static function post(string $uri, array $action): void
    {
        self::register('POST', $uri, $action);
    }

    public static function group(callable $routes): void
    {
        self::$currentLevel++;
        $routes();
        unset(self::$action[self::$currentLevel]);
        self::$currentLevel--;
    }

    public static function search(string $endpoint): ?Route
    {
        $trimmedEndpoint = ltrim($endpoint, '/');

        foreach (self::$routes as $key => $route) {
            $trimmedKey = ltrim($key, '/');

            if ($trimmedEndpoint === $trimmedKey) {
                if (strtoupper($route->method) !== $_SERVER['REQUEST_METHOD']) {
                    throw new \Exception("Method not allowed.", 403);
                }
                return $route;
            }
        }
        throw new \Exception("Route not found.", 404);
    }

    public static function middleware($middleware = null): Router
    {
        if (is_null($middleware)) {
            return self::getInstance();
        }

        if (!is_array($middleware)) {
            $middleware = [$middleware];
        }

        if (isset(self::$action[self::$currentLevel]['middlewares'])) {
            self::$action[self::$currentLevel]['middlewares'] = array_merge(
                self::$action[self::$currentLevel]['middlewares'],
                $middleware
            );
        } else {
            self::$action[self::$currentLevel]['middlewares'] = $middleware;
        }

        return self::getInstance();
    }

    private static function collectMiddlewares(): array
    {
        $middlewares = [];
        for ($level = 0; $level <= self::$currentLevel; $level++) {
            if (isset(self::$action[$level]['middlewares'])) {
                $middlewares = array_merge($middlewares, self::$action[$level]['middlewares']);
            }
        }
        return $middlewares;
    }
}
