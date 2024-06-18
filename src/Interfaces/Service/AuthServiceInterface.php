<?php

namespace App\Interfaces\Service;

use App\Model\Entity\UserModel;

interface AuthServiceInterface
{
    public function validateCredentials(string $email, string $password): ?string;
    public function setCookies(string $userID): void;
    public function getCookies(): array;
    public function getLoggedUser(): ?UserModel;
    public function setLoggedUser(UserModel $user): void;
    public function logout(): void;
}
