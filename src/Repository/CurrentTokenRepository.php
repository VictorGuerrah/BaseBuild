<?php

namespace App\Repository;

use App\Core\Database\Connection;
use App\Core\Database\QueryBuilder;
use App\Model\Entity\CurrentTokenModel;

class CurrentTokenRepository extends BaseRepository
{
    protected string $table = 'current_token';

    public function __construct(Connection $connection, QueryBuilder $queryBuilder)
    {
        $this->connection = $connection->getInstance();
        $this->queryBuilder = $queryBuilder;
    }

    public function mapResults(array $results): array
    {
        return array_map(function ($result) {
            return new CurrentTokenModel($result['UserID'], $result['TokenHash'], $result['IsValid']);
        }, $results);
    }
}
