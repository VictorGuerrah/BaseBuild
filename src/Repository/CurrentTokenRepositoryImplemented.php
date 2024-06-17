<?php

namespace App\Repository;

use App\Core\Database\Connection;
use App\Interfaces\Model\CurrentTokenModelInterface;
use App\Interfaces\Repository\CurrentTokenRepositoryInterface;
use App\Model\Entity\CurrentTokenModel;

class CurrentTokenRepositoryImplemented implements CurrentTokenRepositoryInterface
{
    public function __construct() { }

    public function insert(CurrentTokenModelInterface $currentToken): void
    {
        $sql = 'INSERT INTO current_token (UserID, TokenHash, IsValid) VALUES (?, ?, ?)';

        try {
            $stmt = Connection::prepare($sql);
            $bindValues = [
                $currentToken->getUserId(),
                $currentToken->getTokenHash(),
                $currentToken->isValid()
            ];
            Connection::execute($stmt, $bindValues);
        } catch (\Throwable $th) {
            throw new \Exception("Failed to insert token: " . $th->getMessage());
        }
    }

    public function update(CurrentTokenModelInterface $currentToken): void
    {
        $sql = 'UPDATE current_token SET TokenHash = ?, IsValid = ? WHERE UserID = ?';

        try {
            $stmt = Connection::prepare($sql);
            $bindValues = [
                $currentToken->getTokenHash(),
                $currentToken->isValid(),
                $currentToken->getUserId()
            ];
            Connection::execute($stmt, $bindValues);
        } catch (\Throwable $th) {
            throw new \Exception("Failed to update token: " . $th->getMessage());
        }
    }

    public function findOne(string $userId): ?CurrentTokenModel
    {
        $sql = 'SELECT UserID, TokenHash, IsValid FROM current_token WHERE UserID=?';

        try {
            $stmt = Connection::prepare($sql);
            $bindValues = [$userId];
            Connection::execute($stmt, $bindValues);
            $result = $stmt->fetch();

            if (!$result) {
                return null;
            }

            return new CurrentTokenModel($result['UserID'], $result['TokenHash'], $result['IsValid']);
        } catch (\Throwable $th) {
            throw new \Exception("Failed to fetch token: " . $th->getMessage());
        }
    }
}
