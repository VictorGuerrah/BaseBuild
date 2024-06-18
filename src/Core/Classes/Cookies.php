<?php

namespace App\Core\Classes;

class Cookies
{
    public static function set(string $name, string $value, int $expires): void 
    {
        setcookie($name, $value, $expires, '/', 'localhost', false, true);
    }
}