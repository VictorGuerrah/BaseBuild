<?php

namespace App\Interfaces\Service;

interface ViewServiceInterface
{
    public static function load(string $path): void;
}