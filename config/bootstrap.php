<?php

use App\Core\Classes\Container;

$container = new Container();

function registerClassesRecursively(Container $container, string $baseDir, string $namespacePrefix): void
{
    $files = glob($baseDir . '/*', GLOB_ONLYDIR | GLOB_NOSORT);

    foreach ($files as $dir) {
        $namespace = $namespacePrefix . '\\' . basename($dir);

        foreach (glob($dir . '/*.php') as $file) {
            $className = $namespace . '\\' . pathinfo($file, PATHINFO_FILENAME);
            if ($className === "App\Core\Helper\Util") {
                continue;
            }

            // Use try-catch to handle exceptions when loading class or creating reflection
            try {
                $reflectionClass = new ReflectionClass($className);

                // Check if the class implements interfaces
                $interfaces = $reflectionClass->getInterfaces();
                foreach ($interfaces as $interface) {
                    $interfaceName = $interface->getName();
                    // Bind the interface to the concrete class
                    $container->bind($interfaceName, function ($container) use ($className) {
                        return $container->get($className);
                    });
                }

                // Also bind the class itself
                $container->bind($className, function ($container) use ($className) {
                    $reflectionClass = new ReflectionClass($className);
                    $constructor = $reflectionClass->getConstructor();

                    if ($constructor === null) {
                        return new $className();
                    }

                    $parameters = [];
                    foreach ($constructor->getParameters() as $parameter) {
                        $dependencyClass = $parameter->getType();
                        if ($dependencyClass === null) {
                            throw new Exception("Cannot resolve dependency for {$parameter->getName()} in $className.");
                        }
                        $parameters[] = $container->get($dependencyClass->getName());
                    }

                    return $reflectionClass->newInstanceArgs($parameters);
                });
            } catch (ReflectionException $e) {
                // Handle exception if class cannot be loaded or reflection fails
                echo "Error loading class $className: " . $e->getMessage() . "\n";
            }
        }

        registerClassesRecursively($container, $dir, $namespace);
    }
}

$baseDir = dirname(__FILE__, 2) . '/src';
registerClassesRecursively($container, $baseDir, 'App');

return $container;
