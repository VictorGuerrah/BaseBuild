<?php

namespace App\Core\Classes;

use ReflectionClass;
use Exception;

class Autowired
{
    private string $myClass;
    private object $instance;
    private ReflectionClass $reflectionClass;
    private Container $container;

    public function __construct(string $myClass, Container $container)
    {
        $this->myClass = $myClass;
        $this->container = $container;
        $this->reflectionClass = new ReflectionClass($myClass);
        $this->callConstructor();
    }

    public function getInstance(): object
    {
        return $this->instance;
    }

    public function call(string $method, array $parameters = []): mixed
    {
        if ($method === '__construct') {
            throw new Exception("__construct cannot be called twice. Use getInstance() to retrieve the object.");
        }

        $methodReflection = $this->getMethod($method);
        $dependencies = $this->getDependencies($method);

        return $methodReflection->invokeArgs($this->instance, $dependencies);
    }

    protected function getMethod(string $method): \ReflectionMethod
    {
        if (!$this->reflectionClass->hasMethod($method)) {
            throw new Exception("Method $method does not exist in the class {$this->myClass}.", 404);
        }

        return $this->reflectionClass->getMethod($method);
    }

    private function callConstructor(): void
    {
        $constructor = $this->reflectionClass->getConstructor();

        if (is_null($constructor)) {
            $this->instance = $this->reflectionClass->newInstance();
        } elseif ($constructor->isProtected() && $this->reflectionClass->hasMethod('getInstance')) {
            $this->instance = $this->reflectionClass->getMethod('getInstance')->invoke(null);
        } else {
            $parameters = $this->getDependencies('__construct');
            $this->instance = $this->reflectionClass->newInstanceArgs($parameters);
        }
    }

    private function getDependencies(string $method): array
    {
        $dependencies = $this->getMethod($method)->getParameters();
        $resolvedDependencies = [];

        foreach ($dependencies as $dependency) {
            $dependencyType = $dependency->getType();

            if ($dependencyType && !$dependencyType->isBuiltin()) {
                $className = $dependencyType->getName();
                if ($this->container->has($className)) {
                    $resolvedDependencies[$dependency->name] = $this->container->get($className);
                } else {
                    $autowired = new self($className, $this->container);
                    $resolvedDependencies[$dependency->name] = $autowired->getInstance();
                }
            } elseif (isset($_REQUEST[$dependency->name])) {
                $resolvedDependencies[$dependency->name] = $_REQUEST[$dependency->name];
            } elseif ($return = $this->json($dependency->name)) {
                $resolvedDependencies[$dependency->name] = $return;
            } elseif ($return = $this->phpInput($dependency->name)) {
                $resolvedDependencies[$dependency->name] = $return;
            } else {
                $resolvedDependencies[$dependency->name] = null;
            }
        }

        return $resolvedDependencies;
    }

    private function phpInput(string $key): mixed
    {
        parse_str(file_get_contents("php://input"), $vars);
        return $vars[$key] ?? null;
    }

    private function json(string $key): mixed
    {
        $json = json_decode(file_get_contents("php://input"), true);
        return $json[$key] ?? null;
    }

    public static function make(string $myClass, Container $container): object
    {
        return (new self($myClass, $container))->getInstance();
    }
}