<?php

namespace App\Service;

use App\Interfaces\Service\CurrentTokenServiceInterface;

class CurrentTokenServiceImplemented implements CurrentTokenServiceInterface
{
    public function __construct()
    {
        
    }

    public function save(string $userID, string $tokenHash): void
    {
        
    }
}