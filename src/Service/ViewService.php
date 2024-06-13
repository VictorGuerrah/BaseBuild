<?php

namespace App\Service;

class ViewService
{
    public static function load(string $path)
    {
        $fullPath = dirname(__FILE__, 3) . '/views/' . $path . '.php';
        if (!file_exists($fullPath)) {
            throw new \Exception("Unkown path: " . $path);
            }
        include_once $fullPath;
    }
}