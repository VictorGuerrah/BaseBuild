<?php

namespace App\Core\Classes;

use App\Interfaces\ContainerInterface;

use ReflectionClass;
use Exception;

class Container implements ContainerInterface
{
    private array $bindings = [];

    public function bind(string $abstract, callable | string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function get(string $class): mixed
    {
        if ($this->has($class)) {
            $concrete = $this->bindings[$class];

            if (is_callable($concrete)) {
                return $concrete($this);
            }

            return new $concrete();
        }

        throw new Exception("Class $class not found in container.");
    }

    public function has(string $class): bool
    {
        return isset($this->bindings[$class]);
    }

    public function autoRegister(string $namespace): void
    {
        $files = glob(__DIR__ . "/$namespace/*.php");

        foreach ($files as $file) {
            $className = basename($file, '.php');
            $fullClassName = "$namespace\\$className";

            if (class_exists($fullClassName)) {
                $reflectionClass = new ReflectionClass($fullClassName);

                if (!$reflectionClass->isAbstract() && !$reflectionClass->isInterface()) {
                    $constructor = $reflectionClass->getConstructor();

                    if ($constructor) {
                        $dependencies = [];
                        foreach ($constructor->getParameters() as $parameter) {
                            $dependencyType = $parameter->getType();

                            if ($dependencyType && !$dependencyType->isBuiltin()) {
                                $dependencyClassName = $dependencyType->getName();
                                $dependencies[] = $this->get($dependencyClassName);
                            } else {
                                throw new Exception("Cannot resolve parameter {$parameter->getName()} in class $fullClassName.");
                            }
                        }

                        $this->bind($fullClassName, function () use ($reflectionClass, $dependencies) {
                            return $reflectionClass->newInstanceArgs($dependencies);
                        });
                    } else {
                        $this->bind($fullClassName, $fullClassName);
                    }
                }
            }
        }
    }
}

