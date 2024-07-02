<?php

namespace App\Repository;

use App\Core\Database\Connection;
use App\Core\Database\QueryBuilder;
use App\Model\Entity\UserModel;
use App\Model\ValuableObject\Email;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function __construct(Connection $connection, QueryBuilder $queryBuilder)
    {
        $this->connection = $connection->getInstance();
        $this->queryBuilder = $queryBuilder;
    }

    protected function mapResults(array $results): array
    {
        return array_map(function ($result) {
            return new UserModel(new Email($result['Email']), $result['Password'], $result['ID']);
        }, $results);
    }
}
