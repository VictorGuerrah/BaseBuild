<?php

namespace App\Repository;

use App\Core\Database\Connection;
use App\Interfaces\Model\UserModelInterface;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Model\Entity\UserModel;
use App\Model\ValuableObject\Email;

class UserRepository implements UserRepositoryInterface
{
    public function __construct() { }

    public function insert(UserModelInterface $user): void
    {
        $sql = 'INSERT INTO users (id, email, password) VALUES (?, ?, ?)';

        try {
            $stmt = Connection::prepare($sql);
            $bindValues = [
                $user->getID(),
                $user->getEmail(),
                $user->getPasswordHash()
            ];
            Connection::execute($stmt, $bindValues);
        } catch (\Throwable $th) {
            throw new \Exception("Failed to insert user: " . $th->getMessage());
        }
    }

    public function findByEmail(string $email): ?UserModel
    {
        $sql = 'SELECT ID, Email, Password FROM users WHERE Email=?';

        try {
            $stmt = Connection::prepare($sql);
            $bindValues = [$email];
            Connection::execute($stmt, $bindValues);
            $result = $stmt->fetch();

            if (!$result) {
                return null;
            }

            $user = new UserModel(new Email($result['Email']), $result['Password'], $result['ID']);

            return $user;
        } catch (\Throwable $th) {
            throw new \Exception("Failed to fetch user: " . $th->getMessage());
        }
    }

    public function findById(string $id): ?UserModel
    {
        $sql = 'SELECT ID, Email FROM users WHERE ID=?';

        try {
            $stmt = Connection::prepare($sql);
            $bindValues = [$id];
            Connection::execute($stmt, $bindValues);
            $result = $stmt->fetch();

            if (!$result) {
                return null;
            }

            $user = new UserModel(new Email($result['Email']), $result['ID']);

            return $user;
        } catch (\Throwable $th) {
            throw new \Exception("Failed to fetch user: " . $th->getMessage());
        }
    }
}
