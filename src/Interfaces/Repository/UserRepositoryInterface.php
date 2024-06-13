<?php

namespace App\Interfaces\Repository;

use App\Model\Entity\UserModel;
use App\Model\ValuableObject\Email;

interface UserRepositoryInterface
{
    public function insert(UserModel $user): void;
    // public function update(User $user): void;
    // public function delete(string $id): void;

    // public function getAll(): array;
    public function getByEmail(string $email): ?UserModel;
    // public function getByID(ID $id): User;
}
