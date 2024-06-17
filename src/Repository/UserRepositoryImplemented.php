<?php

namespace App\Repository;

use App\Core\Database\Connection;
use App\Model\Entity\UserModel;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Model\ValuableObject\Email;

class UserRepositoryImplemented implements UserRepositoryInterface
{
    private array $bindValues = [];

    public function __construct()
    {

    }

    public function insert(UserModel $user): void
    {
        $sql = 'INSERT INTO users (id, email, password) VALUES (?, ?, ?)';

        try {
            $this->bindValues[] = $user->getID();
            $this->bindValues[] = $user->getEmail();
            $this->bindValues[] = $user->getPasswordHash();

            $stmt = Connection::prepare($sql);
            Connection::execute($stmt, $this->bindValues);

        } catch (\Throwable $th) {
            throw new \Exception("Failed to insert user: " . $th->getMessage());
        }
    }

    public function getByEmail(string $email): ?UserModel
    {
        $sql = 'SELECT Email, Password FROM users WHERE Email=?';

        try {
            $this->bindValues[] = $email;

            $stmt = Connection::prepare($sql);
            Connection::execute($stmt, $this->bindValues);
            $result = $stmt->fetch();

            if (!$result) {
                return null;
            }

            $user = new UserModel(new Email($result['Email']), $result['Password']);

            return $user;
        } catch (\Throwable $th) {
            throw new \Exception("Failed to fetch user: " . $th->getMessage());
        }
    }
}
