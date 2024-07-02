<?php

namespace App\Interfaces\Repository;

interface BaseRepositoryInterface
{
    public function save(object $model): void;
    public function findAll(array $options = []): array;
    public function findBy(array $criteria, array $options = []): array;
    public function findOneBy(array $criteria): mixed;
    public function findByColumn(string $column, $value, array $options = []): array;
    public function findOneByColumn(string $column, $value): mixed;
}
