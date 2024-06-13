<?php

namespace App\Model\ValuableObject;

class Email
{
    private string $address;

    public function __construct(string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email not valid.");
        }
        $this->address = $address;
    }

    public function __toString(): string
    {
        return $this->address;
    }
}