<?php

namespace App\Core\DependencyInjection;

use App\Interfaces\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use InvalidArgumentException;

class Container implements ContainerInterface
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function get(string $class): mixed
    {
        if ($this->has($class)) {
            if (isset($this->instances[$class])) {
                return $this->instances[$class];
            }

            $concrete = $this->bindings[$class];

            if (is_callable($concrete)) {
                $object = $concrete($this);
            } else {
                $object = $this->resolve($concrete);
            }

            $this->instances[$class] = $object;
            return $object;
        }

        throw new \Exception("Class $class not found in container.");
    }

    public function has(string $class): bool
    {
        return isset($this->bindings[$class]);
    }

    public function autoRegister(string $namespace): void
    {
        $directory = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        $files = glob(__DIR__ . "/$directory/*.php");

        foreach ($files as $file) {
            $className = basename($file, '.php');
            $fullClassName = "$namespace\\$className";

            if (class_exists($fullClassName)) {
                $reflectionClass = new ReflectionClass($fullClassName);

                if (!$reflectionClass->isAbstract() && !$reflectionClass->isInterface()) {
                    $constructor = $reflectionClass->getConstructor();

                    if ($constructor) {
                        $this->bind($fullClassName, function (Container $container) use ($reflectionClass, $constructor) {
                            $dependencies = array_map(
                                fn(ReflectionParameter $param) => $this->resolveParameter($param, $container),
                                $constructor->getParameters()
                            );
                            return $reflectionClass->newInstanceArgs($dependencies);
                        });
                    } else {
                        $this->bind($fullClassName, $fullClassName);
                    }
                }
            }
        }
    }

    public function resolve(string $class): object
    {
        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();

        if (!$constructor) {
            return new $class;
        }

        $dependencies = array_map(
            fn(ReflectionParameter $param) => $this->resolveParameter($param, $this),
            $constructor->getParameters()
        );

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    public function resolveParameter(ReflectionParameter $param, Container $container): mixed
    {
        $type = $param->getType();

        if (!$type) {
            throw new InvalidArgumentException("Parameter {$param->getName()} has no type hint.");
        }

        if ($type->isBuiltin()) {
            throw new InvalidArgumentException("Cannot resolve built-in parameter {$param->getName()}.");
        }

        return $container->get($type->getName());
    }
}
