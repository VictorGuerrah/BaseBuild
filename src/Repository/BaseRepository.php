<?php

namespace App\Repository;

use App\Core\Database\Connection;
use App\Core\Database\QueryBuilder;
use App\Interfaces\Repository\BaseRepositoryInterface;
use PDO;
use PDOException;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected PDO $connection;
    protected string $table;
    protected QueryBuilder $queryBuilder;

    public function __construct(Connection $connection, QueryBuilder $queryBuilder, string $table)
    {
        $this->connection = $connection->getInstance();
        $this->queryBuilder = $queryBuilder;
        $this->table = $table;
    }

    public function findAll(): array
    {
        try {
            $sql = $this->queryBuilder
                ->table($this->table)
                ->toSql();

            $stmt = $this->connection->query($sql);
            return $this->mapResults($stmt->fetchAll());
        } catch (PDOException $e) {
            throw new \Exception("Failed to fetch all records: " . $e->getMessage());
        }
    }

    public function findBy(array $criteria): array
    {
        try {
            $queryBuilder = $this->queryBuilder->table($this->table);

            foreach ($criteria as $column => $value) {
                $queryBuilder->where($column, '=', $value);
            }

            $sql = $queryBuilder->toSql();
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($queryBuilder->getBindings());

            return $this->mapResults($stmt->fetchAll());
        } catch (PDOException $e) {
            throw new \Exception("Failed to fetch records by criteria: " . $e->getMessage());
        }
    }

    public function findOneBy(array $criteria): ?object
    {
        $results = $this->findBy($criteria);
        return $results[0] ?? null;
    }

    public function findByColumn(string $column, $value): array
    {
        return $this->findBy([$column => $value]);
    }

    public function findOneByColumn(string $column, $value): ?object
    {
        return $this->findOneBy([$column => $value]);
    }

    public function save(object $model): void
    {
        try {
            $attributes = $model->getAttributes();
            $sql = $this->queryBuilder
                ->table($this->table)
                ->insertOrUpdate($attributes);
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_values($attributes));
        } catch (PDOException $e) {
            throw new \Exception("Failed to save the model: " . $e->getMessage());
        }
    }

    protected function mapResults(array $results): array
    {
        return $results;
    }
}
