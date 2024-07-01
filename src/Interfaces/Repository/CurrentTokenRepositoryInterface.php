<?php

namespace App\Interfaces\Repository;

use App\Interfaces\Model\CurrentTokenModelInterface;

interface CurrentTokenRepositoryInterface extends BaseRepositoryInterface
{
    public function insert(CurrentTokenModelInterface $currentToken): void;
    public function update(CurrentTokenModelInterface $currentToken): void;
}
