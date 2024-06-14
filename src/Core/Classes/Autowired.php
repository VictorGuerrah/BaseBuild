<?php

namespace App\Core\Classes;

use ReflectionClass;
use Exception;
use PDO;

class Autowired
{
    private string $myClass;
    private object $instance;
    private ReflectionClass $reflectionClass;

    public function __construct(string $myClass)
    {
        $this->myClass = $myClass;
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

        return $methodReflection->invokeArgs($this->instance, array_merge($dependencies, $parameters));
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
            $autowired = new self($className);
            $resolvedDependencies[$dependency->name] = $autowired->getInstance();
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

    public static function make(string $myClass): object
    {
        return (new self($myClass))->getInstance();
    }
}
