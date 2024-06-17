<?php

namespace App\Interfaces\Repository;

interface BaseRepositoryInterface
{
    public function insert($item): void;
    public function findOne($id): void;
}
