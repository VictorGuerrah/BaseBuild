<?php

namespace App\Core\Database;

class QueryBuilder
{
    private string $table;
    private array $columns = ['*'];
    private array $where = [];
    private array $groupBy = [];
    private array $orderBy = [];

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
        $this->orderBy[] = [$column, $direction];
        return $this;
    }

    public function toSql(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;

        if ($this->where) {
            $conditions = array_map(fn($w) => "{$w[0]} {$w[1]} ?", $this->where);
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($this->groupBy) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if ($this->orderBy) {
            $orderConditions = array_map(fn($o) => "{$o[0]} {$o[1]}", $this->orderBy);
            $sql .= ' ORDER BY ' . implode(', ', $orderConditions);
        }

        return $sql;
    }

    public function getBindings(): array
    {
        return array_map(fn($w) => $w[2], $this->where);
    }
}
