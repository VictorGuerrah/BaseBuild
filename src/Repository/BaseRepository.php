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

    public function findAll(array $options = []): array
    {
        try {
            $query = $this->queryBuilder->table($this->table);
            $this->applyOptions($query, $options);
            $sql = $query->toSql();

            $stmt = $this->connection->query($sql);
            return $this->mapResults($stmt->fetchAll());
        } catch (PDOException $e) {
            throw new \Exception("Failed to fetch all records: " . $e->getMessage());
        } finally {
            $this->queryBuilder->reset();
        }
    }

    public function findBy(array $criteria, array $options = []): array
    {
        try {
            $query = $this->queryBuilder->table($this->table);

            foreach ($criteria as $column => $value) {
                $query->where($column, '=', $value);
            }

            $this->applyOptions($query, $options);

            $sql = $query->toSql();
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($query->getBindings());

            return $this->mapResults($stmt->fetchAll());
        } catch (PDOException $e) {
            throw new \Exception("Failed to fetch records by criteria: " . $e->getMessage());
        } finally {
            $this->queryBuilder->reset();
        }
    }

    public function findOneBy(array $criteria): ?object
    {
        $results = $this->findBy($criteria);
        return $results[0] ?? null;
    }

    public function findByColumn(string $column, $value, array $options = []): array
    {
        return $this->findBy([$column => $value], $options);
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
        } finally {
            $this->queryBuilder->reset();
        }
    }

    public function insert(object $model): void
    {
        try {
            $attributes = $model->getAttributes();
            $sql = $this->queryBuilder
                ->table($this->table)
                ->insert($attributes);
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_values($attributes));
        } catch (PDOException $e) {
            throw new \Exception("Failed to insert record: " . $e->getMessage());
        } finally {
            $this->queryBuilder->reset();
        }
    }

    public function update(array $data, array $criteria): void
    {
        try {
            $queryBuilder = $this->queryBuilder->table($this->table);

            foreach ($criteria as $column => $value) {
                $queryBuilder->where($column, '=', $value);
            }

            $bindings = array_merge(array_values($data), $queryBuilder->getBindings());

            $sql = $queryBuilder->update($data);
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($bindings);
        } catch (PDOException $e) {
            throw new \Exception("Failed to update record: " . $e->getMessage());
        } finally {
            $this->queryBuilder->reset();
        }
    }

    protected function applyOptions(QueryBuilder $queryBuilder, array $options): void
    {
        if (empty($options)) {
           return;
        }

        if (isset($options['groupBy'])) {
            $queryBuilder->groupBy($options['groupBy']);
        }

        if (isset($options['orderBy'])) {
            foreach ($options['orderBy'] as $order) {
                $queryBuilder->orderBy($order[0], $order[1] ?? 'ASC');
            }
        }

        if (isset($options['limit'])) {
            $queryBuilder->limit($options['limit']);
        }

        if (isset($options['offset'])) {
            $queryBuilder->offset($options['offset']);
        }
    }

    protected function mapResults(array $results): array
    {
        return $results;
    }
}
