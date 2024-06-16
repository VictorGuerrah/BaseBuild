<?php

use App\Core\Classes\Container;

$container = new Container();

function registerClassesRecursively(Container $container, string $baseDir, string $namespacePrefix): void
{
    if (!is_dir($baseDir)) {
        throw new InvalidArgumentException("Base directory $baseDir does not exist or is not a directory.");
    }

    $directoryIterator = new RecursiveDirectoryIterator($baseDir);
    $iterator = new RecursiveIteratorIterator($directoryIterator);
    $phpFiles = new RegexIterator($iterator, '/\.php$/');

    $ignoreClasses = [
        "App\\Core\\Helper\\Util"
    ];

    foreach ($phpFiles as $file) {
        $filePath = $file->getRealPath();
        $relativePath = str_replace([$baseDir, '/', '.php'], ['', '\\', ''], $filePath);
        $className = $namespacePrefix . $relativePath;

        if (in_array($className, $ignoreClasses)) {
            continue;
        }

        try {
            $reflectionClass = new ReflectionClass($className);

            if ($reflectionClass->isInterface() || $reflectionClass->isAbstract()) {
                continue;
            }

            $interfaces = $reflectionClass->getInterfaces();
            foreach ($interfaces as $interface) {
                $container->bind($interface->getName(), fn($container) => $container->get($className));
            }

            $container->bind($className, function ($container) use ($className, $reflectionClass) {
                $constructor = $reflectionClass->getConstructor();

                if ($constructor === null) {
                    return new $className();
                }

                $parameters = [];
                foreach ($constructor->getParameters() as $parameter) {
                    $dependency = $parameter->getType();
                    if ($dependency === null || $dependency->isBuiltin()) {
                        if ($parameter->isOptional()) {
                            $parameters[] = $parameter->getDefaultValue();
                        } else {
                            throw new Exception("Cannot resolve dependency for {$parameter->getName()} in $className.");
                        }
                    } elseif ($dependency instanceof ReflectionNamedType) {
                        $parameters[] = $container->get($dependency->getName());
                    } else {
                        throw new Exception("Unsupported parameter type for {$parameter->getName()} in $className.");
                    }
                }

                return $reflectionClass->newInstanceArgs($parameters);
            });
        } catch (ReflectionException $e) {
            error_log("Error loading class $className: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error resolving dependencies for $className: " . $e->getMessage());
        }
    }
}

$baseDir = dirname(__DIR__) . '/src';
registerClassesRecursively($container, $baseDir, 'App');

return $container;
