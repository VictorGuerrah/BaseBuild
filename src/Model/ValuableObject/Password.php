<?php

namespace App\Model\ValuableObject;

class Password
{
    public readonly string $passwordHash;

    public function __construct(string $value)
    {
        $this->passwordHash = password_hash($value, PASSWORD_DEFAULT);
    }

    public function __toString(): string
    {
        return $this->passwordHash;
    }

    public function verify($valueToVerify): bool
    {
        return password_verify($this->passwordHash, $valueToVerify);
    }

    private function validatePasswordStrength(string $value): bool 
    {
        if (strlen($value) < 8) {
            throw new \Exception("Password must be at least 8 characters long.");
        }
    
        if (!preg_match('/[A-Z]/', $value)) {
            throw new \Exception("Password must contain at least one uppercase letter.");
        }
    
        if (!preg_match('/[a-z]/', $value)) {
            throw new \Exception("Password must contain at least one lowercase letter.");
        }
    
        if (!preg_match('/[0-9]/', $value)) {
            throw new \Exception("Password must contain at least one digit.");
        }
    
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $value)) {
            throw new \Exception("Password must contain at least one special character.");
        }
    
        return true;
    }
}
