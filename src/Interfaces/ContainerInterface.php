<?php

namespace App\Interfaces;

use App\Core\Classes\Container;
use ReflectionParameter;

interface ContainerInterface
{
    public function get(string $class): mixed;
    public function has(string $class): bool;
    public function bind(string $abstract, callable|string $concrete): void;
    public function autoRegister(string $namespace): void;
    public function resolve(string $class): object;
    public function resolveParameter(ReflectionParameter $param, Container $container): mixed;
}
