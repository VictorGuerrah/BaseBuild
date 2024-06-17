<?php

namespace App\Interfaces\Service;

interface AuthServiceInterface
{
    public function validateCredentials(string $email, string $password): ?string;
    public function setCookies(string $userID): void;
}
