<?php

namespace App\Service;

use App\Interfaces\Service\ViewServiceInterface;

class ViewServiceImplemented implements ViewServiceInterface
{
    public static function load(string $path): void
    {
        $fullPath = dirname(__FILE__, 3) . '/views/' . $path . '.php';
        if (!file_exists($fullPath)) {
            throw new \Exception("Unkown path: " . $path);
            }
        include_once $fullPath;
    }
}