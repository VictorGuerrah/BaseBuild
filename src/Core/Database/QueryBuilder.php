<?php

namespace App\Core\Database;

class QueryBuilder
{
    private string $table = '';
    private array $columns = ['*'];
    private array $where = [];
    private array $groupBy = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function select(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $validOperators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN', 'NOT IN'];
        if (!in_array($operator, $validOperators)) {
            throw new \InvalidArgumentException("Invalid operator: {$operator}");
        }

        $this->where[] = [$column, $operator, $value];
        return $this;
    }

    public function groupBy(array $columns): self
    {
        $this->groupBy = $columns;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $validDirections = ['ASC', 'DESC'];
        if (!in_array(strtoupper($direction), $validDirections)) {
            throw new \InvalidArgumentException("Invalid direction: {$direction}");
        }

        $this->orderBy[] = [$column, strtoupper($direction)];
        return $this;
    }

    public function limit(int $limit): self
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException("Limit must be non-negative");
        }

        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException("Offset must be non-negative");
        }

        $this->offset = $offset;
        return $this;
    }

    public function reset(): self
    {
        $this->table = '';
        $this->columns = ['*'];
        $this->where = [];
        $this->groupBy = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        return $this;
    }

    public function toSql(): string
    {
        if (empty($this->table)) {
            throw new \LogicException("Table name is required");
        }

        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;

        if ($this->where) {
            $conditions = array_map(fn ($w) => "{$w[0]} {$w[1]} ?", $this->where);
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($this->groupBy) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if ($this->orderBy) {
            $orderConditions = array_map(fn ($o) => "{$o[0]} {$o[1]}", $this->orderBy);
            $sql .= ' ORDER BY ' . implode(', ', $orderConditions);
        }

        if (!is_null($this->limit)) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if (!is_null($this->offset)) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    public function insertOrUpdate(array $attributes): string
    {
        $columns = implode(', ', array_keys($attributes));
        $placeholders = implode(', ', array_fill(0, count($attributes), '?'));

        $updateClause = implode(', ', array_map(fn ($col) => "{$col} = VALUES({$col})", array_keys($attributes)));

        return "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders}) ON DUPLICATE KEY UPDATE {$updateClause}";
    }

    public function insert(array $attributes): string
    {
        $columns = implode(', ', array_keys($attributes));
        $placeholders = implode(', ', array_fill(0, count($attributes), '?'));

        return "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
    }

    public function update(array $data): string
    {

        $setClause = implode(', ', array_map(fn ($col) => "{$col} = ?", array_keys($data)));

        $conditions = array_map(fn ($w) => "{$w[0]} {$w[1]} ?", $this->where);
        $whereClause = implode(' AND ', $conditions);

        return "UPDATE {$this->table} SET {$setClause} WHERE {$whereClause}";
    }

    public function getBindings(): array
    {
        return array_map(fn ($w) => $w[2], $this->where);
    }
}
