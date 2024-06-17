<?php

namespace App\Model\ValuableObject;

class Password
{
    private string $value;
    private string $hash;

    public function __construct(string $value)
    {
        // $this->validatePasswordStrength($value);
        $this->value = $value;
        $this->hash = password_hash($value, PASSWORD_DEFAULT);
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function verify(string $hashedPassword): bool
    {
        return password_verify($this->value, $hashedPassword);
    }    

    private function validatePasswordStrength(string $value): void 
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
    }
}
