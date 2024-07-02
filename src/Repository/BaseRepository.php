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

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function findAll(): array
    {
        try {
            $query = (new QueryBuilder())
                ->table($this->table)
                ->toSql();

            $stmt = $this->connection->query($query);
            return $this->mapResults($stmt->fetchAll());
        } catch (PDOException $e) {
            throw new \Exception("Failed to fetch all records: " . $e->getMessage());
        }
    }

    public function findBy(array $criteria): array
    {
        try {
            $queryBuilder = (new QueryBuilder())->table($this->table);

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
            $columns = implode(', ', array_keys($attributes));
            $placeholders = implode(', ', array_fill(0, count($attributes), '?'));
            $updateClause = implode(', ', array_map(fn ($col) => "{$col} = VALUES({$col})", array_keys($attributes)));

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders}) ON DUPLICATE KEY UPDATE {$updateClause}";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_values($attributes));
        } catch (PDOException $e) {
            throw new \Exception("Failed to save the model: " . $e->getMessage());
        }
    }


    protected function insert(object $model): void
    {
        try {
            $attributes = $model->getAttributes();
            $columns = implode(', ', array_keys($attributes));
            $placeholders = implode(', ', array_fill(0, count($attributes), '?'));

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_values($attributes));
        } catch (PDOException $e) {
            throw new \Exception("Failed to insert the model: " . $e->getMessage());
        }
    }

    protected function update(object $model): void
    {
        try {
            $attributes = $model->getAttributes();
            $id = $model->getId();

            $setClause = implode(', ', array_map(fn ($col) => "{$col} = ?", array_keys($attributes)));

            $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([...array_values($attributes), $id]);
        } catch (PDOException $e) {
            throw new \Exception("Failed to update the model: " . $e->getMessage());
        }
    }

    protected function mapResults(array $results): array
    {
        return $results;
    }
}
