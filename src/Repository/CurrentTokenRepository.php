<?php

namespace App\Repository;

use App\Interfaces\Model\CurrentTokenModelInterface;
use App\Interfaces\Repository\CurrentTokenRepositoryInterface;
use App\Model\Entity\CurrentTokenModel;

class CurrentTokenRepository extends BaseRepository implements CurrentTokenRepositoryInterface
{
    protected string $table = 'current_token';

    public function insert(CurrentTokenModelInterface $currentToken): void
    {
        $sql = 'INSERT INTO current_token (UserID, TokenHash, IsValid) VALUES (?, ?, ?)';

        try {
            $stmt = $this->connection->prepare($sql);
            $bindValues = [
                $currentToken->getUserId(),
                $currentToken->getHash(),
                $currentToken->isValid()
            ];
            $stmt->execute($bindValues);
        } catch (\Throwable $th) {
            throw new \Exception("Failed to insert token: " . $th->getMessage());
        }
    }

    public function update(CurrentTokenModelInterface $currentToken): void
    {
        $sql = 'UPDATE current_token SET TokenHash = ?, IsValid = ? WHERE UserID = ?';

        try {
            $stmt = $this->connection->prepare($sql);
            $bindValues = [
                $currentToken->getHash(),
                $currentToken->isValid(),
                $currentToken->getUserId()
            ];
            $stmt->execute($bindValues);
        } catch (\Throwable $th) {
            throw new \Exception("Failed to update token: " . $th->getMessage());
        }
    }

    public function findAll(): array
    {
        $results = parent::findAll();
        return array_map(function ($result) {
            return new CurrentTokenModel($result['UserID'], $result['TokenHash'], $result['IsValid']);
        }, $results);
    }

    public function findBy(array $criteria): array
    {
        $results = parent::findBy($criteria);
        return array_map(function ($result) {
            return new CurrentTokenModel($result['UserID'], $result['TokenHash'], $result['IsValid']);
        }, $results);
    }
}
