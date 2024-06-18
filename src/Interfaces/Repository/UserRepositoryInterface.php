<?php

namespace App\Interfaces\Repository;

use App\Interfaces\Model\UserModelInterface;

interface UserRepositoryInterface
{
    public function insert(UserModelInterface $user): void;
    // public function update(User $user): void;
    // public function delete(string $id): void;

    // public function getAll(): array;
    public function findByEmail(string $email): ?UserModelInterface;
    public function findById(string $id): ?UserModelInterface;
}
