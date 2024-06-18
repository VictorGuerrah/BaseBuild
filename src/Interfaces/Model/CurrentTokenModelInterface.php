<?php

namespace App\Interfaces\Model;

interface CurrentTokenModelInterface
{

    public function getUserId(): string;
    public function setUserId(string $userId): void;
    public function getHash(): string;
    public function setHash(string $tokenHash): void;
    public function isValid(): bool;
    public function setIsValid(bool $isValid): void;
}
