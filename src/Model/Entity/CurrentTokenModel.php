<?php

namespace App\Model\Entity;

use App\Interfaces\Model\CurrentTokenModelInterface;

class CurrentTokenModel implements CurrentTokenModelInterface
{
    private string $userId;
    private string $hash;
    private bool $isValid;

    public function __construct(string $userId, string $tokenHash, bool $isValid)
    {
        $this->setUserId($userId);
        $this->setHash($tokenHash);
        $this->setIsValid($isValid);
    }

    public function getUserId(): string
    {
        return $this->userId;
    }


    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $tokenHash): void
    {
        $this->hash = $tokenHash;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): void
    {
        $this->isValid = $isValid;
    }
}
