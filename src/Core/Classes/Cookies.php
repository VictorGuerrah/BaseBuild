<?php

namespace App\Core\Classes;

class Cookies
{
    public static function set($name, $value, $expires): void 
    {
        setcookie($name, $value, $expires, '/', 'localhost', false, true);
    }
}