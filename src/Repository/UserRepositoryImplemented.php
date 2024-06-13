<?php

namespace App\Repository;

use PDO;
use App\Model\Entity\UserModel;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Model\ValuableObject\Email;

class UserRepositoryImplemented implements UserRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function insert(UserModel $user): void
    {
        $sql = 'INSERT INTO users (id, email, password) VALUES (:id, :email, :password)';

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':id', $user->getID());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':password', $user->getPasswordHash());
            $stmt->execute();
        } catch (\Throwable $th) {
            throw new \Exception("Failed to insert user: " . $th->getMessage());
        }
    }

    public function getByEmail(string $email): ?UserModel
    {
        $sql = 'SELECT Email, Password FROM users WHERE Email=:email';

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userData) {
                return null;
            }

            $user = new UserModel(new Email($userData['Email']), $userData['Password']);

            return $user;
        } catch (\Throwable $th) {
            throw new \Exception("Failed to fetch user: " . $th->getMessage());
        }
    }
}
