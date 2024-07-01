<?php

namespace App\Repository;

use App\Core\Database\Connection;
use App\Core\Database\QueryBuilder;
use App\Interfaces\Repository\BaseRepositoryInterface;
use PDO;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected PDO $connection;
    protected string $table;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function findAll(): array
    {
        $query = (new QueryBuilder())
            ->table($this->table)
            ->toSql();

        $stmt = $this->connection->query($query);
        return $stmt->fetchAll();
    }

    public function findBy(array $criteria): array
    {
        $queryBuilder = (new QueryBuilder())->table($this->table);

        foreach ($criteria as $column => $value) {
            $queryBuilder->where($column, '=', $value);
        }

        $sql = $queryBuilder->toSql();
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($queryBuilder->getBindings());

        return $stmt->fetchAll();
    }

    public function findOneBy(array $criteria): mixed
    {
        $results = $this->findBy($criteria);
        return $results[0] ?? null;
    }

    public function findByColumn(string $column, $value): array
    {
        return $this->findBy([$column => $value]);
    }

    public function findOneByColumn(string $column, $value): mixed
    {
        return $this->findOneBy([$column => $value]);
    }
}
