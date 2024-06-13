<?php

namespace App\Model\ValuableObject;

class ID
{
    private string $value;
    private string $type;
    private int $length;

    const TYPE_UUID = 'uuid';
    const TYPE_RANDOM_STRING = 'random_string';
    const TYPE_NUMERIC = 'numeric';

    public function __construct(string $type = self::TYPE_UUID, int $length = 16)
    {
        $this->type = $type;
        $this->length = $length;
        $this->value = $this->generateId();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function generateId()
    {
        switch ($this->type) {
            case self::TYPE_UUID:
                return $this->generateUuid();
            case self::TYPE_RANDOM_STRING:
                return $this->generateRandomString();
            case self::TYPE_NUMERIC:
                return $this->generateNumeric();
            default:
                throw new \InvalidArgumentException("Type not supported: " . $this->type);
        }
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    private function generateRandomString(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $this->length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function generateNumeric(): string
    {
        $numericString = '';
        for ($i = 0; $i < $this->length; $i++) {
            $numericString .= random_int(0, 9);
        }
        return $numericString;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function equals(ID $id): bool
    {
        return $this->value === $id->getValue() && $this->type === $id->getType();
    }
}
