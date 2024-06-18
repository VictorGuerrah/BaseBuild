<?php

namespace App\Core\Classes;

class Cookies
{
    public static function set(string $name, string $value, int $expires): void 
    {
        setcookie($name, $value, $expires, '/', 'localhost', false, true);
    }

    public static function delete(string $name): void 
    {
        setcookie($name, "", -1, '/', 'localhost', false, true);
    }
}