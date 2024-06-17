<?php

namespace App\Interfaces\Repository;

use App\Interfaces\Model\CurrentTokenModelInterface;
use App\Model\Entity\CurrentTokenModel;

interface CurrentTokenRepositoryInterface
{
    public function insert(CurrentTokenModelInterface $currentToken): void;
    public function update(CurrentTokenModelInterface $currentToken): void;
    public function findOne(string $userId): ?CurrentTokenModel;
}
