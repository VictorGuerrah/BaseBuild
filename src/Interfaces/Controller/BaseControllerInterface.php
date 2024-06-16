<?php

namespace App\Interfaces\Controller;

interface BaseControllerInterface
{
    public static function view(string $path, array $data = []);

}
