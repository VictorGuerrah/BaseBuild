<?php

namespace App\Interfaces\Repository;

interface BaseRepositoryInterface
{
    public function findAll(): array;
    public function findBy(array $criteria): array;
    public function findOneBy(array $criteria): mixed;
    public function findByColumn(string $column, $value): array;
    public function findOneByColumn(string $column, $value): mixed;
}
