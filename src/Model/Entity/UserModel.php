<?php

namespace App\Model\Entity;

use App\Interfaces\Model\UserModelInterface;
use App\Model\ValuableObject\ID;
use App\Model\ValuableObject\Email;

class UserModel implements UserModelInterface
{
    private string $id;
    private Email $email;
    private string $passwordHash;

    public function __construct(Email $email, string $passwordHash)
    {
        $this->id = (new ID())->getValue();
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    public function getID(): string
    {
        return (string) $this->id;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}
