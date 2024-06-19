<?php

namespace App\Core\Routing;

class Route
{
    public $class;
    public string $method;
    public string $uri;
    public ?string $action;
    public ?array $middlewares = [];

    public function __construct(string $method, string $uri, $class, $action = null, $middlewares = null)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->class = $class;
        $this->action = $action;
        $this->middlewares = $middlewares;
    }

    public function middleware(string|array $parameters)
    {
        if (is_string($parameters)) {
            $this->middlewares[] = $parameters;
        } else {
            foreach ($parameters as $item) {
                $this->middlewares[] = $item;
            }
        }
    }

    public function getMiddlewares(): ?array
    {
        return $this->middlewares;
    }
}