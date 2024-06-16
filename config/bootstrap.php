<?php

use App\Core\Classes\Container;
use App\Core\Classes\Autowired;

$container = new Container();

function registerClassesRecursively(Container $container, string $baseDir, string $namespacePrefix): void
{
    if (!is_dir($baseDir)) {
        throw new InvalidArgumentException("Base directory $baseDir does not exist or is not a directory.");
    }

    $directoryIterator = new RecursiveDirectoryIterator($baseDir);
    $iterator = new RecursiveIteratorIterator($directoryIterator);
    $phpFiles = new RegexIterator($iterator, '/\.php$/');

    foreach ($phpFiles as $file) {
        $filePath = $file->getRealPath();
        $relativePath = str_replace([$baseDir, '/', '.php'], ['', '\\', ''], $filePath);
        $className = $namespacePrefix . $relativePath;

        if ($className === "App\\Core\\Helper\\Util") {
            continue;
        }

        try {
            $reflectionClass = new ReflectionClass($className);

            if ($reflectionClass->isInterface() || $reflectionClass->isAbstract()) {
                continue;
            }

            $container->bind($className, fn($container) => Autowired::make($className, $container));

            foreach ($reflectionClass->getInterfaces() as $interface) {
                $container->bind($interface->getName(), fn($container) => $container->get($className));
            }
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
