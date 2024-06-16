<?php

namespace App\Controller;

use App\Interfaces\Controller\BaseControllerInterface;
use App\Core\Classes\View;

class BaseControllerImplemented implements BaseControllerInterface
{
    public static function view(string $path, array $data = []): ?string
    {
        return View::load($path, $data);
    }
}