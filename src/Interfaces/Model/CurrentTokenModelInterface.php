<?php

namespace App\Interfaces\Model;

interface CurrentTokenModelInterface
{

    public function getUserId(): string;
    public function setUserId(string $userId): void;
    public function getTokenHash(): string;
    public function setTokenHash(string $tokenHash): void;
    public function isValid(): bool;
    public function setIsValid(bool $isValid): void;
}
