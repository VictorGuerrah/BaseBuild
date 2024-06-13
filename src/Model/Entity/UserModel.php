<?php

namespace App\Model\Entity;

use App\Model\ValuableObject\ID;
use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;

class UserModel
{
    public ID $id;
    public Email $email;
    public Password $passwordHash;

    public function __construct(Email $email, string $password)
    {
        $this->id = new ID();
        $this->email = $email;
        $this->passwordHash = new Password($password);
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
        return (string) $this->passwordHash;
    }
}
