<?php

namespace App\Repository;

use App\Interfaces\Repository\UserRepositoryInterface;
use App\Interfaces\Model\UserModelInterface;
use App\Model\Entity\UserModel;
use App\Model\ValuableObject\Email;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected string $table = 'users';

    public function insert(UserModelInterface $user): void
    {
        $sql = 'INSERT INTO users (id, email, password) VALUES (?, ?, ?)';

        try {
            $stmt = $this->connection->prepare($sql);
            $bindValues = [
                $user->getID(),
                $user->getEmail(),
                $user->getPasswordHash()
            ];
            $stmt->execute($bindValues);
        } catch (\Throwable $th) {
            throw new \Exception("Failed to insert user: " . $th->getMessage());
        }
    }

    public function findAll(): array
    {
        $results = parent::findAll();
        return array_map(function ($result) {
            return new UserModel(new Email($result['Email']), $result['Password'], $result['ID']);
        }, $results);
    }

    public function findBy(array $criteria): array
    {
        $results = parent::findBy($criteria);
        return array_map(function ($result) {
            return new UserModel(new Email($result['Email']), $result['Password'], $result['ID']);
        }, $results);
    }
}
