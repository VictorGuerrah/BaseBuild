<?php

namespace App\Model\Entity;

use App\Model\ValuableObject\ID;
use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;

class UserModel
{
    public ID $id;
    public Email $email;
    private string $passwordHash;

    public function __construct(Email $email, string $passwordHash)
    {
        $this->id = new ID();
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
