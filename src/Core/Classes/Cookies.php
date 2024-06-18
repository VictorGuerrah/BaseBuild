<?php

namespace App\Core\Classes;

class Cookies
{
    public static function set(string $name, string $value, int $expires): void 
    {
        $options = [
            'expires' => $expires,
            'path' => '/',      
            'domain' => 'localhost',
            'secure' => false,     
            'httponly' => true,    
            'samesite' => 'Strict' 
        ];

        setcookie($name, $value, $options);
    }

    public static function delete(string $name): void 
    {
        $options = [
            'expires' => time() - 3600,
            'path' => '/',      
            'domain' => 'localhost',
            'secure' => false,     
            'httponly' => true,    
            'samesite' => 'Strict' 
        ];
        
        setcookie($name, "", $options);
    }
}