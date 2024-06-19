<?php

class RouteServiceProvider 
{
    public function map($directory)
    {
        if (!is_dir($directory)) {
            throw new \Exception("Invalid directory: $directory", 404);
        }

        $files = scandir($directory);

        foreach ($files as $value) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $value);
            if ($path === false) {
                continue;
            }

            if (!is_dir($path)) {
                include_once $path;
            } elseif ($value !== "." && $value !== "..") {
                $this->map($path);
            }
        }
    }
}

(new RouteServiceProvider())->map(BASE_ROUTER);