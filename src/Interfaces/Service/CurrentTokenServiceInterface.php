<?php

namespace App\Interfaces\Service;

interface CurrentTokenServiceInterface
{
    public function save(string $userID, string $tokenHash): void;
    public function checkToken(string $userID, string $tokenHash): void;
}