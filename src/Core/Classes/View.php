<?php

namespace App\Core\Classes;

class View
{
    protected $cacheData;
    protected $objectLevel;

    public static function render(string $view, array $data = []): ?string
    {
        $fileView = static::getFile($view);

        if (!file_exists($fileView)) {
            throw new \Exception("File not found: $fileView.");
        }

        extract($data, EXTR_OVERWRITE);
        ob_start();
        include($fileView);

        return ob_get_clean();
    }

    public static function getFile(string $view): string
    {
        $view = strtolower($view);

        if (is_file("$view.php")) {
            return "$view.php";
        }

        if (defined('BASE_VIEW') && is_file(BASE_VIEW . "$view.php")) {
            return BASE_VIEW . "$view.php";
        }

        throw new \Exception("View file not found: " . BASE_VIEW . "$view.php");
    }
}
