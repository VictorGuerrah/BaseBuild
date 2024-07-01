<?php

namespace App\Core;

class Environment
{
    private static array $envs = [];

    public static function init(): void
    {
        $envFile = BASE_ROOT . '.env';
        
        if (file_exists($envFile)) {
            $envFile = parse_ini_file($envFile);
            if (!empty($envFile)) {
                self::$envs = array_merge($_ENV, $envFile);
            }
        } else {
            self::$envs = $_ENV;
        }

        // set handler;
    }

    public static function get(string $key, string $defaultValue = null): ?string
    {
        if (isset(self::$envs[$key])) {
            return self::$envs[$key];
        }

        return $defaultValue;
    }
}
