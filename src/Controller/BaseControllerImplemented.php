<?php

namespace App\Controller;

use App\Interfaces\Controller\BaseControllerInterface;
use App\Core\Classes\HTTP;
use App\Core\Classes\View;

class BaseControllerImplemented implements BaseControllerInterface
{
    public static function view(string $path, array $data = []): ?string
    {
        return View::load($path, $data);
    }
    public static function responseJson(string|array $body, int $status = 200): string
    {
        return HTTP::sendResponseJson($body, $status);
    }
}